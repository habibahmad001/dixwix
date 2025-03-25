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
use App\Models\Group;
use QR_Code\Encoder\ErrorCorrection\Rs;

class HowDoesItController extends Controller
{
    // Method to show the "How It Works" page
   public function index(Request $request)
    {
        $data['banner_heading'] = 'How It Works';
        $data['banner_text'] = 'Here is a brief description of how our platform works. You can easily share and rent items with your friends and community.';
        $data['is_banner_link'] = true;
        $data['banner_link'] = '/signup';  // Link to the signup page
        $data['banner_text'] = "Let's get started";
        $data['background-class'] = "header";  // Class for styling (can be changed as needed)

        return view('how_does_it_work', compact('data'));
    }public function tos(Request $request)
    {
        $data['banner_heading'] = 'Terms of Service';
        $data['banner_text'] = 'DixWix Terms of Servic.';
        $data['is_banner_link'] = false;
        $data['banner_link'] = '/signup';  // Link to the signup page
        $data['banner_text'] = "";
        $data['background-class'] = "header";  // Class for styling (can be changed as needed)

        return view('tospage', compact('data'));
    }
}
