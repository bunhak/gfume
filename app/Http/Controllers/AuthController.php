<?php

namespace App\Http\Controllers;
use App\Models\Address;
use App\Models\RoleUser;
use App\Models\User;
use App\Services\MobileFormatService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;
use DB;

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
            'role' => 'USER',
            'password' => Hash::make($request->password),
            'verification_code' => sha1(time())
        ]);
        $user->email_verified_at = date("Y-m-d H:i:s");
        $user->verification_code = null;
        $user->save();
        //MailController::sendSignupEmail($user->name,$user->email,$user->verification_code);

        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;

        if($request->remember_me){
            $token->expires_at = Carbon::now()->addWeeks(1);
        }
        $token->save();
        return response()->json([
            'name' => $user->name,
            'username' => $user->username,
            'currency' => $user->currency,
            'role' => $user->role,
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateString()
        ]);
    }

    public function registerSeller(Request $request){
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
            'role' => 'SELLER',
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'verification_code' => sha1(time())
        ]);
        $user->email_verified_at = date("Y-m-d H:i:s");
        $user->verification_code = null;
        $user->save();
        //MailController::sendSignupEmail($user->name,$user->email,$user->verification_code);

        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;

        if($request->remember_me){
            $token->expires_at = Carbon::now()->addWeeks(1);
        }
        $token->save();
        return response()->json([
            'name' => $user->name,
            'username' => $user->username,
            'currency' => $user->currency,
            'role' => $user->role,
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateString()
        ]);
    }

    public function registerAdmin(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required|string|unique:users',
            'username' => 'required|string|unique:users',
            'password' => 'required|string|confirmed'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }
        if($request->gfumeConfirmPassword != env('GFUME')){
            return response()->json('sorry you are not valid', 400);
        }
        $user = new User([
            'email' => $request->email,
            'name' => $request->name,
            'role' => 'ADMIN',
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'verification_code' => sha1(time())
        ]);
        $user->email_verified_at = date("Y-m-d H:i:s");
        $user->verification_code = null;
        $user->save();
        //MailController::sendSignupEmail($user->name,$user->email,$user->verification_code);

        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;

        if($request->remember_me){
            $token->expires_at = Carbon::now()->addWeeks(1);
        }
        $token->save();
        return response()->json([
            'name' => $user->name,
            'username' => $user->username,
            'currency' => $user->currency,
            'role' => $user->role,
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateString()
        ]);
    }

    public function confirmation(Request $request,$verification_code){
        $user = User::where('verification_code', $verification_code)->first();
        if(!$verification_code || !$user){
            return view('auth.confirm_fail');
        }
        $user->email_verified_at = date("Y-m-d H:i:s");
        $user->verification_code = null;
        $user->save();

        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;

        if($request->remember_me){
            $token->expires_at = Carbon::now()->addWeeks(1);
        }
        $token->save();
        return response()->json([
            'name' => $user->name,
            'username' => $user->username,
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateString()
        ]);

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
            'name' => $user->name,
            'username' => $user->username,
            'currency' => $user->currency,
            'role' => $user->role,
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

    public function createAddress(Request $request){
        $validator = Validator::make($request->all(),[
            'lat' => 'required',
            'lng' => 'required',
            'location' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }
        $user = $request->user();
        $address = new Address();
        $address->name = $request->name;
        $address->noted = $request->noted;
        $address->lat = $request->lat;
        $address->lng = $request->lng;
        $address->location = $request->location;
        $address->module_id = $user->id;
        $address->module_name = 'user';
        $address->created_by = $user->id;
        $addresses = DB::select("select * from addresses where module_id = '$user->id' and is_deleted = false");
        if(sizeof($addresses) == 0){
            $address->is_default = true;
        }
        if($request->is_default == true && sizeof($addresses) > 0){
            $address->is_default = true;
            foreach ($addresses as $add){
                $add->is_default = false;
                $add->updated_by = $user->id;
                $add->save();
            }
        }
        $address->save();
        return MobileFormatService::formatWithoutPagination($address);
    }

    public function editAddress(Request $request){
        $validator = Validator::make($request->all(),[
            'id' => 'required',
            'lat' => 'required',
            'lng' => 'required',
            'location' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }

        $user = $request->user();
        $address = DB::select("select * from addresses where id = '$request->id' and is_deleted = false")[0];
        $address->name = $request->name;
        $address->noted = $request->noted;
        $address->lat = $request->lat;
        $address->lng = $request->lng;
        $address->location = $request->location;
        $address->module_id = $user->id;
        $address->module_name = 'user';
        $address->created_by = $user->id;
        $addresses = DB::select("select * from addresses where module_id = '$user->id' and is_deleted = false");
        if($request->is_default == true && sizeof($addresses) > 0){
            $address->is_default = true;
            foreach ($addresses as $add){
                $add->is_default = false;
                $add->updated_by = $user->id;
                $add->save();
            }
        }
        $address->save();
        return MobileFormatService::formatWithoutPagination($address);
    }

    public function getAddresses(Request $request){
       $user = $request->user;
       $addresses = DB::select("select id,name,noted,telephone,lat,lng,location from addresses where module_id = '$user->id'");
       foreach ($addresses as $address){
           $images = DB::select("
            SELECT id, CONCAT('".env('APP_URL')."','/',url) AS url FROM files
            WHERE module_id = '".$address->id."'
            AND image_type = 'slide'");
           if(sizeof($images) > 0){
               $address->image = $images[0]->url;
           }
       }
        return MobileFormatService::formatWithoutPagination($addresses);
    }

}
