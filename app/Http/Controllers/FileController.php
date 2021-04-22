<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Image;
use App\Models\ItemFile;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Str;

class FileController extends Controller
{
    function resize_crop_image($max_width, $max_height, $source_file, $dst_dir, $quality = 80){
        $imgsize = getimagesize($source_file);
        $width = $imgsize[0];
        $height = $imgsize[1];
        $mime = $imgsize['mime'];

        switch($mime){
            case 'image/gif':
                $image_create = "imagecreatefromgif";
                $image = "imagegif";
                break;

            case 'image/png':
                $image_create = "imagecreatefrompng";
                $image = "imagepng";
                $quality = 7;
                break;

            case 'image/jpeg':
                $image_create = "imagecreatefromjpeg";
                $image = "imagejpeg";
                $quality = 80;
                break;

            default:
                return false;
                break;
        }

        $dst_img = imagecreatetruecolor($max_width, $max_height);
        $src_img = $image_create($source_file);

        $width_new = $height * $max_width / $max_height;
        $height_new = $width * $max_height / $max_width;
        //if the new width is greater than the actual width of the image, then the height is too large and the rest cut off, or vice versa
        if($width_new > $width){
            //cut point by height
            $h_point = (($height - $height_new) / 2);
            //copy image
            imagecopyresampled($dst_img, $src_img, 0, 0, 0, $h_point, $max_width, $max_height, $width, $height_new);
        }else{
            //cut point by width
            $w_point = (($width - $width_new) / 2);
            imagecopyresampled($dst_img, $src_img, 0, 0, $w_point, 0, $max_width, $max_height, $width_new, $height);
        }

        $image($dst_img, $dst_dir, $quality);

        if($dst_img)imagedestroy($dst_img);
        if($src_img)imagedestroy($src_img);
    }


    public function uploadFile(Request $request) {

        $validator = Validator::make($request->all(),[
            'files' => 'required',
            'id' => 'required|string',
            'image_type' => 'required|string',
            'module_id' => 'required|string',
            'module_name' => 'required|string'
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
            $fileDb->image_type = $request->image_type;
            $destinationPath = 'uploads\\'.$request->id;
            $fileDb->url = $file->move($destinationPath,$uuid.'.'.$file->getClientOriginalExtension());
            $fileDb->created_by = $request->user()->id;
            if($request->thumbnail != 'false'){
                $thumb = $destinationPath.'\\'.'thumbnail\\'.$uuid.'.'.$file->getClientOriginalExtension();
                if (!file_exists($destinationPath.'\\'.'thumbnail')) {
                    mkdir($destinationPath.'\\'.'thumbnail');
                }
                $this->resize_crop_image(100, 100, $fileDb->url, $thumb);
                $fileDb->thumbnail = $thumb;
            }
            $fileDb->save();
            array_push($result,["id" => $fileDb->id, "name" => $file->getClientOriginalName()]);
            $itemFile = new ItemFile();
            $itemFile->file_id =  $fileDb->id;
            $itemFile->item_id =  $request->id;
            $itemFile->save();
        }
        return response()->json([
            'message' => 'Successfully upload file',
            'data' => $result
        ],200);


    }
}
