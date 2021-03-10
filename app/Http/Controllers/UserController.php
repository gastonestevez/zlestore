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

    return view('users/users', $vac);
  }

  public function show()
  {
    $user = auth::user();
    $vac = compact('user');

    return view('users/user', $vac);
  }

  public function store(Request $request)
  {

    $reglas = [
      'name' =>'required|string|min:2|max:40|',
      'email' => 'required|string|email|max:255|unique:users,email' ,
      'password' => 'min:6|confirmed',
    ];

    $mensajes = [
    "required" => "El campo es obligatorio",
    "string" => "El campo debe ser un texto",
    "min" => "La clave debe contener al menos :min caracteres",
    "max" => "El valor máximo es de :max caracteres",
    "confirmed" => "Las claves no coinciden"

    ];

    $this->validate($request, $reglas,$mensajes);

    $user = New User();
    $user->name = $request->name;
    $user->email = $request->email;
    $user->password = Hash::make($request['password']);
    $user->role = $request->role;

    $user->save();

    return redirect()->back()
        ->with('success', 'Usuario creado exitosamente');

  }

  public function update(Request $request, int $id)
  {

    $reglas = [
      'name' =>'required|string|min:2|max:40|',
      'email' => 'string|email|max:255|unique:users,email,'.$id.',id', // https://laravel.com/docs/5.2/validation#rule-unique , https://laracasts.com/discuss/channels/laravel/how-to-update-unique-email
      'password' => 'nullable|min:6|confirmed',
    ];

    $mensajes = [
      "required" => "El campo es obligatorio",
      "string" => "El campo debe ser un texto",
      "min" => "La clave debe contener al menos :min caracteres",
      "max" => "El valor máximo es de :max caracteres",
      "confirmed" => "Las claves no coinciden"
    ];

    $this->validate($request, $reglas,$mensajes);

    $user = User::find($id);
    $user->name = $request->name;
    $user->email = $request->email;

    if ($request['password'])
    {
      $user->password = Hash::make($request['new_password']);
    }

    $user->role = $request->role;

    $user->save();

    return redirect()->back()
        ->with('success', 'Usuario editado exitosamente');

  }

  public function destroy(int $id)
  {

    $user = User::find($id);
    $user->delete();

    return redirect('/users')
                    ->with('success', 'Usuario eliminado exitosamente');
  }

}
