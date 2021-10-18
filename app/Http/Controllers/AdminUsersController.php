<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\User;

class AdminUsersController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $this->authorize('view-admin-merchants');
        $users = User::where('role', 'admin');

        return view('admin_users', [
            'users' => $users->get(),
            'total_count' => $users->count()
        ]);
    }
    
    public function profile()
    {
        $this->authorize('view-admin-merchants');
        $user = User::where('id', Auth::user()->id)->first();
        return view('admin_profile', [
            'user' => $user
        ]);
    }
    
    public function create_user()
    {
        $this->authorize('view-admin-merchants');
        return view('admin_add_user');
    }
}
