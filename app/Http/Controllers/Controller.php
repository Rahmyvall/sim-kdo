<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

abstract class Controller
{
    /**
     * Ambil user yang sedang login.
     *
     * Dipakai sebagai pengganti helper auth()->user() agar tipe datanya
     * jelas (App\Models\User) dan terbaca oleh analisis statis editor.
     */
    protected function currentUser(): User
    {
        /** @var User $user */
        $user = Auth::user();

        return $user;
    }
}
