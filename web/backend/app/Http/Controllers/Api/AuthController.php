<?php
namespace App\Http\Controllers\Api;

use App\Domain\AdminUsers\Models\AdminUser;
use App\Support\Auth\Totp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Support\Audit\Audit;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $data=$request->validate(['email'=>'required|email','password'=>'required|string','otp'=>'nullable|string']);
        $user=AdminUser::where('email',$data['email'])->first();
        if (!$user || ($user->locked_until && now()->lt($user->locked_until)) || !Hash::check($data['password'],$user->password)) {
            if($user){$user->increment('failed_login_attempts'); if($user->failed_login_attempts >= (int)config('web.login_max_attempts',5)) $user->update(['locked_until'=>now()->addMinutes((int)config('web.login_lock_minutes',15))]);}
            return response()->json(['message'=>'Credenciales inválidas.'], 422);
        }
        if ($user->two_factor_enabled && !Totp::verify($user->two_factor_secret, (string)($data['otp'] ?? ''))) return response()->json(['message'=>'Código 2FA requerido o inválido.','two_factor_required'=>true], 422);
        $user->update(['failed_login_attempts'=>0,'locked_until'=>null]); $user->tokens()->delete();
        $abilities=$user->roles()->with('permissions')->get()->flatMap(fn($r)=>$r->permissions->pluck('name'))->unique()->values()->all(); $abilities[]='audience:web';
        $token=$user->createToken('web-admin',$abilities,now()->addSeconds((int)config('web.admin_token_ttl',900)));
        Audit::write('identity','login',$user); return response()->json(['token'=>$token->plainTextToken,'expires_at'=>$token->accessToken->expires_at,'user'=>$user->load('roles.permissions')]);
    }
    public function logout(Request $request){Audit::write('identity','logout',$request->user());$request->user()->currentAccessToken()?->delete(); return response()->json(['message'=>'Sesión cerrada.']);}
    public function me(Request $request){return response()->json($request->user()->load('roles.permissions'));}
    public function changePassword(Request $request){$d=$request->validate(['current_password'=>'required','password'=>'required|string|min:12|confirmed']); abort_unless(Hash::check($d['current_password'],$request->user()->password),422,'Contraseña actual inválida.'); $request->user()->update(['password'=>$d['password']]); $request->user()->tokens()->delete(); return response()->json(['message'=>'Contraseña actualizada.']);}
    public function forgot(Request $request){$d=$request->validate(['email'=>'required|email']); Password::broker('admin_users')->sendResetLink($d); return response()->json(['message'=>'Si la cuenta existe, recibirás instrucciones.']);}
    public function reset(Request $request){$d=$request->validate(['token'=>'required','email'=>'required|email','password'=>'required|min:12|confirmed']); $status=Password::broker('admin_users')->reset($d,fn($u)=>$u->forceFill(['password'=>$d['password'],'remember_token'=>Str::random(60)])->save()); return response()->json(['message'=>__($status)], $status===Password::PASSWORD_RESET?200:422);}
    public function twoFactorSetup(Request $request){$secret=Totp::secret(); $request->user()->update(['two_factor_secret'=>$secret,'two_factor_enabled'=>false]); return response()->json(['secret'=>$secret,'otpauth_uri'=>Totp::uri($secret,$request->user()->email)]);}
    public function twoFactorConfirm(Request $request){$d=$request->validate(['code'=>'required|digits:6']); abort_unless(Totp::verify($request->user()->two_factor_secret,$d['code']),422,'Código inválido.'); $request->user()->update(['two_factor_enabled'=>true]); return response()->json(['message'=>'2FA habilitado.']);}
    public function twoFactorDisable(Request $request){$d=$request->validate(['password'=>'required','code'=>'required|digits:6']); abort_unless(Hash::check($d['password'],$request->user()->password)&&Totp::verify($request->user()->two_factor_secret,$d['code']),422,'Validación inválida.'); $request->user()->update(['two_factor_enabled'=>false,'two_factor_secret'=>null]); return response()->json(['message'=>'2FA deshabilitado.']);}
}
