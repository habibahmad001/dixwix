<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Contact as Contact;
use App\Models\Grouptype as Grouptype;
use App\Models\User as User;
use Illuminate\Support\Facades\Mail;
use App\Mail\MailService;
use QR_Code\Encoder\ErrorCorrection\Rs;

class MembershipController extends Controller
{
    public function MembershipPage(Request $request){
        $data['title'] = "Membership";
        $data['template'] = "membership";
        $data['script_file'] = "membership";
        $user = User::find(Auth::user()->id);
        $usermembership = $user->membership;
        if(count($usermembership)>0){
            $data["membership_plan_id"] = $usermembership[0]->plan_id;
        }
        return view('with_login_common', compact('data'));
    }

    public function ActivateMembership(Request $request, $plan_id){
        $user = User::find(Auth::user()->id);
        $user->membership()->create([
            "plan_id" => $plan_id,
            "is_active" => 1,
            "start_date" => date("Y-m-d H:i:s"),
            "created_by" => Auth::user()->id,
            "created_at" => date("Y-m-d H:i:s"),
        ]);
        $user->group_type = 1;
        $user->save();
        return redirect()->route('membership');
    }
}
