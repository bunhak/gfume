<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Services\MobileFormatService;
use Illuminate\Http\Request;
use Validator;
use DB;

class UserController extends Controller
{
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
