<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller; use Illuminate\Http\Request; use Illuminate\Support\Facades\Cache; use Illuminate\Support\Facades\DB;
use App\Domain\Pages\Models\Page; use App\Domain\News\Models\NewsArticle; use App\Domain\Squad\Models\Player; use App\Domain\Squad\Models\StaffMember; use App\Domain\Board\Models\BoardMember; use App\Domain\Sponsors\Models\Sponsor; use App\Domain\Menus\Models\Menu; use App\Domain\Settings\Models\SiteSetting; use App\Domain\Media\Models\Media; use App\Domain\AdminUsers\Models\AdminUser; use App\Domain\Audit\Models\AuditLog; use App\Domain\Forms\Models\FormSubmission;
class WebApiController extends Controller {
 private function pub($key,$cb){return Cache::remember('web:public:'.$key,60,$cb);}
 public function pages(){return $this->pub('pages',fn()=>Page::whereIn('status',['published','scheduled'])->where(fn($q)=>$q->whereNull('published_at')->orWhere('published_at','<=',now()))->with('sections')->orderBy('title')->get());}
 public function page(string $slug){return $this->pub('page:'.$slug,fn()=>Page::where('slug',$slug)->where('status','published')->with('sections')->firstOrFail());}
 public function news(){return $this->pub('news',fn()=>NewsArticle::where('status','published')->where(fn($q)=>$q->whereNull('published_at')->orWhere('published_at','<=',now()))->with('category')->latest('published_at')->paginate(12));}
 public function team(){return $this->pub('team',fn()=>['players'=>Player::query()->orderBy('shirt_number')->get(),'staff'=>StaffMember::query()->orderBy('sort_order')->get()]);}
 public function board(){return $this->pub('board',fn()=>BoardMember::where('is_active',true)->orderBy('sort_order')->get());}
 public function sponsors(){return $this->pub('sponsors',fn()=>Sponsor::where('is_active',true)->orderBy('sort_order')->get());}
 public function menus(string $location='main'){return $this->pub('menu:'.$location,fn()=>Menu::where('location',$location)->with('items')->first());}
 public function settings(){return $this->pub('settings',fn()=>SiteSetting::where('is_public',true)->pluck('value','key'));}
 public function dashboard(){return response()->json(['users'=>AdminUser::count(),'pages'=>Page::count(),'news'=>NewsArticle::count(),'media'=>Media::count(),'forms'=>FormSubmission::where('status','new')->count(),'audit'=>AuditLog::latest()->limit(10)->get()]);}
 public function users(){return AdminUser::with('roles.permissions')->latest()->paginate(25);}
 public function updateUser(Request $r,AdminUser $user){$d=$r->validate(['name'=>'sometimes|string|max:120','email'=>'sometimes|email','locked_until'=>'nullable|date','revoked_at'=>'nullable|date']);$user->update($d);return $user->fresh('roles');}
 public function audit(){return AuditLog::with('adminUser')->latest()->paginate(50);}
 public function media(){return Media::latest()->paginate(30);}
 public function forms(){return FormSubmission::latest()->paginate(30);}
 public function invalidateCache(){Cache::flush();return ['message'=>'Cache Web invalidada.'];}
}
