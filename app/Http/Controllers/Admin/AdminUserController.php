<?php

namespace App\Http\Controllers\Admin;

use App\Domain\AdminUsers\Models\AdminUser;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(): View
    {
        return view('admin.admin-users.index', [
            'adminUsers' => AdminUser::query()->latest()->get(),
        ]);
    }
}
