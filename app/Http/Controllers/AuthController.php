<?php

namespace App\Http\Controllers;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;

class AuthController extends Controller
{
    public function register(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required|string|unique:users',
            'username' => 'required|string|unique:users',
            'password' => 'required|string|confirmed'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }
        $user = new User([
            'email' => $request->email,
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'verification_code' => sha1(time())
        ]);
        $user->email_verified_at = date("Y-m-d H:i:s");
        $user->verification_code = null;
        $user->save();
        //MailController::sendSignupEmail($user->name,$user->email,$user->verification_code);

        return response()->json([
            'message' => 'Successfully created user'
        ],201);
    }


    public function confirmation(Request $request,$verification_code){
        $user = User::where('verification_code', $verification_code)->first();
        if(!$verification_code || !$user){
            return view('auth.confirm_fail');
        }
        $user->email_verified_at = date("Y-m-d H:i:s");
        $user->verification_code = null;
        $user->save();
        return view('auth.confirm_success');

    }


    public function login(Request $request){
        $request->validate([
            'username' => 'required',
            'password' => 'required',
            'remember_me' => 'boolean'
        ]);

        $credentials = request(['username','password']);

        if(!Auth::attempt($credentials)){
            return response()->json(['message' => 'Unauthorized'],401);
        }

        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;

        if($request->remember_me){
            $token->expires_at = Carbon::now()->addWeeks(1);
        }
        $token->save();
        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateString()
        ]);
    }

    public function logout(Request $request){
        $request->user()->token()->revoke();
        return response()->json(['message' => 'Successfully logged out']);
    }


    public function user(Request $request){
        return response()->json($request->user());
    }

}
