<?php
namespace Tests\Feature;
use App\Domain\AccessControl\Models\Permission; use App\Domain\AccessControl\Models\Role; use App\Domain\AdminUsers\Models\AdminUser; use Illuminate\Foundation\Testing\RefreshDatabase; use Illuminate\Support\Facades\Hash; use Tests\TestCase;
class WebPhase4Test extends TestCase { use RefreshDatabase;
 public function test_public_api_and_form_submission(): void { $this->assertGuest(); $this->getJson('/api/v1/web/pages')->assertOk(); $this->postJson('/api/v1/web/forms/contact',['name'=>'Ana','email'=>'ana@example.com','message'=>'Hola','consent'=>'1','website'=>''])->assertStatus(202); }
 public function test_login_and_permissioned_admin_api(): void { $p=Permission::create(['name'=>'web.admin','label'=>'Admin']); $r=Role::create(['name'=>'superadmin','label'=>'Super']); $r->permissions()->attach($p); $u=AdminUser::create(['name'=>'Admin','email'=>'admin@example.com','password'=>Hash::make('ChangeMe_123456!')]); $u->roles()->attach($r); $response=$this->postJson('/api/v1/web/auth/login',['email'=>$u->email,'password'=>'ChangeMe_123456!'])->assertOk(); $token=$response->json('token'); $this->withHeader('Authorization','Bearer '.$token)->getJson('/api/v1/web/admin/dashboard')->assertOk(); }
}
