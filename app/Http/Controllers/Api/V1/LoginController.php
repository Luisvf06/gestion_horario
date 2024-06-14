<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
class LoginController extends Controller
{
  public function login(LoginRequest $request)
  {
    if (!Auth::attempt($request->only('user_name', 'password'))) {
      return response()->json([
        'status' => false,
        'message' => 'Unauthorized'
      ], 401);
    }
    $user = User::where('user_name', $request->user_name)->first();
    return response()->json([
      'status' => true,
      'message' => 'Login Success',
      'token' => $request->user()->createToken("API TOKEN")->plainTextToken,
      'message' => 'Success'
    ],200);
  }

}