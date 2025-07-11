<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisteredUserController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
           'name'                  => ['required','string','max:255'],
           'email'                 => ['required','string','email','max:255','unique:users'],
           'password'              => ['required','string','confirmed','min:8'],
        ]);

        $user = User::create([
           'name'     => $request->name,
           'email'    => $request->email,
           'password' => Hash::make($request->password),
        ]);

        // fire the Registered event so email-verification mail is queued
        event(new Registered($user));

        // issue Sanctum token immediately
        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
           'user'  => $user,
           'token' => $token,
        ], 201);
    }
}

