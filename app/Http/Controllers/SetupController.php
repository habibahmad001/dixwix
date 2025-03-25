<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Group as Group;
use Artisan;

class SetupController extends Controller
{
    public function Main(Request $request)
    {
        Artisan::call('cache:clear');
        $groups = Group::get();
        foreach($groups as $group){
            try{
                $group->groupmembers()->create([
                    "member_id" => $group->created_by,
                    "status" => "added",
                    "created_by" => $group->created_by,
                    "created_at" => date("Y-m-d H:i:s"),
                ]);
            }catch(\Exception $ex){
            }
        }
        return json_encode(["status"=>200,"message"=>"Project Initiated Successfully !"]);
    }
}
