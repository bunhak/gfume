<?php
namespace App\Services;


class MobileFormatService {
    public static function formatWithPagination($data,$module,$current_page,$last_page,$per_page,$total){
        if($data || sizeof($data) == 0){
            return response()->json([
                'status' => 'success',
                "code"=> 200,
                "message"=> "OK",
                'data' => [$module => $data],
                'meta' => [
                    'current_page' => $current_page,
                    'last_page' =>$last_page,
                    'per_page' => $per_page,
                    'total' => $total
                ]
            ],200);
        }else{
            return response()->json([
                'status' => 'Fail',
                "code"=> 400,
                "message"=> "Fail",
                'data' => 'Fail'
            ],400);
        }
    }
    public static function formatWithoutPagination($data){
        $is_array = false;
        if(gettype($data) == 'array'){
            $is_array = true;
        }
        if($data || ($is_array == true && sizeof($data) == 0)){
            return response()->json([
                'status' => 'success',
                "code"=> 200,
                "message"=> "OK",
                'data' => $data,
            ],200);
        }else{
            return response()->json([
                'status' => 'Fail',
                "code"=> 400,
                "message"=> "Fail",
                'data' => 'Fail'
            ],400);
        }
    }
}
