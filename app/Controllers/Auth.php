<?php

namespace App\Controllers;

class Auth extends BaseController
{
    public function login()
    {
        return view('admin/login'); // tampilin form login
    }

    public function attemptLogin()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        // contoh validasi dummy
        if ($username === 'admin' && $password === '1234') {
            return redirect()->to('/ticket'); // sukses login
        } else {
            return redirect()->back()->with('error', 'Username atau Password salah');
        }
    }
}
