<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Item;
use App\Models\ItemDetail;
use App\Models\Shop;
use App\Models\Size;
use App\Models\SizeType;
use App\Models\SubCategory;
use App\Models\SubSubCategory;
use Illuminate\Http\Request;
use Validator;
use DB;

class ItemController extends Controller
{

    public function mockData(){

        //mock category
        $a=['img/Fashion@1X.png','img/interior@1X.png'];
        for($i = 0;$i < 15;$i++){
            $t = rand(0,1);
            $cat = new Category();
            $cat->name = 'l1('.$i.')';
            $cat->image = $a[$t];
            $cat->click_count = rand(0, 100000);
            $cat->save();
            for($ii = 0;$ii < 4;$ii++){
                $tt = rand(0,1);
                $cat2 = new SubCategory();
                $cat2->name = 'l1('.$i.') l2('.$ii.')';
                $cat2->category_id = $cat->id;
                $cat2->category_name = $cat->name;
                $cat2->image = $a[$tt];
                $cat2->click_count = rand(0, 100000);
                $cat2->save();
                for($iii = 0;$iii < 5;$iii++){
                    $ttt = rand(0,1);
                    $cat3 = new SubSubCategory();
                    $cat3->name ='l1('.$i.') l2('.$ii.') l3('.$i.')';
                    $cat3->sub_category_id = $cat2->id;
                    $cat3->category_name = $cat->name;
                    $cat3->sub_category_name = $cat2->name;
                    $cat3->image = $a[$ttt];
                    $cat3->click_count = rand(0, 100000);
                    $cat3->save();
                }
            }
        }

        //mock brand
        $a=['img/adidas.png','img/nike.png','img/jordan.png'];
        for($i =0;$i<3;$i++){
            $brand = new Brand();
            $brand->name = 'brand '.$i;
            $brand->image = $a[$i];
            $brand->save();
        }

        //mock color
        $a=['red','yellow','blue','black','white'];
        for($i = 0;$i<5;$i++){
            $color = new Color();
            $color->name = $a[$i];
            $color->save();
        }

        // mock size text
        $sizeType = new SizeType();
        $sizeType->name = 'text';
        $sizeType->save();
        $a=['xs','s','m','l','xl'];
        for ($i = 0;$i < sizeof($a);$i++){
            $size = new Size();
            $size->name = $a[$i];
            $size->size_type_id = $sizeType->id;
            $size->order = $i;
            $size->save();
        }

        // mock size number
        $sizeType = new SizeType();
        $sizeType->name = 'number';
        $sizeType->save();
        for ($i = 10;$i < 100;$i++){
            $size = new Size();
            $size->name = $i;
            $size->size_type_id = $sizeType->id;
            $size->order = $i;
            $size->save();
        }

        $shop = new Shop();
        $shop->name = 'GFUME';
        $shop->save();


    }

    public function addNewItem(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }

        $item = new Item();
        $item->name = $request->name;
        $item->brand_id = $request->brand_id;
        $item->shop_id = $request->shop_id;
        $item->video_url = $request->video_url;
        $item->description = $request->description;
        $item->sub_sub_category_id = $request->sub_sub_category_id;
        $item->created_by = $request->user()->id;
        $item->save();
        for($i =0;$i<sizeof($request->item_details);$i++){
            $it = $request->item_details[$i];
            $item_detail = new ItemDetail();
            $item_detail->item_id = $item->id;
            $item_detail->qty = array_key_exists('qty', $it) ?  $it['qty'] : null;
            $item_detail->price = array_key_exists('price', $it) ?  $it['price'] : null;
            $item_detail->size_id = array_key_exists('size_id', $it) ?  $it['size_id'] : null;
            $item_detail->color_id = array_key_exists('color_id', $it) ?  $it['color_id'] : null;
            $item_detail->size_type_id = array_key_exists('size_type_id', $it) ?  $it['size_type_id'] : null;
            $item_detail->created_by = $request->user()->id;
            $item_detail->save();
        }
        return response()->json([
            'status' => 'success',
            "code"=> 200,
            "message"=> "OK",
            'data' => $item
        ],200);
    }


    public function deleteItem(Request $request){
        $item_id = $request->id;
        $item = Item::find($item_id);
        $item->is_deleted = true;
        $item->updated_by = $request->user()->id;
        $item->save();
        return response()->json([
            'status' => 'success',
            "code"=> 200,
            "message"=> "OK"
        ],200);
    }


    public function getAllItem(Request $request){
        $page = $request->page ? $request->page : 1;
        $limit = $request->limit ? $request->limit : 10;
        $items = DB::select("SELECT i.id AS id , i.name AS name, b.name AS brand, s.name AS shop, ssc.sub_category_name AS sub_sub_category FROM items i
                            LEFT JOIN brands b ON i.brand_id = b.id
                            LEFT JOIN shops s ON i.shop_id = s.id
                            LEFT JOIN sub_sub_categories ssc ON i.sub_sub_category_id = ssc.id
                            LIMIT ".$limit."
                            offset ".(($page - 1) * 10) .
                            " WHERE i.is_deleted = false");
        $count = DB::table('items')->where('is_deleted','=',false)->count();
        $result = [
            'items' => $items,
            "meta" =>[
                'current_page' => $page,
                'last_page' => ceil( $count / $limit ),
                'per_page' => $limit,
                'total' => $count
            ]
        ];

        return response()->json([
            'status' => 'success',
            "code"=> 200,
            "message"=> "OK",
            'data' => $result
        ],200);
    }
    public function getItemDetail(Request $request){
        $validator = Validator::make($request->all(),[
            'id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }
        $id = $request->id ? $request->id : null;
        $item = DB::table('items')->where('id','=',$id)->first();
        $item_details = DB::table('item_details')->where('item_id','=',$id)->get();
        $item_image_slide = DB::select("
            SELECT id, CONCAT('".env('APP_URL')."',url) AS url FROM files
            WHERE item_id = '".$id."'
            AND image_type = 'slide'");
        $item_image_detail = DB::select("
            SELECT id, CONCAT('".env('APP_URL')."',url) AS url FROM files
            WHERE item_id = '".$id."'
            AND image_type = 'detail'");
        $result = [
            "id" => $item->id,
            "name" => $item->name,
            "brand_id" => $item->brand_id,
            "shop_id" => $item->shop_id,
            "sub_sub_category_id" => $item->sub_sub_category_id,
            "description" => $item->description,
            "item_details" => $item_details,
            "image_details" => $item_image_detail,
            "image_slide" => $item_image_slide,
        ];

        return response()->json([
            'status' => 'success',
            "code"=> 200,
            "message"=> "OK",
            'data' => $result
        ],200);
    }

    public function getItemProperty(Request $request){
        $brands = DB::table('brands')->select('id','name')->get();
        $colors = DB::table('colors')->select('id','name')->get();
        $shops = DB::table('shops')->select('id','name')->get();
        $sizes = DB::table('sizes')->select('id','name')->get();
        $size_types = DB::table('size_types')->select('id','name')->get();
        $sub_sub_categories = DB::table('sub_sub_categories')->select('id','name','category_name','sub_category_name')->get();

        $result = [
            "brands" => $brands,
            "colors" => $colors,
            "shops" => $shops,
            "sizes" => $sizes,
            "size_types" => $size_types,
            "sub_sub_categories" => $sub_sub_categories
        ];

        return response()->json([
            'status' => 'success',
            "code"=> 200,
            "message"=> "OK",
            'data' => $result
        ],200);
    }
}
