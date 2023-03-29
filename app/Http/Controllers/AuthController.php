<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Session;

class AuthController extends Controller
{
    function register(Request $request){
        $request->validate([
            'name' => 'required',
            'username' => 'required|min:6',
            'email' => 'required',
            'password' => 'required|min:6|required_with:passwordConfirm|same:passwordConfirm',
            'passwordConfirm' => 'required|min:6',
        ]);

        $name = $request->name;
        $username = $request->username;
        $email = $request->email;
        $password = $request->password;

        $hashed = bcrypt($password);

        try{
            $existUsername = User::where('username', $username)->first();
            $existEmail = User::where('email', $email)->first();
            if($existUsername){
                return redirect()->back()->withErrors("Username already exist");
            }
            if($existEmail){
                return redirect()->back()->withErrors("Email already exists");
            }
            if(!$existEmail && !$existUsername){
                $user = new User();
                $user->name = $name;
                $user->username = $username;
                $user->email = $email;
                $user->password = $hashed;
                $user->save();
                return redirect('login')->with('status',"Insert successfully");
            }
        }
        catch(Exception $e){
            return redirect()->back()->withErrors("Login failed");
        }
    }

    function login(Request $request){
        $request->validate([
            'username' => 'required|min:6',
            'password' => 'required|min:6|',
        ]);
        $username = $request->username;
        $password = $request->password;
        try{
            $existUser = User::where('username', $username)->first();
            if(!$existUser){
                return redirect()->back()->withErrors("Username is not correct");
            }else{
                if (Hash::check($password, $existUser->password))
                {
                    $name = $existUser->name;
                    $email = $existUser->email;
                    return view('baohome',compact('name','email'));
                }else{
                    return redirect()->back()->withErrors("Password is not correct");
                }
            }
        }
        catch(Exception $e){
            return redirect()->back()->withErrors("Login failed");
        }
    }
}
