<?php

namespace App\Http\Controllers;

use Illuminate\Http\Requests;
use App\Models\User;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
//use Auth;
class AuthController extends Controller
{
    // public function createUser(CreateUserRequest $request){
    //     $nameParts = explode(' ', $request->name);
    //     $firstName = $nameParts[0];
    //     $lastName = $nameParts[1]; 
    
    //     $password = bcrypt($firstName . $lastName); 
    //     $userName = strtolower($firstName . $lastName . date('Y'));
    //     $email = strtolower($firstName . '.' . $lastName . '@ejemplo.com');
    
    //     $user = new User([
    //         'name' => $request->name,
    //         'email' => $email,
    //         'password' => $password,
    //         'user_name' => $userName,
    //         'role_id' => 3 
    //     ]);
    
    //     $user->save();
    
    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Successfully created user!',
    //         'token' => $user->createToken("API TOKEN")->plainTextToken
    //     ], 201);
    // }
    

    

    public function loginUser(LoginRequest $request){
        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password] + ($request->username ? ['user_name' => $request->username] : []))) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid login details'
            ], 401);
        }
        $user = User::where('email',$request->email)->first();
        return response()->json([
            'status'=>true,
            'message'=>'Successfully logged in',
            'token'=>$user->createToken("API TOKEN")->plainTextToken
        ], 200);
    }

    // public function login(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'email' => 'required|email',
    //         'password' => 'required',
    //         'device_name' => 'required',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json($validator->errors(), 400);
    //     }

    //     $user = User::where('email', $request->email)->first();

    //     if (! $user || ! Hash::check($request->password, $user->password)) {
    //         return response()->json(['email' => ['The provided credentials are incorrect.']], 401);
    //     }

    //     $token = $user->createToken($request->device_name)->plainTextToken;

    //     return response()->json(['token' => $token], 200);
    // }

    public function logout(Request $request) {
        
        $request->user()->tokens()->delete();
        Auth::logout();

        // Invalidar la sesión actual
        $request->session()->invalidate();
        // Regenera el token de la sesión para evitar CSRF
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Successfully logged out'], 200);
    }
}
