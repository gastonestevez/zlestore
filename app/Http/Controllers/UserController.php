<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

  public function directory()
  {
    $users = User::all();
    $vac = compact('users');

    return view('/users', $vac);
  }

  public function store(Request $request)
  {

    $reglas = [
      'name' =>'required|string|min:2|max:40|',
      'email' => 'required|string|email|max:255|unique:users,email,'.Auth::user()->id.',id', // https://laravel.com/docs/5.2/validation#rule-unique , https://laracasts.com/discuss/channels/laravel/how-to-update-unique-email
      'password' => 'min:6|confirmed',
    ];

    $mensajes = [
    "required" => "El campo es obligatorio",
    "string" => "El campo debe ser un texto",
    "min" => "El minimo es de :min caracteres",
    "max" => "El maximo es de :max caracteres",

    ];

    $this->validate($request, $reglas,$mensajes);

    $user = New User();
    $user->name = $request->name;
    $user->email = $request->email;
    $user->password = Hash::make($request['password']);
    $user->role = $request->role;

    $user->save();

    return redirect()->back()
        ->with('status', 'Usuario creado exitosamente');

  }

}
