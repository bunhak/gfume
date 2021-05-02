<?php

namespace App\Http\Controllers;

use App\Models\Bonus;
use Illuminate\Http\Request;

class BonusController extends Controller
{
    public function mockData(Request $request){
        $user_id = $request->user()->id;
        $bonus = new Bonus();
        $bonus->bonus = 1;
        $bonus->created_by = $user_id;
        $bonus->save();
        return response()->json($bonus, 400);
    }
}
