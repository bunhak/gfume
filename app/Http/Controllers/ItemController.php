<?php

namespace App\Http\Controllers;

use App\GlobalVariable;
use App\Models\Bonus;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\DeliveryFee;
use App\Models\Discount;
use App\Models\ExchangeRate;
use App\Models\GlobalSearch;
use App\Models\GlobalSearchRank;
use App\Models\Item;
use App\Models\ItemDetail;
use App\Models\Shop;
use App\Models\Size;
use App\Models\SizeType;
use App\Models\SubCategory;
use App\Models\SubSubCategory;
use App\Models\UserSearch;
use App\Models\UserWishList;
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
                'img'=>'Beauty.png',
                'path'=>'img/Beauty.png',
                'name'=>'Beauty',
                'extension'=>'png',
                'size'=>123,
                'mime_type'=>'png'
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
                'img'=>'bag.png',
                'path'=>'img/bag.png',
                'name'=>'bag',
                'extension'=>'png',
                'size'=>123,
                'mime_type'=>'png'
            ],
            [
                'img'=>'scarf.png',
                'path'=>'img/scarf.png',
                'name'=>'scarf',
                'extension'=>'png',
                'size'=>123,
                'mime_type'=>'png'
            ],
            [
                'img'=>'shoe.png',
                'path'=>'img/shoe.png',
                'name'=>'shoe',
                'extension'=>'png',
                'size'=>123,
                'mime_type'=>'png'
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
            $delivery_fee = new DeliveryFee();
            $delivery_fee->item_id = $item->id;
            $delivery_fee->shop_id = $item->shop_id;
            $delivery_fee->delivery_fee = rand(1,3);
            $delivery_fee->created_by = $user_id;
            $delivery_fee->save();
            $shouldHaveDiscount = rand(0,1);
            $discount = new Discount();
            $discount->item_id = $item->id;
            $discount->discount = rand(1,60);
            $discount->is_default = true;
            $discount->created_by = $user_id;
            $discount->save();
            if($shouldHaveDiscount === 0){
                $discount = new Discount();
                $discount->item_id = $item->id;
                $discount->discount = rand(1,60);
                $date = date("Y-m-d H:i:s");
                $discount->start_date = $date;
                $discount->end_date = date("Y-m-d H:i:s",strtotime("+".rand(0,100)." days", strtotime($date)));
                $discount->created_by = $user_id;
                $discount->save();
            }
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
        $exc->exchange_rate = 4100;
        $exc->money_type = 'KHR';
        $exc->created_by = $user_id;
        $exc->save();

        //mock bonus
        $bonus = new Bonus();
        $bonus->bonus = 1;
        $bonus->created_by = $user_id;
        $bonus->save();
    }
    public function createNewItem(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required|string',
            'shop_id' => 'required|string',
            'sub_sub_category_id' => 'required|string',
            'item_details' => 'required',
            'delivery_fee' => 'required',
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

        $delivery_fee = new DeliveryFee();
        $delivery_fee->item_id = $item->id;
        $delivery_fee->shop_id = $item->shop_id;
        $delivery_fee->delivery_fee = $request->delivery_fee;
        $delivery_fee->created_by = $request->user()->id;
        $delivery_fee->save();

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
            WHERE module_id = '".$id."'
            AND image_type = 'slide'");
        $item_image_detail = DB::select("
            SELECT id, CONCAT('".env('APP_URL')."','/',url) AS url FROM files
            WHERE module_id = '".$id."'
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
        return $this->getDataRandom($request);
    }
    public function getDontYouNeedThis(Request $request){
        return $this->getDataRandom($request);
    }
    public function getRecommendItemHome(Request $request){
        return $this->getDataRandom($request);
    }
    public function getGoodToCompare(Request $request){
        return $this->getDataRandom($request);
    }
    public function getCustomerViewThisItemAlsoView(Request $request){
        return $this->getDataRandom($request);
    }

    public function getDataRandom(Request $request){
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
            $date = date("Y-m-d H:i:s");
            $discounts = DB::select("SELECT * from discounts where active = true and is_deleted = false and item_id = '$item->id' and start_date < '$date' and end_date > '$date'");
            $full_price = $price[0]->price;
            $discount = null;
            $item->discount = DB::select("SELECT * from discounts where active = true and is_deleted = false and item_id = '$item->id' and is_default = true order by created_at desc limit 1")[0]->discount;
            if(sizeof($discounts) > 0){
                $item->discount_limit_date = $discounts[0]->end_date;
                $item->discount = $discounts[0]->discount;
                $discount = round(($full_price * $item->discount) / 100,2 );
                $item->price = [
                    "USD" => $full_price - $discount,
                    "KHR" => ($full_price - $discount) * $exchange_rate
                ];
            }
            else{
                $item->discount_limit_date = null;
                $item->price = [
                    "USD" => $full_price,
                    "KHR" => $full_price * $exchange_rate
                ];
            }
            $item->full_price = [
                "USD" => $full_price,
                "KHR" => $full_price * $exchange_rate
            ];
            $item->rate = rand(0, 1000);
            $item->star = mt_rand(0 * 2, 5 * 2) / 2;
        }
        $last_page = ceil($total / $limit);
        return MobileFormatService::formatWithPagination($items,'items',$page,$last_page,$limit,$total);
    }

    public function getSpecialPrice(Request $request){
        $limit = $request->limit ? (int)$request->limit : 10;
        $page = $request->page ? (int)$request->page : 1;
        $date = date("Y-m-d H:i:s");
        $exchange_rate = DB::select("select exchange_rate from exchange_rates where money_type = 'KHR' order by created_at desc limit 1")[0]->exchange_rate;
        $items = DB::select("SELECT i.id AS id, i.name AS name
                            FROM items i 
                            INNER JOIN discounts d on d.item_id = i.id
                            where d.start_date < '$date' and d.end_date > '$date'
                            AND d.active = true
                            ORDER BY RAND() LIMIT ".$limit);
        $total = DB::select("SELECT count(i.id) as count_item
                            FROM items i 
                            INNER JOIN discounts d on d.item_id = i.id
                            where d.start_date < '$date' and d.end_date > '$date'
                            AND d.active = true")[0]->count_item;
        foreach ($items as $item){
            $image = DB::select("SELECT CONCAT('".env('APP_URL')."','/',url) as image FROM files WHERE module_id = '".$item->id."' AND image_type = 'slide' LIMIT 1");
            $price = DB::select("SELECT price FROM item_details WHERE item_id = '".$item->id."' order by price limit 1");
            $item->image = $image[0]->image;

            $discounts = DB::select("SELECT * from discounts where active = true and is_deleted = false and item_id = '$item->id' and start_date < '$date' and end_date > '$date'");
            $full_price = $price[0]->price;
            $discount = null;
            $item->discount = DB::select("SELECT * from discounts where active = true and is_deleted = false and item_id = '$item->id' and is_default = true order by created_at desc limit 1")[0]->discount;
            if(sizeof($discounts) > 0){
                $item->discount_limit_date = $discounts[0]->end_date;
                $item->discount = $discounts[0]->discount;
                $discount = round(($full_price * $item->discount) / 100,2 );
                $item->price = [
                    "USD" => $full_price - $discount,
                    "KHR" => ($full_price - $discount) * $exchange_rate
                ];
            }
            else{
                $item->discount_limit_date = null;
                $item->price = [
                    "USD" => $full_price,
                    "KHR" => $full_price * $exchange_rate
                ];
            }
            $item->full_price = [
                "USD" => $full_price,
                "KHR" => $full_price * $exchange_rate
            ];
            $item->rate = rand(0, 1000);
            $item->star = mt_rand(0 * 2, 5 * 2) / 2;
        }
        $last_page = ceil($total / $limit);
        return MobileFormatService::formatWithPagination($items,'items',$page,$last_page,$limit,$total);
    }


    public function getItemDetailById(Request $request){
        $validator = Validator::make($request->all(),[
            'id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }
        $user_id = $request->user_id ? $request->user_id : null;
        $id = $request->id ? $request->id : null;
        $itemView = Item::where('id','=',$id)->first();
        $itemView->view++;
        $itemView->save();
        $item = DB::select("SELECT i.id AS id, i.name AS name, i.description as description,i.shop_id as shop_id
                            FROM items i 
                            where id = '".$id."'")[0];
        $exchange_rate = DB::select("select exchange_rate from exchange_rates where money_type = 'KHR' order by created_at desc limit 1")[0]->exchange_rate;
        $bonus = DB::select("select bonus from bonuses order by created_at desc limit 1")[0]->bonus;
        $userWishList = null;
        if($user_id){
            $userWishList = UserWishList::where('item_id','=',$item->id)->where('user','=',$user_id)->first();
        }
        if($item){
            if($userWishList){
                $item->userWishList = true;
            }else{
                $item->userWishList = false;
            }
            $price = DB::select("SELECT price FROM item_details WHERE item_id = '".$item->id."' order by price limit 1");
            $date = date("Y-m-d H:i:s");
            $discounts = DB::select("SELECT * from discounts where active = true and is_deleted = false and is_default = false and item_id = '$item->id' and start_date < '$date' and end_date > '$date'");
            $full_price = $price[0]->price;
            $discount = null;
            $item->discount = DB::select("SELECT * from discounts where active = true and is_deleted = false and item_id = '$item->id' and is_default = true order by created_at desc limit 1")[0]->discount;
            if(sizeof($discounts) > 0){
                $item->discount_limit_date = $discounts[0]->end_date;
                $item->discount = $discounts[0]->discount;
                $discount = round(($full_price * $item->discount) / 100,2 );
                $item->price = [
                    "USD" => $full_price - $discount,
                    "KHR" => ($full_price - $discount) * $exchange_rate
                ];
            }
            else{
                $item->discount_limit_date = null;
                $discount = round(($full_price * $item->discount) / 100,2 );
                $item->price = [
                    "USD" => $full_price - $discount,
                    "KHR" => ($full_price - $discount) * $exchange_rate
                ];
            }
            $item->full_price = [
                "USD" => $full_price,
                "KHR" => $full_price * $exchange_rate
            ];
            $price_after_discount = $item->price['USD'];
            $bonusUSD = round(($price_after_discount * $bonus) / 100,2);
            $bonusKHR = $bonusUSD * $exchange_rate;
            $item->bonus = [
                "USD" => $bonusUSD,
                "KHR" => $bonusKHR
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
                $full_price_itd = $itd->price;
                $discount = round(($full_price_itd * $item->discount) / 100,2 );
                $price_after_discount_itd = $full_price_itd - $discount;
                $itd->price = [
                    "USD" => $price_after_discount_itd,
                    "KHR" => $price_after_discount_itd * $exchange_rate
                ];
                $itd->full_price = [
                    "USD" => $full_price_itd,
                    "KHR" => $full_price_itd * $exchange_rate
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
        $date = date("Y-m-d H:i:s");
        $item = DB::select("SELECT i.id,i.name,itd.price,d.discount FROM items i
                    INNER JOIN item_details itd ON itd.item_id = i.id
                    INNER JOIN discounts d ON d.item_id = i.id
                    WHERE i.id = '".$id."'
                    AND ((d.start_date < '".$date."' AND d.end_date > '".$date."') OR d.is_default = true)
                    AND d.is_deleted = false
                    ORDER BY itd.price,d.end_date DESC
                    LIMIT 1")[0];
        $price = $item->price - (($item->price * $item->discount)/100);
        $item_lower_count = DB::select("SELECT i.id,i.name,itd.price,d.discount FROM items i
                    INNER JOIN item_details itd ON itd.item_id = i.id
                    INNER JOIN discounts d ON d.item_id = i.id
                    WHERE i.id <> '".$id."'
                    AND (itd.price - ((itd.price * d.discount)/100)) < ".$price."
                    AND i.id <> '".$item->id."'
                    AND i.name = '".$item->name."'
                    AND ((d.start_date < '".$date."' AND d.end_date > '".$date."') OR d.is_default = true)
                    AND d.is_deleted = false
                    ORDER BY itd.price,d.end_date DESC");
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
                $discounts = DB::select("SELECT * from discounts where active = true and is_deleted = false and item_id = '$item->id' and start_date < '$date' and end_date > '$date'");
                $full_price = $price[0]->price;
                $discount = null;
                $item->discount = DB::select("SELECT * from discounts where active = true and is_deleted = false and item_id = '$item->id' and is_default = true order by created_at desc limit 1")[0]->discount;
                if(sizeof($discounts) > 0){
                    $item->discount_limit_date = $discounts[0]->end_date;
                    $item->discount = $discounts[0]->discount;
                    $discount = round(($full_price * $item->discount) / 100,2 );
                    $item->price = [
                        "USD" => $full_price - $discount,
                        "KHR" => ($full_price - $discount) * $exchange_rate
                    ];
                }
                else{
                    $item->discount_limit_date = null;
                    $discount = round(($full_price * $item->discount) / 100,2 );
                    $item->price = [
                        "USD" => $full_price - $discount,
                        "KHR" => ($full_price - $discount) * $exchange_rate
                    ];
                }
                $item->full_price = [
                    "USD" => $full_price,
                    "KHR" => $full_price * $exchange_rate
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
    public function getUserWishList(Request $request){
        $limit = $request->limit ? (int)$request->limit : 10;
        $page = $request->page ? (int)$request->page : 1;
        $offset = $limit * ($page - 1);
        $user = $request->user();
        $userWishLists = DB::select("select item_id from user_wish_lists where user_id = '$user->id' order by created_at limit ".$limit." offset ".$offset);
        $ids = [];
        foreach ($userWishLists as $userWishList){
            array_push($ids,$userWishList->item_id);
        }
        $exchange_rate = DB::select("select exchange_rate from exchange_rates where money_type = 'KHR' order by created_at desc limit 1")[0]->exchange_rate;
        $items = Item::whereIn('id',$ids)->select('id','name')->get();
        $total = UserWishList::where('user_id','=',$user->id)->count();
        foreach ($items as $item){
            $image = DB::select("SELECT CONCAT('".env('APP_URL')."','/',url) as image FROM files WHERE module_id = '".$item->id."' AND image_type = 'slide' LIMIT 1");
            $price = DB::select("SELECT price FROM item_details WHERE item_id = '".$item->id."' order by price limit 1");
            $item->image = $image[0]->image;
            $date = date("Y-m-d H:i:s");
            $discounts = DB::select("SELECT * from discounts where active = true and is_deleted = false and item_id = '$item->id' and start_date < '$date' and end_date > '$date'");
            $full_price = $price[0]->price;
            $discount = null;
            $item->discount = 0;
            if(sizeof($discounts) > 0){
                $item->discount_limit_date = $discounts[0]->end_date;
                $item->discount = $discounts[0]->discount;
                $discount = round(($full_price * $item->discount) / 100,2 );
                $item->price = [
                    "USD" => $full_price - $discount,
                    "KHR" => ($full_price - $discount) * $exchange_rate
                ];
            }
            else{
                $item->discount_limit_date = null;
                $discount = round(($full_price * $item->discount) / 100,2 );
                $item->price = [
                    "USD" => $full_price - $discount,
                    "KHR" => ($full_price - $discount) * $exchange_rate
                ];
            }
            $item->full_price = [
                "USD" => $full_price,
                "KHR" => $full_price * $exchange_rate
            ];
            $item->rate = rand(0, 1000);
            $item->star = mt_rand(0 * 2, 5 * 2) / 2;
        }
        $last_page = ceil($total / $limit);
        return MobileFormatService::formatWithPagination($items,'items',$page,$last_page,$limit,$total);
    }
    public function addUserWishList(Request $request){
        $validator = Validator::make($request->all(),[
            'item_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }
        $result = '';
        $item_id = $request->item_id;
        $user = $request->user();
        $userWishItem = UserWishList::where('user_id','=',$user->id)->where('item_id','=',$item_id)->first();
        if($userWishItem){
            $result = 'Item already added';
        }else{
            $userWishItem = new UserWishList();
            $userWishItem->user_id = $user->id;
            $userWishItem->item_id = $item_id;
            $userWishItem->save();
            $result = 'Item successfully added';
        }
        return MobileFormatService::formatWithoutPagination($result);
    }
    public function removeUserWishList(Request $request){
        $validator = Validator::make($request->all(),[
            'item_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }
        $user = $request->user();
        $userWishList = UserWishList::where('item_id','=',$request->item_id)->where('user_id','=',$user->id)->first();
        $userWishList->delete();
        return MobileFormatService::formatWithoutPagination('Item successfully removed');
    }
    public function search(Request $request){
        $limit = $request->limit ? (int)$request->limit : 10;
        $page = $request->page ? (int)$request->page : 1;
        $user = $request->user_id ? $request->user_id : null;
        $search = $request->search ? $request->search : '';
        $searches = '';
        $date = date("Y-m-d H:0:0");
        if($user){
            $user_search = UserSearch::where('user_id','=',$user)->where('search','=',$search)->first();
            if($user_search){
                $user_search->count++;
                $user_search->save();
            }else{
                $user_search = new UserSearch();
                $user_search->search = $search;
                $user_search->user_id = $user;
                $user_search->count = 1;
                $user_search->save();
            }
        }
        $global_search = GlobalSearch::where('search','=',$search)->first();
        if($global_search){
            $global_search->count ++;
            $global_search->save();
        }else{
            $global_search = new GlobalSearch();
            $global_search->search = $search;
            $global_search->count = 1;
            $global_search->save();
        }
        $global_search_rank = GlobalSearchRank::where('search','=',$search)->where('date_search','=',$date)->first();
        if($global_search_rank){
            $global_search_rank->count ++;
            $global_search_rank->save();
        }else{
            $global_search_rank = new GlobalSearchRank();
            $global_search_rank->search = $search;
            $global_search_rank->count = 1;
            $global_search_rank->date_search = $date;
            $global_search_rank->save();
        }
        foreach (explode(" ", $search) as $s){
            $searches.= " And name like '%".$s."%'";
        }
        $exchange_rate = DB::select("select exchange_rate from exchange_rates where money_type = 'KHR' order by created_at desc limit 1")[0]->exchange_rate;
        $items = DB::select("SELECT i.id AS id, i.name AS name, i.discount as discount
                            FROM items i where is_deleted = false ".$searches."  LIMIT ".$limit." offset ".(($page - 1) * 10));
        $total = DB::select("SELECT i.id AS id, i.name AS name
                            FROM items i where is_deleted = false ".$searches);
        foreach ($items as $item){
            $image = DB::select("SELECT CONCAT('".env('APP_URL')."','/',url) as image FROM files WHERE module_id = '".$item->id."' AND image_type = 'slide' LIMIT 1");
            $price = DB::select("SELECT price FROM item_details WHERE item_id = '".$item->id."' order by price limit 1");
            $item->image = $image[0]->image;
            $date = date("Y-m-d H:i:s");
            $discounts = DB::select("SELECT * from discounts where active = true and is_deleted = false and item_id = '$item->id' and start_date < '$date' and end_date > '$date'");
            $full_price = $price[0]->price;
            if(sizeof($discounts) > 0){
                $item->discount_limit_date = $discounts[0]->end_date;
                $item->discount = $discounts[0]->discount;
                $discount = round(($full_price * $item->discount) / 100,2 );
                $item->price = [
                    "USD" => $full_price - $discount,
                    "KHR" => ($full_price - $discount) * $exchange_rate
                ];
            }
            else{
                $item->discount_limit_date = null;
                $discount = round(($full_price * $item->discount) / 100,2 );
                $item->price = [
                    "USD" => $full_price - $discount,
                    "KHR" => ($full_price - $discount) * $exchange_rate
                ];
            }
            $item->full_price = [
                "USD" => $full_price,
                "KHR" => $full_price * $exchange_rate
            ];
            $item->rate = rand(0, 1000);
            $item->star = mt_rand(0 * 2, 5 * 2) / 2;
        }
        $last_page = ceil(sizeof($total) / $limit);
        return MobileFormatService::formatWithPagination($items,'items',$page,$last_page,$limit,sizeof($total));
    }
    public function getRecentSearch(Request $request){
        $user_id = $request->user_id ? $request->user_id : null;
        if($user_id){
            $recent_searches = UserSearch::where('user_id','=',$user_id)->orderBy('updated_at', 'DESC')->limit(10)->get();
            $result = [];
            foreach ($recent_searches as $recentSearch){
                array_push($result,$recentSearch->search);
            }
            return MobileFormatService::formatWithoutPagination($result);
        }
    }
    public function getRecommendSearch(Request $request){
        $user_id = $request->user_id ? $request->user_id : null;
        $last_search = $request->last_search ? $request->last_search : '';
        $search_texts = explode(" ", $last_search);
        $search_text = '';
        if(sizeof($search_texts) > 0){
            $search_text = $search_texts[0];
        }
        if($user_id){
            $recent_searches = UserSearch::where('user_id','=',$user_id)->orderBy('updated_at', 'DESC')->limit(1)->get();
            if(sizeof($recent_searches) > 0){
                $search_text =  $recent_searches[0]->search;
            }
        }
        $result = [];
        $recommends = GlobalSearch::where('search','like','%'.$search_text.'%')->where('search','<>',$search_text)->orderBy('count', 'DESC')->limit(10)->get();
        foreach ($recommends as $recommend){
            array_push($result,$recommend->search);
        }
        return MobileFormatService::formatWithoutPagination($result);
    }
    public function getSearchRank(Request $request){
        $limit = $request->limit ? $request->limit : 30;
        $result = [];
        GlobalSearchRankController::getGlobalSearchRankRecursive($result,$limit,0);
        return MobileFormatService::formatWithoutPagination($result);
    }

}
