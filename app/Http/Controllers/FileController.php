<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Image;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Str;

class FileController extends Controller
{
    public function uploadFile(Request $request) {

        $validator = Validator::make($request->all(),[
            'files' => 'required',
            'item_id' => 'required|string',
            'image_type' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }
        $files = $request->file('files');
        $result = [];
        for($i = 0;$i< sizeof($files);$i++){
            $file = $files[$i];
            $fileDb = new File();
            $uuid = Str::uuid()->toString();
            $fileDb->id = $uuid;
            $fileDb->extension =  $file->getClientOriginalExtension();
            $fileDb->size = $file->getSize();
            $fileDb->mime_type = $file->getMimeType();
            $fileDb->item_id = $request->item_id;
            $fileDb->image_type = $request->image_type;
            $destinationPath = 'uploads\\'.$request->item_id;
            $fileDb->url = $file->move($destinationPath,$uuid.'.'.$file->getClientOriginalExtension());
            $fileDb->save();
            array_push($result,["id" => $fileDb->id, "name" => $file->getClientOriginalName()]);
        }
        return response()->json([
            'message' => 'Successfully upload file',
            'data' => $result
        ],200);


    }
}
