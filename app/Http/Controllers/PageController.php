<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller as Controller;
use App\Mail\MailService;
use App\Models\BlogPost;
use App\Models\Contact as Contact;
use App\Models\Group;
use App\Models\Grouptype as Grouptype;
use App\Models\User as User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class PageController extends Controller
{
    public function Homepage(Request $request)
    {
        $data['groups'] = Group::where('status', 1)->orderBy('created_at', 'desc')->get();
        $data['title'] = 'Home';
        $data['template'] = 'home';
        $data['is_banner'] = true;
        //$data['banner_heading'] = 'DixWix helps users share<br>resources and expertise with<br>friends and neighbors';
        $data['banner_heading'] = 'The Private Peer-to-Peer Rental Platform';
        $data['is_banner_link'] = true;
        $data['banner_link'] = route('signup');
        $data['banner_text'] = "Let's get started";
        $data['background-class'] = "header";
        return view('without_login_common', compact('data'));
    }

    public function Login(Request $request)
    {
        $data['title'] = 'Login';
        return view('login', compact('data'));
    }

    public function Signup(Request $request)
    {
        $data['title'] = 'Sign Up';
        return view('signup', compact('data'));
    }

    public function SignupViaGroup(Request $request, $referrer_id, $group_id, $group_type_id)
    {
        // Handle guest users (referrer_id = 0)
        if ($referrer_id == 0) {
            $referrer = 'Guest'; // Placeholder or alternative behavior
        } else {
            $referrer = User::find($referrer_id); // Fetch referrer details if available
        }

        // Get the email from the session or URL
        $email_id = session('invite_email_id', ''); // If session is used to store the invite email

        $data['title'] = 'Sign Up';
        $data['group_id'] = $group_id;
        $data['group_type_id'] = $group_type_id;
        $data['referrer'] = $referrer;
        $data['email_id'] = $email_id; // Pass the email to the view

        return view('signup', compact('data'));
    }

    public function Contactus(Request $request)
    {
        $data['title'] = 'Contact Us';
        $data['template'] = 'contactus';
        $data['is_banner'] = true;
        $data['banner_heading'] = 'Contact Us';
        $data['banner_text'] = "A human always responds, no bots!";
        $data['background-class'] = "header-support support contactp";
        return view('without_login_common', compact('data'));
    }

    public function SaveContactUs(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'comment' => 'required|string|max:5000',
        ]);

        try {

            Contact::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'comment' => $validatedData['comment'],
            ]);

            $formData = [
                'message' => 'Query from Contact Us',
                'email' => "You have received a new query:<br/><br/>
                Name: {$validatedData['name']}<br/>
                Email: {$validatedData['email']}<br/>
                Query: {$validatedData['comment']}<br/><br/>
                This is an automated message from the Dixwix contact form.",
            ];

            $mailTo = env('CONTACT_EMAIL', 'support@dixwix.com');
            Mail::to($mailTo)->send(new MailService($formData));

            return back()->with('success', 'Your concerns have been shared with Dixwix admin.');

        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while submitting your concerns. Please try again later.');
        }
    }

    public function SelectUserType(Request $request)
    {
        $data = array();
        $group_types = Grouptype::get();
        $data['title'] = 'User Type';
        $data["group_types"] = $group_types;
        return view('usertype', compact('data'));
    }

    public function SaveUserType(Request $request, $type_id)
    {
        $user = User::find(Auth::user()->id);
        $user->group_type = $type_id;
        $user->save();
        Auth::login($user, true);
        return redirect()->route("dashboard");
    }

    public function HowItWorks(Request $request)
    {
        $data['title'] = 'How It Works';
        $data['template'] = 'howItWork';
        $data['is_banner'] = true;
        $data['is_banner_link'] = true;
        $data['banner_link'] = route('signup');
        $data['banner_heading'] = 'How It Works';
        $data['banner_text'] = "Pellentesque quis lectus sagittis, gravida erat id";
        $data['background-class'] = "header";

        return view('without_login_common', compact('data'));
    }

    public function Pricing()
    {
        $data['title'] = 'Pricing';
        $data['template'] = 'pricing';
        $data['is_banner'] = true;
        $data['is_banner_link'] = true;
        $data['banner_link'] = route('signup');
        $data['banner_heading'] = 'Pricing';
        $data['banner_text'] = "Pellentesque quis lectus sagittis, gravida erat id";
        $data['background-class'] = "header";

        return view('without_login_common', compact('data'));
    }

    public function Faq()
    {
        $data['title'] = 'Faq';
        $data['template'] = 'faq';
        $data['is_banner'] = true;
        $data['is_banner_link'] = true;
        $data['banner_link'] = route('signup');
        $data['banner_heading'] = 'Faq';
        $data['banner_text'] = "Pellentesque quis lectus sagittis, gravida erat id";
        $data['background-class'] = "header";

        return view('without_login_common', compact('data'));
    }

    public function Blog(Request $request)
    {
        $data['title'] = 'Blog';
        $data['template'] = 'blog';
        $data['is_banner'] = true;
        $data['is_banner_link'] = true;
        $data['banner_link'] = route('signup');
        $data['banner_heading'] = 'Blog';
        $data['banner_text'] = "The Heart of Knowledge and Imagination";
        $data['background-class'] = "header";

        $search = $request->search;

        $posts = BlogPost::when($search, function($query) use ($search) {
            $query->where("title", "like", "%$search%")
            ->orWhere("slug", "like", "%$search%");
        })
        ->with('user')->where("status", 'published')->latest('id')->get();

        return view('without_login_common', compact('data', 'posts'));
    }

    public function Security()
    {
        $data['title'] = 'Security';
        $data['template'] = 'security';
        $data['is_banner'] = true;
        $data['is_banner_link'] = true;
        $data['banner_link'] = route('signup');
        $data['banner_heading'] = 'Security';
        $data['banner_text'] = "Pellentesque quis lectus sagittis, gravida erat id";
        $data['background-class'] = "header";

        return view('without_login_common', compact('data'));
    }
    public function ourstory()
    {
        $data['title'] = 'Our Story';
        $data['template'] = 'ourstory';
        $data['is_banner'] = true;
        $data['is_banner_link'] = true;
        $data['banner_link'] = route('signup');
        $data['banner_heading'] = 'Our Story';
        $data['banner_text'] = "Pellentesque quis lectus sagittis, gravida erat id";
        $data['background-class'] = "header";

        return view('without_login_common', compact('data'));
    }public function privacypolicy()
    {
        $data['title'] = 'Privacy';
        $data['template'] = 'privacypolicy';
        $data['is_banner'] = true;
        $data['is_banner_link'] = true;
        $data['banner_link'] = route('signup');
        $data['banner_heading'] = 'Privacy';
        $data['banner_text'] = "Pellentesque quis lectus sagittis, gravida erat id";
        $data['background-class'] = "header";

        return view('without_login_common', compact('data'));
    }public function careers()
    {
        $data['title'] = 'Careers';
        $data['template'] = 'careers';
        $data['is_banner'] = true;
        $data['is_banner_link'] = true;
        $data['banner_link'] = route('signup');
        $data['banner_heading'] = 'Careers';
        $data['banner_text'] = "Pellentesque quis lectus sagittis, gravida erat id";
        $data['background-class'] = "header";

        return view('without_login_common', compact('data'));
    }public function yoursecurity()
    {
        $data['title'] = 'Your Security Matters';
        $data['template'] = 'yoursecurity';
        $data['is_banner'] = true;
        $data['is_banner_link'] = true;
        $data['banner_link'] = route('signup');
        $data['banner_heading'] = 'Your Security Matters';
        $data['banner_text'] = "Pellentesque quis lectus sagittis, gravida erat id";
        $data['background-class'] = "header";

        return view('without_login_common', compact('data'));
    }public function sitemap()
    {
        $data['title'] = 'Site Map';
        $data['template'] = 'sitemap';
        $data['is_banner'] = true;
        $data['is_banner_link'] = true;
        $data['banner_link'] = route('signup');
        $data['banner_heading'] = 'Site Map';
        $data['banner_text'] = "Pellentesque quis lectus sagittis, gravida erat id";
        $data['background-class'] = "header";

        return view('without_login_common', compact('data'));
    }

    public function Support()
    {
        $data['title'] = 'Support';
        $data['template'] = 'support';
        $data['is_banner'] = true;
        $data['is_banner_link'] = false;
        $data['banner_link'] = route('signup');
        $data['banner_heading'] = 'How can we help?';
        $data['banner_text'] = "";
        $data['background-class'] = "header";

        return view('without_login_common', compact('data'));
    }
}
