<?php

namespace App\Http\Controllers\Admin;

use App\Domain\AccessControl\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(): View
    {
        return view('admin.roles.index', [
            'roles' => Role::query()->with('permissions')->latest()->get(),
        ]);
    }
}
