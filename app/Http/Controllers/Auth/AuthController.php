<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use HttpResponses;
    public function login(LoginUserRequest $request){
        $request->validated($request->all());
        $credentials = $request->only('email','password');
        if(!Auth::attempt($credentials)){
            return $this->error("","Invalid email or password.",401);
        }
        $user = User::where('email', $request->email)->first();
        return $this->success([
            'user' =>$user,
            'token' => $user->createToken('API token of '.$user->name)->plainTextToken,
        ],'Login successfull.',200);
    }

    public function register(StoreUserRequest $request){
        $request->validated($request->all());
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $message = "Registration is successfull.";
        return $this->success([
            'user' => $user,
            'token' => $user->createToken('API token of'.$user->name)->plainTextToken,
        ],$message);
    }

    public function logout() {
        Auth::user()->currentAccessToken()->delete();
        return $this->success([],'You have successfully been logged out and your token has been deleted',200);
    }
}
