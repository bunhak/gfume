<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\ExchangeRate;
use App\Models\Item;
use App\Models\ItemDetail;
use App\Models\Shop;
use App\Models\Size;
use App\Models\SizeType;
use App\Models\SubCategory;
use App\Models\SubSubCategory;
use App\Services\MobileFormatService;
use Illuminate\Http\Request;
use App\Http\Controllers\FileController;
use Validator;
use DB;

class ItemController extends Controller
{
    // Admin and seller
    public function mockData(Request $request){
        //mock category
        $sub_sub_categories = [];
        $fileController = new FileController();
        $user_id = $request->user()->id;
        $a=[
            [
                'img'=>'Fashion@1X.png',
                'path'=>'img/Fashion@1X.png',
                'name'=>'Fashion',
                'extension'=>'png',
                'size'=>123,
                'mime_type'=>'png'
            ],
            [
                'img'=>'interior@1X.png',
                'path'=>'img/interior@1X.png',
                'name'=>'Interior',
                'extension'=>'png',
                'size'=>123,
                'mime_type'=>'png'
            ],
            [
                'img'=>'Beauty.svg',
                'path'=>'img/Beauty.svg',
                'name'=>'Beauty',
                'extension'=>'svg',
                'size'=>123,
                'mime_type'=>'svg'
            ]
        ];
        for($i = 0;$i < 15;$i++){
            $t = rand(0,2);
            $cat = new Category();
            $cat->name = $a[$t]['name'].$i;
            $cat->created_by = $user_id;
            $cat->click_count = rand(0, 100000);
            $cat->save();
            $a[$t]['module_name'] = 'category';
            $a[$t]['module_id'] = $cat->id;
            $a[$t]['image_type'] = 'web';
            $fileController->mockFile($a[$t],$user_id);
            $a[$t]['image_type'] = 'mobile';
            $fileController->mockFile($a[$t],$user_id);
            for($ii = 0;$ii < 4;$ii++){
                $cat2 = new SubCategory();
                $cat2->name = $a[$t]['name'].' sub '.$i.' ('.$ii.') ';
                $cat2->category_id = $cat->id;
                $cat2->category_name = $cat->name;
                $cat2->click_count = rand(0, 100000);
                $cat2->save();
                $a[$t]['module_name'] = 'sub_category';
                $a[$t]['module_id'] = $cat2->id;
                $a[$t]['image_type'] = 'web';
                $fileController->mockFile($a[$t],$user_id);
                $a[$t]['image_type'] = 'mobile';
                $fileController->mockFile($a[$t],$user_id);
                for($iii = 0;$iii < 5;$iii++){
                    $cat3 = new SubSubCategory();
                    $cat3->name =$a[$t]['name'].' sub '.$i.' sub '.$ii.' ('.$iii.')';
                    $cat3->category_id = $cat->id;
                    $cat3->category_name = $cat->name;
                    $cat3->sub_category_id = $cat2->id;
                    $cat3->sub_category_name = $cat2->name;
                    $cat3->click_count = rand(0, 100000);
                    $cat3->save();
                    array_push($sub_sub_categories,$cat3->id);
                    $a[$t]['module_name'] = 'sub_sub_category';
                    $a[$t]['module_id'] = $cat3->id;
                    $a[$t]['image_type'] = 'web';
                    $fileController->mockFile($a[$t],$user_id);
                    $a[$t]['image_type'] = 'mobile';
                    $fileController->mockFile($a[$t],$user_id);
                }
            }
        }

        //mock brand
        $a=[
            [
                'img'=>'adidas.png',
                'path'=>'img/adidas.png',
                'name'=>'adidas',
                'extension'=>'png',
                'size'=>123,
                'mime_type'=>'png'
            ],
            [
                'img'=>'nike.png',
                'path'=>'img/nike.png',
                'name'=>'nike',
                'extension'=>'png',
                'size'=>123,
                'mime_type'=>'png'
            ],
            [
                'img'=>'jordan.png',
                'path'=>'img/jordan.png',
                'name'=>'jordan',
                'extension'=>'png',
                'size'=>123,
                'mime_type'=>'png'
            ]
        ];
        $brands = [];
        for($i =0;$i<3;$i++){
            $brand = new Brand();
            $brand->name = $a[$i]['name'];
            $brand->save();
            array_push($brands,$brand->id);
            $a[$i]['module_name'] = 'brand';
            $a[$i]['module_id'] = $brand->id;
            $a[$i]['image_type'] = 'web';
            $fileController->mockFile($a[$i],$user_id);
            $a[$i]['image_type'] = 'mobile';
            $fileController->mockFile($a[$i],$user_id);
        }


        $a=[
            [
                'img'=>'gfume.png',
                'path'=>'img/gfume.png',
                'name'=>'bag',
                'extension'=>'png',
                'size'=>123,
                'mime_type'=>'png'
            ]
        ];
        $shop = new Shop();
        $shop->name = 'GFUME';
        $shop->created_by = $user_id;
        $shop->user_id = $user_id;
        $shop->save();
        $a[0]['module_name'] = 'shop';
        $a[0]['module_id'] = $shop->id;
        $a[0]['image_type'] = 'shop';
        $fileController->mockFile($a[0],$user_id);


        $a=[
            [
                'img'=>'bag.svg',
                'path'=>'img/bag.svg',
                'name'=>'bag',
                'extension'=>'svg',
                'size'=>123,
                'mime_type'=>'svg'
            ],
            [
                'img'=>'scarf.svg',
                'path'=>'img/scarf.svg',
                'name'=>'scarf',
                'extension'=>'svg',
                'size'=>123,
                'mime_type'=>'svg'
            ],
            [
                'img'=>'shoe.svg',
                'path'=>'img/shoe.svg',
                'name'=>'shoe',
                'extension'=>'svg',
                'size'=>123,
                'mime_type'=>'svg'
            ]
        ];
        $colors = ['Red','Yellow','Pink','Grey','Blue'];
        for($i=0 ; $i < sizeof($sub_sub_categories);$i++){
            $item_rand = rand(0,2);
            $brand_rand = rand(0,2);
            $item = new Item();
            $item->name = $a[$item_rand]['name'].' '.$i;
            $item->brand_id = $brands[$brand_rand];
            $item->shop_id = $shop->id;
            $item->description = 'this is description test for '.$a[$item_rand]['name'].' '.$i;
            $item->sub_sub_category_id = $sub_sub_categories[$i];
            $item->created_by = $user_id;
            $item->save();
            $a[$item_rand]['module_name'] = 'item';
            $a[$item_rand]['module_id'] = $item->id;
            for($bb =0;$bb < rand(1,8);$bb++){
                $a[$item_rand]['image_type'] = 'detail';
                $fileController->mockFile($a[$item_rand],$user_id);
            }
            for($bb =0;$bb < rand(1,8);$bb++){
                $a[$item_rand]['image_type'] = 'slide';
                $fileController->mockFile($a[$item_rand],$user_id);
            }
            $col_rand_max = rand(0,(sizeof($colors) - 1));
            $mockColor = [];
            for($color_rand = 0;$color_rand <= $col_rand_max;$color_rand++){
                $color = new Color();
                $color->name = $colors[$color_rand];
                $color->item_id = $item->id;
                $color->created_by = $user_id;
                $color->save();
                array_push($mockColor,$color->id);
                $a[$item_rand]['module_name'] = 'color';
                $a[$item_rand]['module_id'] = $color->id;
                $a[$item_rand]['image_type'] = 'mobile';
                $fileController->mockFile($a[$item_rand],$user_id);
            }

            $mockSize = [];
            for($ss = 29;$ss < rand(32,42);$ss++){
                $size = new Size();
                $size->name = $ss;
                $size->item_id = $item->id;
                $size->created_by = $user_id;
                $size->save();
                array_push($mockSize,$size->id);
            }

            if(sizeof($mockColor) > 0){
                for($c = 0;$c<sizeof($mockColor);$c++){
                    for($s =0;$s<sizeof($mockSize);$s++){
                        $itemDetail = new ItemDetail();
                        $itemDetail->item_id = $item->id;
                        $itemDetail->qty = rand(1,300);
                        $itemDetail->price = rand(1,200);
                        $itemDetail->color_id = $mockColor[$c];
                        $itemDetail->size_id = $mockSize[$s];
                        $itemDetail->created_by = $user_id;
                        $itemDetail->save();
                    }
                }
            }else{
                $itemDetail = new ItemDetail();
                $itemDetail->item_id = $item->id;
                $itemDetail->qty = rand(1,300);
                $itemDetail->price = rand(1,200);
                $itemDetail->created_by = $user_id;
                $itemDetail->save();
            }
        }

        //mock exchange rate
        $exc = new ExchangeRate();
        $exc->exchange_rate = 4050;
        $exc->money_type = 'KHR';
        $exc->created_by = $user_id;
        $exc->save();
    }
    public function createNewItem(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required|string',
            'shop_id' => 'required|string',
            'sub_sub_category_id' => 'required|string',
            'item_details' => 'required',
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
        $page = $request->page ? (int)$request->page : 1;
        $limit = $request->limit ? (int)$request->limit : 10;
        $where = '';
        if($request->search && $request->search != ''){
            $where = ' AND i.name like "%'.$request->search.'%"';
        }
        $items = DB::select("SELECT i.id AS id , i.name AS name, b.name AS brand, s.name AS shop, ssc.sub_category_name AS sub_sub_category FROM items i
                            LEFT JOIN brands b ON i.brand_id = b.id
                            LEFT JOIN shops s ON i.shop_id = s.id
                            LEFT JOIN sub_sub_categories ssc ON i.sub_sub_category_id = ssc.id
                            WHERE i.is_deleted = false ".$where."
                            LIMIT ".$limit."
                            offset ".(($page - 1) * 10));
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
            SELECT id, CONCAT('".env('APP_URL')."','/',url) AS url FROM files
            WHERE item_id = '".$id."'
            AND image_type = 'slide'");
        $item_image_detail = DB::select("
            SELECT id, CONCAT('".env('APP_URL')."','/',url) AS url FROM files
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
        $shops = DB::table('shops')->select('id','name')->where('user_id,','=',$request->user()->id)->get();
        $brands = DB::table('brands')->select('id','name')->get();
        $sub_sub_categories = DB::table('sub_sub_categories')->select('id','name','category_name','sub_category_name')->get();
        $result = [
            "brands" => $brands,
            "shops" => $shops,
            "sub_sub_categories" => $sub_sub_categories
        ];

        return response()->json([
            'status' => 'success',
            "code"=> 200,
            "message"=> "OK",
            'data' => $result
        ],200);
    }




    //User
    public function getLookingForThis(Request $request){
        $limit = $request->limit ? (int)$request->limit : 10;
        $page = $request->page ? (int)$request->page : 1;
        $exchange_rate = DB::select("select exchange_rate from exchange_rates where money_type = 'KHR' order by created_at desc limit 1")[0]->exchange_rate;
        $items = DB::select("SELECT i.id AS id, i.name AS name
                            FROM items i 
                            ORDER BY RAND() LIMIT ".$limit);
        $total = Item::where('is_deleted','=',false)->count();
        foreach ($items as $item){
            $image = DB::select("SELECT CONCAT('".env('APP_URL')."','/',url) as image FROM files WHERE module_id = '".$item->id."' AND image_type = 'slide' LIMIT 1");
            $price = DB::select("SELECT price FROM item_details WHERE item_id = '".$item->id."' order by price limit 1");
            $item->image = $image[0]->image;
            $item->price = [
                "USD" => $price[0]->price,
                "KHR" => $price[0]->price * $exchange_rate
            ];
            $item->rate = rand(0, 1000);
            $item->star = mt_rand(0 * 2, 5 * 2) / 2;
        }
        $last_page = ceil($total / $limit);
        return MobileFormatService::formatWithPagination($items,'items',$page,$last_page,$limit,$total);

    }
    public function getDontYouNeedThis(Request $request){
        $limit = $request->limit ? (int)$request->limit : 10;
        $page = $request->page ? (int)$request->page : 1;
        $exchange_rate = DB::select("select exchange_rate from exchange_rates where money_type = 'KHR' order by created_at desc limit 1")[0]->exchange_rate;
        $items = DB::select("SELECT i.id AS id, i.name AS name
                            FROM items i 
                            ORDER BY RAND() LIMIT ".$limit);
        $total = Item::where('is_deleted','=',false)->count();
        foreach ($items as $item){
            $image = DB::select("SELECT CONCAT('".env('APP_URL')."','/',url) as image FROM files WHERE module_id = '".$item->id."' AND image_type = 'slide' LIMIT 1");
            $price = DB::select("SELECT price FROM item_details WHERE item_id = '".$item->id."' order by price limit 1");
            $item->image = $image[0]->image;
            $item->price = [
                "USD" => $price[0]->price,
                "KHR" => $price[0]->price * $exchange_rate
            ];
            $item->rate = rand(0, 1000);
            $item->star = mt_rand(0 * 2, 5 * 2) / 2;
        }
        $last_page = ceil($total / $limit);
        return MobileFormatService::formatWithPagination($items,'items',$page,$last_page,$limit,$total);
    }
    public function getRecommendItemHome(Request $request){
        $limit = $request->limit ? (int)$request->limit : 10;
        $page = $request->page ? (int)$request->page : 1;
        $exchange_rate = DB::select("select exchange_rate from exchange_rates where money_type = 'KHR' order by created_at desc limit 1")[0]->exchange_rate;
        $items = DB::select("SELECT i.id AS id, i.name AS name
                            FROM items i 
                            ORDER BY RAND() LIMIT ".$limit);
        $total = Item::where('is_deleted','=',false)->count();
        foreach ($items as $item){
            $image = DB::select("SELECT CONCAT('".env('APP_URL')."','/',url) as image FROM files WHERE module_id = '".$item->id."' AND image_type = 'slide' LIMIT 1");
            $price = DB::select("SELECT price FROM item_details WHERE item_id = '".$item->id."' order by price limit 1");
            $item->image = $image[0]->image;
            $item->price = [
                "USD" => $price[0]->price,
                "KHR" => $price[0]->price * $exchange_rate
            ];
            $item->rate = rand(0, 1000);
            $item->star = mt_rand(0 * 2, 5 * 2) / 2;
        }
        $last_page = ceil($total / $limit);
        return MobileFormatService::formatWithPagination($items,'items',$page,$last_page,$limit,$total);
    }
    public function getGoodToCompare(Request $request){
        $limit = $request->limit ? (int)$request->limit : 10;
        $page = $request->page ? (int)$request->page : 1;
        $exchange_rate = DB::select("select exchange_rate from exchange_rates where money_type = 'KHR' order by created_at desc limit 1")[0]->exchange_rate;
        $items = DB::select("SELECT i.id AS id, i.name AS name
                            FROM items i 
                            ORDER BY RAND() LIMIT ".$limit);
        $total = Item::where('is_deleted','=',false)->count();
        foreach ($items as $item){
            $image = DB::select("SELECT CONCAT('".env('APP_URL')."','/',url) as image FROM files WHERE module_id = '".$item->id."' AND image_type = 'slide' LIMIT 1");
            $price = DB::select("SELECT price FROM item_details WHERE item_id = '".$item->id."' order by price limit 1");
            $item->image = $image[0]->image;
            $item->price = [
                "USD" => $price[0]->price,
                "KHR" => $price[0]->price * $exchange_rate
            ];
            $item->rate = rand(0, 1000);
            $item->star = mt_rand(0 * 2, 5 * 2) / 2;
        }
        $last_page = ceil($total / $limit);
        return MobileFormatService::formatWithPagination($items,'items',$page,$last_page,$limit,$total);
    }
    public function getCustomerViewThisItemAlsoView(Request $request){
        $limit = $request->limit ? (int)$request->limit : 10;
        $page = $request->page ? (int)$request->page : 1;
        $exchange_rate = DB::select("select exchange_rate from exchange_rates where money_type = 'KHR' order by created_at desc limit 1")[0]->exchange_rate;
        $items = DB::select("SELECT i.id AS id, i.name AS name
                            FROM items i 
                            ORDER BY RAND() LIMIT ".$limit);
        $total = Item::where('is_deleted','=',false)->count();
        foreach ($items as $item){
            $image = DB::select("SELECT CONCAT('".env('APP_URL')."','/',url) as image FROM files WHERE module_id = '".$item->id."' AND image_type = 'slide' LIMIT 1");
            $price = DB::select("SELECT price FROM item_details WHERE item_id = '".$item->id."' order by price limit 1");
            $item->image = $image[0]->image;
            $item->price = [
                "USD" => $price[0]->price,
                "KHR" => $price[0]->price * $exchange_rate
            ];
            $item->rate = rand(0, 1000);
            $item->star = mt_rand(0 * 2, 5 * 2) / 2;
        }
        $last_page = ceil($total / $limit);
        return MobileFormatService::formatWithPagination($items,'items',$page,$last_page,$limit,$total);
    }
    public function getItemDetailById(Request $request){
        $id = $request->id ? $request->id : null;
        $item = DB::select("SELECT i.id AS id, i.name AS name, i.description as description,i.shop_id as shop_id
                            FROM items i 
                            where id = '".$id."'")[0];
        $exchange_rate = DB::select("select exchange_rate from exchange_rates where money_type = 'KHR' order by created_at desc limit 1")[0]->exchange_rate;
        if($item){
            $price = DB::select("SELECT price FROM item_details WHERE item_id = '".$item->id."' order by price limit 1")[0]->price;
            $item->price = [
                "USD" => $price,
                "KHR" => $price * $exchange_rate
            ];
            $item->image_slide = [];
            $slides = DB::select("SELECT CONCAT('".env('APP_URL')."','/',url) as slide FROM files WHERE module_id = '".$item->id."' AND image_type = 'slide'");
            foreach ($slides as $slide){
                array_push($item->image_slide,$slide->slide);
            }
            $item->image_detail = [];
            $details = DB::select("SELECT CONCAT('".env('APP_URL')."','/',url) as detail FROM files WHERE module_id = '".$item->id."' AND image_type = 'detail'");
            foreach ($details as $detail){
                array_push($item->image_detail,$detail->detail);
            }
            $item->color = DB::select("select id,name from colors where item_id = '".$item->id."'");
            $item->size = DB::select("select id,name from sizes where item_id = '".$item->id."'");
            $item->item_detail = DB::select("select id,qty,price,color_id,size_id from item_details where item_id = '".$item->id."'");
            $item->in_stock = 0;
            foreach ($item->item_detail as $itd){
                $item->in_stock = $item->in_stock + (int)$itd->qty;
                $itd->price = [
                    "USD" => $itd->price,
                    "KHR" => $itd->price * $exchange_rate
                ];
            }
            $item->image_detail = [];
            $details = DB::select("SELECT CONCAT('".env('APP_URL')."','/',url) as detail FROM files WHERE module_id = '".$item->id."' AND image_type = 'detail'");
            foreach ($details as $detail){
                array_push($item->image_detail,$detail->detail);
            }
            $shop = DB::select("SELECT id,name from shops where id = '".$item->shop_id."'")[0];
            $shop->image = DB::select("SELECT CONCAT('".env('APP_URL')."','/',url) as image FROM files WHERE module_id = '".$shop->id."' AND image_type = 'shop'")[0]->image;
            $item->shop = $shop;
            $item->rate = rand(0, 1000);
            $item->star = mt_rand(0 * 2, 5 * 2) / 2;

        }
        return MobileFormatService::formatWithoutPagination($item);
    }
    public function getLowerPrice(Request $request){
        $id = $request->id;
        $page = $request->page ? (int)$request->page : 1;
        $limit = $request->limit ? (int)$request->limit : 1;
        $item = DB::select("SELECT i.id,i.name,itd.price FROM items i
                INNER JOIN item_details itd ON itd.item_id = i.id
                WHERE i.id = '".$id."'
                ORDER BY itd.price
                LIMIT 1")[0];
        $item_lower_count = DB::select("SELECT i.id,i.name,itd.price FROM item_details itd
            INNER JOIN items i ON itd.item_id = i.id
            WHERE itd.price < ".$item->price."
            AND i.id <> '".$item->id."'
            AND i.name = '".$item->name."'
            ORDER BY itd.price");
        $tmp = [];
        foreach ($item_lower_count as $ilc){
            $tmp[$ilc->id] = $ilc;
        }
        $item_lower_count = [];
        foreach ($tmp as $tm){
            array_push($item_lower_count,$tm);
        }
        if(sizeof($item_lower_count) > 0){
            $items = [];
            $exchange_rate = DB::select("select exchange_rate from exchange_rates where money_type = 'KHR' order by created_at desc limit 1")[0]->exchange_rate;
            $max = ($page * $limit) <= sizeof($item_lower_count) ? ($page * $limit) : sizeof($item_lower_count);
            for($i = ($limit - 1)*$page;$i< $max;$i++){
                $item = DB::select("SELECT i.id AS id, i.name AS name
                            FROM items i 
                            where id = '".$item_lower_count[$i]->id."'")[0];
                $image = DB::select("SELECT CONCAT('".env('APP_URL')."','/',url) as image FROM files WHERE module_id = '".$item->id."' AND image_type = 'slide' LIMIT 1");
                $price = DB::select("SELECT price FROM item_details WHERE item_id = '".$item->id."' order by price limit 1");
                $item->image = $image[0]->image;
                $item->price = [
                    "USD" => $price[0]->price,
                    "KHR" => $price[0]->price * $exchange_rate
                ];
                $item->rate = rand(0, 1000);
                $item->star = mt_rand(0 * 2, 5 * 2) / 2;
                array_push($items,$item);
            }
            $total = sizeof($item_lower_count);
            $last_page = ceil(sizeof($item_lower_count) / $limit);
            return MobileFormatService::formatWithPagination($items,'items',$page,$last_page,$limit,$total);
        }else{
            return MobileFormatService::formatWithPagination([],'items',$page,0,$limit,0);
        }
    }

}
