<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    private $users = [
        [
            'name' => 'Admin Collaber',
            'email' => 'admin@collaber.test',
            'password' => 'admin123',
            'role' => 'admin',
        ],
        [
            'name' => 'Project Owner',
            'email' => 'owner@collaber.test',
            'password' => 'owner123',
            'role' => 'project_owner',
        ],
        [
            'name' => 'Mahasiswa',
            'email' => 'mahasiswa@collaber.test',
            'password' => 'mahasiswa123',
            'role' => 'mahasiswa',
        ],
    ];

    public function showRegister()
    {
        return view('auth.register');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = collect($this->users)->first(function ($user) use ($request) {
            return $user['email'] === $request->email
                && $user['password'] === $request->password;
        });

        if (!$user) {
            return back()->withErrors([
                'email' => 'Email atau password salah.',
            ])->withInput($request->only('email'));
        }

        session([
            'user' => [
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role'],
            ],
        ]);

        if ($user['role'] === 'admin') {
            return redirect('/admin/dashboard');
        }

        if ($user['role'] === 'project_owner') {
            return redirect('/project-owner/dashboard');
        }

        return redirect('/mahasiswa/dashboard');
    }

    public function logout()
    {
        session()->forget('user');

        return redirect('/login');
    }
}