<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller as Controller;
use Illuminate\Support\Facades\DB;
use App\Mail\MailService;
use App\Models\Comment;
use App\Models\Country;
use App\Models\Entries;
use App\Models\Group as Group;
use App\Models\Groupmember;
use App\Models\Grouptype as Grouptype;
use App\Models\GroupUserInvited;
use App\Models\ItemRejectedRequest;
use App\Models\LoanRule;
use App\Models\Post;
use App\Models\Setting;
use App\Models\Ticket;
use App\Models\TrustScore;
use App\Models\Type as Type;
use App\Models\User as User;
use App\Notifications\GeneralNotification;
use Carbon\Carbon;
use Exception;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;





class GroupController extends Controller
{
    public function AddGroup($id = null)
    {
        $retdata         = [];
        $retdata["mode"] = "add";
        if (isset($id)) {
            $group = Group::findOrFail($id);
            if (! auth()->user()->hasRole('admin') && $group['created_by'] != auth()->id()) {
                abort(403);
            }
            $retdata["group"]    = $group;
            $retdata["mode"]     = "edit";
            $retdata["group_id"] = $id;
        } else {
            $user = auth()->user();
            if (count($user->membership) > 0) {
                if (count($user->createdgroups) >= $user->membership[0]->plan->allowed_groups) {
                    $retdata["group_limit_reached"] = true;
                }
            }
        }
        return $this->ReturnToAddPage($retdata);
    }

    public function StoreGroup(Request $request)
{
    $retdata             = [];
    $data                = $request->input('group');
    $mode                = $request->input('mode');
    $group_id            = $request->input('group_id');
    $retdata['mode']     = $mode;
    $retdata['group_id'] = $group_id;

    // Default location and cover image for "add" mode
    if ($mode === "add") {
        // Set default location if none is provided
        if (empty($data['locations'])) {
            $data['locations'] = ['community']; // default location
        }

        // Set a default cover image if none is provided
        if (empty($data['group_picture'])) {
            // Provide a default image URL (change the path as necessary)
            $data['group_picture'] = 'media/logo.png'; // Example path, change this to the actual default image
        }
    }

   /* $request->validate(
        [
            "group.title"       => "required|string",
            "group.locations"   => "array",
            "group.description" => "",
            "group.status"      => "required",
            "group_picture"     => $mode === "add" ? "image|mimes:jpeg,png,jpg,gif" : "nullable|image|mimes:jpeg,png,jpg,gif",
        ],
        [
            "group.title.required"       => "The title field is required.",
            // "group.description.required" => "The description field is required.",
            "group.title.string"         => "The title must be a valid string.",
            // "group.locations.required"   => "Please provide at least one location.",
            // "group.locations.array"      => "The locations must be an array.",
            "group.status.required"      => "The status field is required.",
            // "group_picture.required"     => "A group picture is required for adding a new group.",
            // "group_picture.image"        => "The group picture must be an image.",
            // "group_picture.mimes"        => "The group picture must be a file of type: jpeg, png, jpg, gif.",
            // "group_picture.max"          => "The group picture must not exceed 2MB in size.",
        ]
    );*/
    $request->validate(
        [
            "group.title"       => "required|string",
            "group.locations"   => "array", // You could make this required if necessary
            "group.description" => "nullable|string", // Optional but you can set a string validation if required
            "group.status"      => "required",
            "group_picture"     => $mode === "add" ? "image|mimes:jpeg,png,jpg,gif|max:2048" : "nullable|image|mimes:jpeg,png,jpg,gif|max:2048", // Image size limit
        ],
        [
            "group.title.required"       => "The title field is required.",
            "group.title.string"         => "The title must be a valid string.",
            "group.status.required"      => "The status field is required.",
            "group_picture.required"     => "A group picture is required for adding a new group.",
            "group_picture.image"        => "The group picture must be an image.",
            "group_picture.mimes"        => "The group picture must be a file of type: jpeg, png, jpg, gif.",
            "group_picture.max"          => "The group picture must not exceed 2MB in size.",
        ]
    );


    if ($request->hasFile('group_picture')) {
        $path                  = $request->file('group_picture')->store('group_pictures');
        $data["group_picture"] = $path;
    }

    $group = null;
    $model = new Group();

    if ($mode == "add") {

        $file_path = 'barcodes/' . date("Ymd_His") . '.png';
        $save_path = "storage/{$file_path}";

        \QRCode::text($data['title'] ?? '')->setOutfile($save_path)->png();
        $qrcode_url = \Storage::disk('local')->url($file_path);

        $data["qrcode_url"] = $qrcode_url;
        $data["created_at"] = date("Y-m-d H:i:s");
        $data["created_by"] = Auth::user()->id;

        $group = $model->add($data);

        $group->groupmembers()->create([
            "member_id"  => Auth::user()->id,
            "status"     => "added",
            "created_by" => Auth::user()->id,
            "activated"  => 1,
            "created_at" => date("Y-m-d H:i:s"),
        ]);
    } else if ($mode == "edit") {
        $groupN = Group::find($group_id);
        if (empty($data['group_picture']) && ! empty($groupN->group_picture)) {
            $data['group_picture'] = $groupN->group_picture;
        }
        $group = $model->change($data, $group_id);
    }

    if (! is_object($group)) {
        if ($mode == "edit") {
            $errors = \App\Message\Error::get('group.change');
        } else {
            $errors = \App\Message\Error::get('group.add');
        }
        if (count($errors) == 0) {
            if ($mode == "edit") {
                $errors = \App\Message\Error::get('group.change');
            } else {
                $errors = \App\Message\Error::get('group.add');
            }
        }
    }
    if (isset($errors) && count($errors) > 0) {
        $message                           = returnErrorMsg($errors);
        $retdata['errs']                   = $errors;
        $retdata['group']                  = $data;
        $retdata['group']['group_picture'] = "";
        $retdata['err_message']            = $message;
        // $this->flashError($retdata['err_message']);
        return $this->ReturnToAddPage($retdata);
    }
    $retdata["success"] = "Group " . ucfirst($mode) . "ed successfully";
    return back()->with('success', $retdata['success']);
}


    public function ReturnToAddPage($retdata = [])
    {
        $data['title']       = ucfirst($retdata["mode"]) . ' Group';
        $data['template']    = 'group.add';
        $data['script_file'] = 'add_item';
        $data['group_types'] = Grouptype::get();
        $data["countries"]   = Country::get();
        return view('with_login_common', compact('data', 'retdata'));
    }

    public function ShowMyGroups(Request $request)
    {
        $data['title']         = 'My Groups';
        $data['template']      = 'group.mylist';
        $data['my_groups']     = Group::where("created_by", Auth::user()->id)->get();
        $my_id                 = Auth::user()->id;
        $data['joined_groups'] = Group::whereHas("addedmembers", function ($q) use ($my_id) {
            $q->where("member_id", $my_id);
        })->where("created_by", "!=", $my_id)->get();
        $data['script_file'] = 'group_listing';

        foreach ($data['joined_groups'] as $group) {
            $group['current_user'] = user_in_group($group);
        }

        return view('with_login_common', compact('data'));
    }

    public function ShowAllGroups(Request $request)
    {
        $data['title']       = 'All Groups';
        $data['template']    = 'group.mylist';
        $data['my_groups']   = Group::get();
        $data['script_file'] = 'group_listing';

        return view('with_login_common', compact('data'));
    }

    public function ShowLenderGroups(Request $request)
    {
        $data['title']     = 'Lender Groups';
        $data['template']  = 'group.mylist';
        $data['my_groups'] = Group::whereHas("grouptype", function ($q) {
            $q->where("name", "=", "Lender");
        })->get();

        return view('with_login_common', compact('data'));
    }

    public function ShowBorrowerGroups(Request $request)
    {
        $data['title']     = 'Borrower Groups';
        $data['template']  = 'group.mylist';
        $data['my_groups'] = Group::whereHas("grouptype", function ($q) {
            $q->where("name", "=", "Borrower");
        })->get();

        return view('with_login_common', compact('data'));
    }

    public function ReloadGroupsByType(Request $request)
    {
        $type_id  = $request->input('group_type_id');
        $groups   = Group::where("group_type_id", $type_id)->select(['id', 'title'])->get();
        $ret_data = "";
        foreach ($groups as $group) {
            $ret_data .= '<option value="' . $group["id"] . '">' . $group["title"] . '</option>';
        }
        return $ret_data;
    }

    public function ReloadCatsByType(Request $request)
    {
        $type_id  = $request->input('group_type_id');
        $cats     = Type::where("group_type_id", $type_id)->select(['id', 'name'])->get();
        $ret_data = "";
        foreach ($cats as $cat) {
            $ret_data .= '<option value="' . $cat["id"] . '">' . $cat["name"] . '</option>';
        }
        return $ret_data;
    }

    public function GetMembersToAdd(Request $request, $group_id, $group_type_id)
    {
        //<td><input type="email" id="email_to_invite" placeholder="Enter Email id to invite"/></td><td><input type="button" onclick="invite_by_email(\'' . $group_id . '\',\'' . $group_type_id . '\')" value="Invite"/></td>
        $users   = User::role("user")->where("id", "!=", Auth::user()->id)->select(['id', 'name', 'email', 'id AS user_id'])->get();
        $retData = '';
        if (count($users) > 0) {
            $retData .= '
            <p id="res_msg"></p>
            <table><tbody><tr></tr>
            <tr><td colspan="2"><span id="response_message" style="color:red;font-size:small;"></span></td></tr></tbody>
            <table style="width:100%"><thead><tr><th>Name</th><th>Invite</th><th>Remove</th></tr></thead><tbody>';
            foreach ($users as $user) {

                $group_member = Groupmember::where("member_id", $user->id)->where("group_id", $group_id)->get();
                $checked      = '';
                if (count($group_member) > 0) {
                    $checked = ($group_member[0]['member_role'] == "admin") ? "checked" : "";
                }
                $activated = '';
                if (count($group_member) > 0) {
                    $activated = ($group_member[0]['activated'] == 1) ? "checked" : "";
                }
                $link = '
                <a href="javascript:void(0)" class="dark-btn btn link_with_img" onclick="addMemberToGroup(\'' . $user["id"] . '\', \'' . $group_id . '\',\'' . $group_type_id . '\')">
                    <img src="' . url("assets/media/add-circle-outline.png") . '">
                </a>';

                $removeMember = '<a href="javascript:void(0)" class="dark-btn btn link_with_img" onclick="deleteUserFromGroup(\'' . $user["id"] . '\', \'' . $group_id . '\',\'' . $group_type_id . '\')">Delete</a>';
                $makeAdmin    = '<label class="switch"><input id="member_' . $user["id"] . '" onchange="updateMember(\'' . $user["id"] . '\', \'' . $group_id . '\',\'' . $group_type_id . '\')" type="checkbox" ' . $checked . '><span class="slider"></span></label>';
                $activate     = '<label class="switch"><input id="member_status_' . $user["id"] . '" onchange="updateMemberStatus(\'' . $user["id"] . '\', \'' . $group_id . '\',\'' . $group_type_id . '\')" type="checkbox" ' . $activated . '><span class="slider"></span></label>';
                //$userRemoveFromGroup = '<span class="dark-btn btn link_with_img" style="background:gray">can\'t delete</span>';
                $userRemoveFromGroup = '';
                if (count($group_member) > 0) {
                    if ($group_member[0]->status == 'added') {
                        $userRemoveFromGroup = $removeMember;
                    }
                    $link = '<span class="item-type-book">' . $group_member[0]->status . '</span>';
                }

                $retData .= '<tr>
                <td>' . $user['name'] . '</td>

                <td>' . $link . '</td>
                <td>' . $userRemoveFromGroup . '</td>
                </tr>';
            }
            $retData .= '</tbody></table>';
        } else {
            $retData = 'No User available !';
        }
        return json_encode(["success" => true, "data" => $retData, "message" => "Potential Member List fetched."]);
    }

    public function searchUsers(Request $request)
    {
        //<td><input type="email" id="email_to_invite" placeholder="Enter Email id to invite"/></td><td><input type="button" onclick="invite_by_email(\'' . $group_id . '\',\'' . $group_type_id . '\')" value="Invite"/></td>
        $group_id      = $request->group_id;
        $group_type_id = $request->group_type_id;
        $searchQuery   = $request->search_user;
        $users         = collect();
        if (! empty($searchQuery)) {
            $users = User::role("user")
                ->where("id", "!=", Auth::user()->id)
                ->where(function ($query) use ($searchQuery) {
                    $query->where('name', 'LIKE', "%{$searchQuery}%")
                        ->orWhere('email', 'LIKE', "%{$searchQuery}%");
                })
                ->select(['id', 'name', 'email'])
                ->get();
        }
        $retData = '';
        if (count($users) > 0) {
            $retData .= '
                <p id="res_msg"></p>
                <table><tbody><tr></tr>
                <tr><td colspan="2"><span id="response_message" style="color:red;font-size:small;"></span></td></tr></tbody>
                <table style="width:100%"><thead><tr><th>Name</th><th>Invite</th><th>Remove</th></tr></thead><tbody>';
            foreach ($users as $user) {

                $group_member = Groupmember::where("member_id", $user->id)->where("group_id", $group_id)->get();
                $checked      = '';
                if (count($group_member) > 0) {
                    $checked = ($group_member[0]['member_role'] == "admin") ? "checked" : "";
                }
                $activated = '';
                if (count($group_member) > 0) {
                    $activated = ($group_member[0]['activated'] == 1) ? "checked" : "";
                }
                $link = '
                    <a href="javascript:void(0)" class="dark-btn btn link_with_img" onclick="addMemberToGroup(\'' . $user["id"] . '\', \'' . $group_id . '\',\'' . $group_type_id . '\')">
                        <img src="' . url("assets/media/add-circle-outline.png") . '">
                    </a>';

                $removeMember = '<a href="javascript:void(0)" class="dark-btn btn link_with_img" onclick="deleteUserFromGroup(\'' . $user["id"] . '\', \'' . $group_id . '\',\'' . $group_type_id . '\')">Delete</a>';
                $makeAdmin    = '<label class="switch"><input id="member_' . $user["id"] . '" onchange="updateMember(\'' . $user["id"] . '\', \'' . $group_id . '\',\'' . $group_type_id . '\')" type="checkbox" ' . $checked . '><span class="slider"></span></label>';
                $activate     = '<label class="switch"><input id="member_status_' . $user["id"] . '" onchange="updateMemberStatus(\'' . $user["id"] . '\', \'' . $group_id . '\',\'' . $group_type_id . '\')" type="checkbox" ' . $activated . '><span class="slider"></span></label>';
                //$userRemoveFromGroup = '<span class="dark-btn btn link_with_img" style="background:gray">can\'t delete</span>';
                $userRemoveFromGroup = '';
                if (count($group_member) > 0) {
                    if ($group_member[0]->status == 'added') {
                        $userRemoveFromGroup = $removeMember;
                    }
                    $link = '<span class="item-type-book">' . $group_member[0]->status . '</span>';
                }

                $retData .= '<tr>
                <td>' . $user['name'] . '</td>

                <td>' . $link . '</td>
                <td>' . $userRemoveFromGroup . '</td>
                </tr>';
            }
            $retData .= '</tbody></table>';
        } else {
            $retData = 'No User available !';
        }
        return json_encode(["success" => true, "data" => $retData, "message" => "Potential Member List fetched."]);

    }

    public function AddGroupMember(Request $request)
    {
        try {

            $member_id = $request->input('member_id');
            $group_id  = $request->input('group_id');

            $group = Group::find($group_id);

            if (! $group) {
                throw new \Exception("Group not found");
            }

            $group_member = Groupmember::where('member_id', $member_id)->where('group_id', $group_id)->first();
            if ($group_member) {
                $group_member->update([
                    "member_id"   => $member_id,
                    "status"      => "invited",
                    "member_role" => "user",
                    "created_by"  => Auth::user()->id,
                    "created_at"  => date("Y-m-d H:i:s"),
                ]);
            } else {
                $group_member = $group->groupmembers()->create([
                    "member_id"   => $member_id,
                    "status"      => "invited",
                    "member_role" => "user",
                    "created_by"  => Auth::user()->id,
                    "created_at"  => date("Y-m-d H:i:s"),
                ]);

            }

            $user = User::find($member_id);
            if (! $user) {
                throw new \Exception("User not found");
            }

            $link = "<a href=\"" . route("show-group", ["id" => $group_id]) . "\">link</a>";
            SendEmail($user->email, 'Group Invitation', "You are invited to join the group " . $group->name . ". Click on " . $link . " to confirm.");

            $inviteNotification = [
                'only_database' => true,
                'title'         => 'Group Invitation',
                'type'          => 'group_invitation',
                'subject'       => 'Invited to a group',
                'message'       => "You are invited to join the group {$group->name}",
                'user_id'       => auth()->user()->id,
                'group_id'      => $group_id,
                'member_id'     => $member_id,
                'id'            => $group_member->id,
                'url'           => route("show-group", ["id" => $group_id]),
                'action'        => 'View Group',
            ];

            $user->notify(new GeneralNotification($inviteNotification));

            return json_encode([
                "success"    => true,
                "message"    => "Group Member Added Successfully!",
                "reload_url" => route('get-members-to-add', [
                    "group_id"      => $group->id,
                    "group_type_id" => $group->group_type_id,
                ]),
            ]);

        } catch (\Exception $e) {

            Log::error('Error adding group member: ' . $e->getMessage());

            return response()->json([
                "success" => false,
                "message" => "Smtp Error: " . $e->getMessage(),
            ], 500);
        }
    }

    public function ShowGroup(Request $request, $id)
    {
        $retdata                      = [];
        $retdata['active_tab']        = $request->tab ?? 'details';
        $retdata['requested_members'] = [];

        $standard_load_rule            = Setting::where('name', 'standard_load_rule')->first()->value;
        $retdata["standard_load_rule"] = $standard_load_rule;

        $group = Group::with('grouptype')->where("id", $id)->firstOrFail();

        if ($group->default) {
            if (auth()->check() && auth()->user()->id == $group->created_by) {
                $group->books = $group->books()->with([
                    'group',
                    'category',
                    'entries',
                    'user' => function ($q) {
                        $q->select('id', 'name');
                    },
                ])->where('status_options', 'maintenance')->get();
            } else {
                $group->books = [];
            }
        } else {
            $group->books = $group->books()->with([
                'group',
                'category',
                'entries',
                'user' => function ($q) {
                    $q->select('id', 'name');
                },
            ])->where('status_options', 'maintenance')->get();
        }

        $group = $group->toArray();

        if ($group['created_by'] != auth()->id() && ! $group['status']) {
            abort(404);
        }

        /*$group_id       = $group['id'];
        $enabled_member = Group::whereHas('groupMember', function ($qu) use ($group_id) {
            $qu->where('member_id', auth()->user()->id);
            $qu->where('group_id', $group_id);
            $qu->where('activated', 1);
        })->find($id);*/

        $group_id = $group['id'];
        if (auth()->user()->hasRole('admin')) {
            $enabled_member = Group::find($group_id);
        } else {
            $enabled_member = Group::whereHas('groupMember', function ($qu) use ($group_id) {
                $qu->where('member_id', auth()->user()->id);
                $qu->where('group_id', $group_id);
                $qu->where('activated', 1);
            })->find($group_id);
        }


        $user_status = null;
        if (! auth()->user()->hasRole('admin') && auth()->id() != $group['created_by']) {
            $user_status = Groupmember::where('member_id', auth()->user()->id)
                ->where("status", "added")
                ->where('group_id', $group_id)->first();
        }

        foreach ($group['books'] as &$book) {
            $book['is_reserved']         = $this->hasUserReservedBook($book, auth()->id(), "reserved");
            $book['is_reserved_pending'] = $this->hasUserReservedBook($book, auth()->id(), "pending");
            $book['due_date']            = $this->hasUserReservedBook($book, auth()->id(), "due_date");
            $book['entry_id']            = $this->hasUserReservedBook($book, auth()->id(), "entry_id");
            $book['state']               = $this->hasUserReservedBook($book, auth()->id(), "state");
            $book['reservation_deleted'] = Entries::where('reserved_by', auth()->id())->where('book_id', $book['id'])->withTrashed()->whereNotNull('canceled_at')->exists();
        }

        // $canceled_reservations = Entries::onlyTrashed()
        //     ->with([
        //         'reserved_by:id,name',
        //         'canceled_by:id,name',
        //         'book.category',
        //         'book.user:id,name',
        //     ])
        //     ->whereHas('book.group', function ($query) use ($id) {
        //         $query->where('id', $id);
        //     })
        //     ->where('group_id', $group['id'])
        //     ->whereNotNull('cancel_reason')
        //     ->where(function ($query) use ($group) {
        //         $query->where('reserved_by', auth()->id())
        //             ->orWhere('canceled_by', auth()->id());

        //         if (auth()->id() === $group['created_by']) {
        //             $query->orWhereNotNull('id');
        //         }

        //         if (auth()->user()->hasRole('admin')) {
        //             $query->orWhereNotNull('id');
        //         } else {
        //             $query->orWhereHas('book.group.groupMember', function ($subQuery) {
        //                 $subQuery->where('member_role', 'admin')
        //                     ->where('member_id', auth()->id())
        //                     ->where('activated', 1);
        //             });
        //         }
        //     })
        //     ->get()
        //     ->toArray();

        // $group["canceledReservations"] = $canceled_reservations;

        $loan_rules = LoanRule::orderBy('duration')->get()->toArray();

        $group["loanRules"] = $loan_rules;

        /*$entries = Entries::with(['book'])
            ->whereHas('book', function ($q) use ($id) {
                $q->whereHas('group', function ($q) use ($id) {
                    $q->where('id', $id);
                })->where('created_by', auth()->user()->id);
            })
            ->where('is_reserved', 2)
            ->get()
            ->toArray();*/
      
      $entries = Entries::with(['book'])
        ->whereHas('book', function ($q) use ($id) {
            $q->whereHas('group', function ($q) use ($id) {
                $q->where('id', $id); // Ensure the book belongs to the specified group
            })
            ->where('created_by', auth()->user()->id); // Only show books created by the user
        })
        ->where('is_reserved', 2) // Ensure the entry is reserved
        // Add additional conditions to ensure the group_id and book_id are valid
        ->whereHas('book.entries', function ($query) use ($id) {
            $query->where('group_id', $id) // Ensure the entry belongs to the correct group
                ->whereNotNull('group_id') // Ensure group_id is not null
                ->whereNotNull('book_id') // Ensure book_id is not null
                ->where('book_id', '!=', ''); // Ensure book_id is not empty
        })
        ->get()
        ->toArray();


        /*$return_requests = Entries::with(['book'])
            ->whereHas('book', function ($q) use ($id, $user_status, $group) {
                $q->whereHas('group', function ($q) use ($id) {
                    $q->where('id', $id);
                });

                if (! auth()->user()->hasRole('admin')) {
                    if ($group['created_by'] != auth()->id()) {
                        if (empty($user_status) || $user_status['member_role'] != 'admin') {
                            $q->where('created_by', auth()->user()->id);
                        }
                    }
                }
            })
            ->where(function ($query) {
                $query->whereIn('is_reserved', [1, 2]);
                $query->orWhere('state', 'return-request');
            })
            ->orWhere(function ($query) {
                $query->where(function ($query) {
                    $query->where('is_reserved', 1)
                        ->orWhere('state', 'return-request');
                });
                $query->where('reserved_by', auth()->id());
            })
            ->get();*/
            $return_requests = Entries::with(['book'])
          ->whereHas('book', function ($q) use ($id, $user_status, $group) {
              $q->whereHas('group', function ($q) use ($id) {
                  $q->where('id', $id); // Ensure the group is the one the user is part of
              });

              if (!auth()->user()->hasRole('admin')) {
                  if ($group['created_by'] != auth()->id()) {
                      if (empty($user_status) || $user_status['member_role'] != 'admin') {
                          $q->where('created_by', auth()->user()->id); // Only show books created by the user
                      }
                  }
              }
          })
          ->where(function ($query) {
              $query->whereIn('is_reserved', [1, 2]);
              $query->orWhere('state', 'return-request');
          })
          ->orWhere(function ($query) {
              $query->where(function ($query) {
                  $query->where('is_reserved', 1)
                      ->orWhere('state', 'return-request');
              });
              $query->where('reserved_by', auth()->id());
          })
          // Add the additional conditions to check for matching group_id and non-null/empty group_id and book_id in entries
          ->whereHas('book.entries', function ($query) use ($id) {
              $query->where('group_id', $id) // Ensure the entry belongs to the current group
                  ->whereNotNull('group_id') // Ensure the group_id is not null
                  ->whereNotNull('book_id') // Ensure the book_id is not null
                  ->where('book_id', '!=', ''); // Ensure book_id is not empty
          })
          ->get();


        $return_requests->each(function ($entry) {
            if ($entry->reserved_by) {
                $userTrustScores = TrustScore::where('user_id', $entry->reserved_by);
                $averageRating   = $userTrustScores->avg('rating');
                $totalReviews    = $userTrustScores->count();

                $entry->average_rating = $totalReviews === 0 ? 'First time'
                : sprintf('%.1f/5 out of %d', $averageRating, $totalReviews);
            }
        });

        $group["returnRequests"] = $return_requests->toArray();
        $group["entries"]        = $entries;

        $group['itemMetrics']['mainid']=$id;
        // Get the authenticated user's ID
        $userId = auth()->user()->id;

        // Retrieve the rejected items, or assign an empty array if none are found
        /*$group['itemMetrics'] = [
            'rejected_items' => ItemRejectedRequest::with(['book', 'user', 'disapprover'])
                ->whereHas('book', function ($query) use ($userId) {
                    $query->where('created_by', $userId);
                    $query->where('book.group_id', $group['itemMetrics']['mainid']);
                })
                ->get()
        ];*/

        // Ensure that itemMetrics is initialized first
        $group['itemMetrics'] = $group['itemMetrics'] ?? [];

        if (isset($group['itemMetrics']['mainid'])) {
            $group['itemMetrics']['rejected_items'] = ItemRejectedRequest::with(['book', 'user', 'disapprover'])
                ->whereHas('book', function ($query) use ($userId, $group) {
                    $query->where('created_by', $userId);
                    $query->where('book.group_id', $group['itemMetrics']['mainid']);
                })
                ->get();
        } else {
            // Handle the case when 'mainid' is not available
            $group['itemMetrics']['rejected_items'] = collect();  // Empty collection
        }
        

        // Check if the 'rejected_items' array is empty, and if so, assign it as an empty array
        if ($group['itemMetrics']['rejected_items']->isEmpty()) {
            $group['itemMetrics']['rejected_items'] = [];
        }


        $retdata["enabled_member"] = $enabled_member;
        $retdata["user_status"]    = $user_status;

        if ($enabled_member || $group['created_by'] == auth()->id() || auth()->user()->hasRole('admin')) {
            $tickets = Ticket::with(['comments', 'comments.user:id,name', 'user:id,name', 'admin:id,name'])->whereHas('user')->whereHas('admin')->where('group_id', $group['id'])->where(function ($query) use ($group) {
                if (! auth()->user()->hasRole('admin') && $group['created_by'] != auth()->id()) {
                    $query->where('user_id', auth()->id())
                        ->orWhere('admin_id', auth()->id());
                }
            })->get()->toArray();
        } else {
            $tickets = [];
        }

        $group['tickets'] = $tickets;

        if ($enabled_member || $group['created_by'] == auth()->id() || auth()->user()->hasRole('admin')) {
            $posts = Post::with(['comments', 'comments.user:id,name', 'user:id,name'])->whereHas('user')->where('group_id', $group['id'])
                ->get()->toArray();
        } else {
            $posts = [];
        }

        $group['posts'] = $posts;

        $retdata["group"]   = $group;
        $retdata['members'] = Groupmember::with(['member', 'group'])->where("group_id", $id)->where("status", "added")
            ->when(! auth()->user()->hasRole('admin') && auth()->id() !== $group['created_by'], function ($query) {
                return $query->where("activated", 1);
            })->get()->toArray();

        if (! isset(Auth::user()->id)) {
            echo "Added in group Successfully";
            return;
        }

        if (auth()->user()->hasRole('admin') || (! empty($user_status) && $user_status['member_role'] == 'admin' && $user_status['activated'])) {
            $retdata['requested_members'] = Groupmember::with(['member', 'group'])
                ->where("group_id", $id)
                ->whereIn("status", ["requested", "invited"])
                ->get()
                ->toArray();
        } elseif (Auth::user()->id == $group["created_by"]) {
            $retdata['requested_members'] = Groupmember::with(['member', 'group'])
                ->where("group_id", $id)
                ->where("status", "requested")
                ->get()
                ->toArray();
        } elseif (Groupmember::where('member_id', auth()->user()->id)
                ->where('group_id', $group_id)
                ->where('status', 'invited')
                ->first()) {
            $retdata['requested_members'] = Groupmember::with(['member', 'group'])
                ->where("group_id", $id)
                ->where("status", "invited")
                ->get()->toArray();
        }

        if (! empty($retdata['requested_members'])) {
            $retdata['active_tab'] = 'requests';
        }

        

        $data                = [];
        $data['title']       = 'Show Group';
        $data['template']    = 'group.show';
        $data['script_file'] = 'group_show';

        return view('with_login_common', compact('data', 'retdata'));
    }

    public function addComment(Request $request)
    {
        $request->validate([
            'item_id' => 'required',
            'comment' => 'required|string|max:1000',
        ]);

        Comment::create([
            'item_id' => $request->item_id,
            'user_id' => auth()->user()->id,
            'comment' => $request->comment,
        ]);

        return response()->json(['message' => 'Comment added successfully']);
    }

    public function getComments($itemId)
    {
        $comments = Comment::where('item_id', $itemId)
            ->with('user:id,name')
            ->latest()
            ->get();

        return response()->json($comments);
    }

    public function ConfirmGroupAdd(Request $request, $id, $member_id, $created_by)
    {
        $group_member = Groupmember::where('group_id', $id)->where('member_id', $member_id)->first();
        $group_member->update(["status" => "added", 'activated' => true]);
        $group            = Group::find($id);
        $user_accepted_by = User::find($member_id);
        $group_creater    = User::find($created_by);

        $link = "<a href=\"" . route("show-group", ["id" => $id]) . "\">link</a>";
        SendEmail($group_creater->email, 'Group Invitation Acceptance ', "Group invitation of <b>" . $group->title . "</b> has been accepted by <b>" . $user_accepted_by->name . '</b> click ' . $link . " to view.");

        $notification = [
            'only_database' => true,
            'title'         => 'Group Invitation Acceptance',
            'type'          => 'group_invitation_acceptance',
            'subject'       => 'Group Invitation Acceptance',
            'message'       => "Group invitation of <b>{$group->title}</b> has been accepted by <b>{$user_accepted_by->name}</b>",
            'user_id'       => auth()->user()->id,
            'group_id'      => $group->id,
            'member_id'     => $member_id,
            'id'            => $group_member->id,
            'url'           => route("show-group", ["id" => $id]),
            'action'        => 'View Group',
        ];

        $group_creater->notify(new GeneralNotification($notification));

        return json_encode(["success" => true, "message" => "Group joining request accepted successfully"]);

    }

    public function RejectGroupAdd(Request $request, $id, $member_id)
    {
        $group_member = Groupmember::where('group_id', $id)
            ->where('member_id', $member_id)
            ->where('status', 'invited');

        // $group_member->update(["status" => "rejected", 'activated' => false]);
        $group_member->delete();

        return json_encode(["success" => true, "message" => "Group joining request rejected successfully"]);
    }

    public function JoinGroup(Request $request)
    {
        $retdata = [];

        $week_group = Group::select('group.*')
            ->join('users', 'group.created_by', '=', 'users.id')
            ->with(['addedmembers', 'groupmembers'])
            ->where("group.created_by", "!=", Auth::user()->id)
            ->where('group.default', false)
            ->where('group.status', '!=', '0')
            ->get()->toArray();

        $retdata["week_group"] = $week_group;
        $data                  = [];
        $data['title']         = 'Join Group';
        $data['template']      = 'group.join';
        $data['script_file']   = 'join';
        return view('with_login_common', compact('data', 'retdata'));
    }

    public function RequestJoin(Request $request)
    {
        $member_id = $request->input('member_id');
        $group_id  = $request->input('group_id');
        $group     = Group::find($group_id);

        $mem_status = "requested";
        if (! empty($group->grouptype) && $group->grouptype->name == "Borrower") {
            $mem_status = "added";
        }
        $group_member = Groupmember::where('member_id', $member_id)->where('group_id', $group_id)->first();
        if ($group_member) {
            $group_member->update([
                "member_id"  => $member_id,
                "status"     => $mem_status,
                "created_by" => Auth::user()->id,
                "created_at" => date("Y-m-d H:i:s"),
            ]);
        } else {
            $group_member = $group->groupmembers()->create([
                "member_id"  => $member_id,
                "status"     => $mem_status,
                "created_by" => Auth::user()->id,
                "created_at" => date("Y-m-d H:i:s"),
            ]);
        }
        $creater          = User::find($group->created_by);
        $user             = User::find($member_id);
        $user->group_type = $group->group_type_id;
        $user->save();

        $joining_link = route('show-group', ['id' => $group->id]);
        $joining_link = $joining_link . '?tab=requests';
        $link         = "<a href=\"" . $joining_link . "\">Accept the request on Group Page?</a>";

        $email_content = "User named <b>" . $user->name . "</b> has joined your group <b>" . $group->title . "</b>.";
        if ($mem_status == "requested") {
            $email_subject = 'Group Join Request';
            $email_content = "User named <b>" . $user->name . "</b> has requested to join your group <b>" . $group->title . "</b>. " . $link;
        }

        SendEmail($creater->email, $email_subject, $email_content);

        $inviteNotification = [
            'only_database' => true,
            'title'         => 'Group Join Request',
            'type'          => 'group_join_request',
            'subject'       => 'Invited to a group',
            'message'       => "User named <b>{$user->name}</b> has requested to join your group <b>{$group->title}</b>",
            'user_id'       => $user->id,
            'group_id'      => $group_id,
            'member_id'     => $member_id,
            'id'            => $group_member->id,
            'url'           => $joining_link,
            'action'        => 'View Group',
        ];

        $creater->notify(new GeneralNotification($inviteNotification));

        return json_encode(["success" => true, "message" => "Join Request send successfully !"]);
    }

    public function DeleteGroup(Request $request)
    {
        $group_id                = $request->input('group_id');
        $group                   = Group::find($group_id);
        $group->to_be_deleted_at = date("Y-m-d", strtotime('+90 days'));
        $group->save();
        return json_encode(["success" => true, "message" => "Group Will be deleted in 90 Days From now."]);
    }

    /*public function InviteUser(Request $request)
    {
        $group_id      = $request->input('group_id');
        $group_type_id = $request->input('group_type_id');
        $email_id      = $request->input('email_id');

        if (empty($group_id) || empty($group_type_id) || empty($email_id)) {
            return json_encode(["success" => false, "message" => "Invalid input data provided."]);
        }

        if (! filter_var($email_id, FILTER_VALIDATE_EMAIL)) {
            return json_encode(["success" => false, "message" => "Invalid email address provided."]);
        }

        $group = Group::find($group_id);
        if (! $group) {
            return json_encode(["success" => false, "message" => "Group not found."]);
        }

        $invited_by = Auth::user();

        $user = User::where("email", $email_id)->first();
        if ($user) {

            $existing_member = Groupmember::where('member_id', $user->id)->where('group_id', $group_id)->first();

            if ($existing_member) {
                if ($existing_member->status == 'requested') {
                    return json_encode([
                        "success" => false,
                        "message" => "User already requested to join the group. Please accept it in the requested tab.",
                    ]);
                } elseif ($existing_member->status == 'added') {
                    return json_encode([
                        "success" => false,
                        "message" => "User is already part of the group.",
                    ]);
                } elseif ($existing_member->status == 'invited') {
                    return json_encode([
                        "success" => false,
                        "message" => "Joining request already sent you cannot send again untill user aacpet or reject.",
                    ]);
                    // $existing_member->update([
                    //     'updated_by' => Auth::user()->id,
                    //     'updated_at' => now(),
                    // ]);

                    // $inviteNotification = [
                    //     'only_database' => true,
                    //     'title' => 'Invited to a group',
                    //     'type' => 'group_invitation',
                    //     'subject' => 'Invited to a group',
                    //     'message' => 'Join my group to view its items',
                    //     'user_id' => $user->id,
                    //     'url' => route('show-group', ['id' => $group->id]),
                    //     'action' => 'View Group',
                    // ];

                    // $user->notify(new GeneralNotification($inviteNotification));

                    // $accept_link = route("show-group", ["id" => $group_id]);

                    // $email_content = "
                    // <p>You have been re-invited by DixWix User <b>" . htmlspecialchars($invited_by->name) . "</b> to join the group <b>" . htmlspecialchars($group->title) . "</b>.</p>
                    // <p>Please click one of the following <a href=\"" . $accept_link . "\">Link</a> view the group</p>
                    // <p>If you have any questions, feel free to contact us at support@dixwix.com.</p>";

                    // try {
                    //     SendEmail($email_id, 'Group Invitation (Reminder)', $email_content);
                    // } catch (\Exception $e) {
                    //     return json_encode([
                    //         "success" => false,
                    //         "message" => "Failed to resend the invitation email. Please try again later.",
                    //     ]);
                    // }

                    // return json_encode([
                    //     "success" => true,
                    //     "message" => "Invitation resent to the user.",
                    // ]);
                }
            } else {
                $group->groupmembers()->create([
                    "member_id"   => $user->id,
                    "status"      => "invited",
                    "member_role" => "user",
                    "created_by"  => Auth::user()->id,
                    "created_at"  => now(),
                ]);

                $inviteNotification = [
                    'only_database' => true,
                    'title'         => 'Invited to a group',
                    'type'          => 'group_invitation',
                    'subject'       => 'Invited to a group',
                    'message'       => 'Join my group to view its items',
                    'user_id'       => $user->id,
                    'url'           => route('show-group', ['id' => $group->id]),
                    'action'        => 'View Group',
                ];

                $user->notify(new GeneralNotification($inviteNotification));
            }

            $accept_link = route("show-group", ["id" => $group_id]);
        } else {

            if (GroupUserInvited::where("group_id", $group_id)->where("email", $email_id)->exists()) {

                return json_encode([
                    "success" => false,
                    "message" => "Joining request already sent you cannot send again untill user aacpet or reject.",
                ]);

            }
            GroupUserInvited::create([
                "email"    => $email_id,
                "group_id" => $group_id,
            ]);

            $accept_link = route("accept-invite", [
                "group_id"      => $group_id,
                "group_type_id" => $group_type_id,
                "email_id"      => $email_id,
                "created_by"    => $invited_by->id,
            ]);
        }

        $email_content = "
        <p>You have been invited by DixWix User <b>" . htmlspecialchars($invited_by->name) . "</b> to join the group <b>" . htmlspecialchars($group->title) . "</b>.</p>
        <p>Please click the following <a href=\"" . $accept_link . "\">Link</a> view the group</p>
        <p>If you have any questions, feel free to contact us at support@dixwix.com.</p>";


        try {
            SendEmail($email_id, 'Group Invitation', $email_content);
        } catch (\Exception $e) {
            return json_encode([
                "success" => false,
                "message" => "Failed to send the invitation email. Please try again later.",
            ]);
        }

        return json_encode([
            "success" => true,
            "message" => "User invited to join DixWix group <b>" . htmlspecialchars($group->title) . "</b>.",
        ]);
    }*/

public function InviteUser(Request $request)
{
    $group_id = $request->input('group_id');
    $group_type_id = $request->input('group_type_id');
    $email_id = $request->input('email_id');

    // Check for empty inputs
    if (empty($group_id) || empty($group_type_id) || empty($email_id)) {
        Log::error("Invalid input data", ['group_id' => $group_id, 'group_type_id' => $group_type_id, 'email_id' => $email_id]);
        return json_encode(["success" => false, "message" => "Invalid input data provided."]);
    }

    // Validate email
    if (!filter_var($email_id, FILTER_VALIDATE_EMAIL)) {
        Log::error("Invalid email address", ['email_id' => $email_id]);
        return json_encode(["success" => false, "message" => "Invalid email address provided."]);
    }

    // Find group
    $group = Group::find($group_id);
    if (!$group) {
        Log::error("Group not found", ['group_id' => $group_id]);
        return json_encode(["success" => false, "message" => "Group not found."]);
    }

    $invited_by = Auth::user();
    Log::info("User details", ['invited_by' => $invited_by->id]);

    // Find user
    $user = User::where("email", $email_id)->first();



    if ($user) {
        Log::info("User found in database", ['user_id' => $user->id]);

        // Check for existing group member
        $existing_member = Groupmember::where('member_id', $user->id)->where('group_id', $group_id)->first();

        if ($existing_member) {
            Log::info("Existing group member found", ['user_id' => $user->id, 'group_id' => $group_id]);

            // Handle different statuses
            if ($existing_member->status == 'requested') {
                return json_encode([
                    "success" => false,
                    "message" => "User already requested to join the group. Please accept it in the requested tab.",
                ]);
            } elseif ($existing_member->status == 'added') {
                return json_encode([
                    "success" => false,
                    "message" => "User is already part of the group.",
                ]);
            } elseif ($existing_member->status == 'invited') {
                Log::info("User is already invited, updating existing invitation", ['user_id' => $user->id, 'group_id' => $group_id]);

                try {
                    $existing_member->update([
                        'updated_by' => Auth::user()->id,
                        'updated_at' => now(),
                    ]);

                    // Prepare the notification data
                    $inviteNotification = [
                        'id' => (string) Str::uuid(), // Unique ID for the notification
                        'type' => 'App\\Notifications\\GeneralNotification', // Notification class name
                        'notifiable_type' => 'App\\Models\\User', // Type of the notifiable (User model)
                        'notifiable_id' => $user->id, // The ID of the user being notified
                        'data' => json_encode([ // JSON-encoded data field
                            'title' => 'Re-invited to a group',
                            'type' => 'group_invitation',
                            'subject' => 'Invited to a group',
                            'message' => 'Join my group to view its items',
                            'url' => route('show-group', ['id' => $group->id]),
                            'action' => 'View Group',
                        ]),
                        'read_at' => null, // Set to NULL initially
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    // Directly insert into the notifications table
                    $sts = DB::table('notifications')->insert($inviteNotification);

                    // Log successful notification insertion
                    Log::info("Notification inserted directly into the database", [
                        'user_id' => $user->id,
                        'notification' => $inviteNotification,
                        'status' => $sts
                    ]);

                } catch (Exception $e) {
                    // Log the error with the exception message
                    Log::error("Error inserting notification into database", [
                        'user_id' => $user->id,
                        'notification' => $inviteNotification,
                        'error_message' => $e->getMessage(),
                    ]);
                }
             

                // Log notification entry
                // Log::info("Notification sent to user", ['user_id' => $user->id, 'notification' => $inviteNotification]);

                // Send email content
                $accept_link = route("show-group", ["id" => $group_id]);
                $email_content = "
                <p>You have been re-invited by DixWix User <b>" . htmlspecialchars($invited_by->name) . "</b> to join the group <b>" . htmlspecialchars($group->title) . "</b>.</p>
                <p>Please click one of the following <a href=\"" . $accept_link . "\" style='color: #005cbf'>Link</a> to view the group</p>
                <p>If you have any questions, feel free to contact us at support@dixwix.com.</p>";

                try {
                    SendEmail($email_id, 'Group Invitation (Reminder)', $email_content);
                    Log::info("Invitation email sent", ['email_id' => $email_id]);
                } catch (\Exception $e) {
                    Log::error("Failed to resend the invitation email", ['error' => $e->getMessage()]);
                    return json_encode([
                        "success" => false,
                        "message" => "Failed to resend the invitation email. Please try again later.",
                    ]);
                }

                return json_encode([
                    "success" => true,
                    "message" => "Invitation resent to the user.",
                ]);
            }
        } /*else {
            Log::info("No existing group member, adding user as invited", ['user_id' => $user->id, 'group_id' => $group_id]);

            $group->groupmembers()->create([
                "member_id"   => $user->id,
                "status"      => "invited",
                "member_role" => "user",
                "created_by"  => Auth::user()->id,
                "created_at"  => now(),
            ]);

            // Send notification to user
            $inviteNotification = [
                'only_database' => true,
                'title' => 'Invited to a group',
                'type' => 'group_invitation',
                'subject' => 'Invited to a group',
                'message' => 'Join my group to view its items',
                'user_id' => $user->id,
                'url' => route('show-group', ['id' => $group->id]),
                'action' => 'View Group',
            ];

            $user->notify(new GeneralNotification($inviteNotification));

            // Log notification entry
            Log::info("Notification sent to new group member", ['user_id' => $user->id, 'notification' => $inviteNotification]);
        }*/
        /*else {
            $inviteNotification=[];
            try {
                // Create new group member entry as invited
                $group->groupmembers()->create([
                    "member_id"   => $user->id,
                    "status"      => "invited",
                    "member_role" => "user",
                    "created_by"  => Auth::user()->id,
                    "created_at"  => now(),
                ]);

                // Prepare the notification data
                $inviteNotification = [
                    'id' => (string) Str::uuid(), // Unique ID for the notification
                    'type' => 'App\\Notifications\\GeneralNotification', // Notification class name
                    'notifiable_type' => 'App\\Models\\User', // Type of the notifiable (User model)
                    'notifiable_id' => $user->id, // The ID of the user being notified
                    'data' => json_encode([ // JSON-encoded data field
                        'title' => 'Invited to a group',
                        'type' => 'group_invitation',
                        'subject' => 'Invited to a group',
                        'message' => 'Join my group to view its items',
                        'url' => route('show-group', ['id' => $group->id]),
                        'action' => 'View Group',
                    ]), 
                    'read_at' => null, // Set to NULL initially
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // Directly insert into the notifications table
                $sts = DB::table('notifications')->insert($inviteNotification);

                // Log successful notification insertion
                Log::info("Notification inserted directly into the database", [
                    'user_id' => $user->id,
                    'notification' => $inviteNotification,
                    'status' => $sts
                ]);

            } catch (Exception $e) {
                // Log the error with the exception message
                Log::error("Error inserting notification into database", [
                    'user_id' => $user->id,
                    'notification' => $inviteNotification,
                    'error_message' => $e->getMessage(),
                ]);
            }

            // Send email content for initial invitation
            $accept_link = route("show-group", ["id" => $group->id]);
            $email_content = "
            <p>You have been invited by DixWix User <b>" . htmlspecialchars($invited_by->name) . "</b> to join the group <b>" . htmlspecialchars($group->title) . "</b>.</p>
            <p>Please click one of the following <a href=\"" . $accept_link . "\">Link</a> to view the group</p>
            <p>If you have any questions, feel free to contact us at support@dixwix.com.</p>";

            try {
                SendEmail($email_id, 'Group Invitation', $email_content);
                Log::info("Invitation email sent", ['email_id' => $email_id]);
            } catch (\Exception $e) {
                Log::error("Failed to send the invitation email", ['error' => $e->getMessage()]);
                return json_encode([
                    "success" => false,
                    "message" => "Failed to send the invitation email. Please try again later.",
                ]);
            }

            return json_encode([
                "success" => true,
                "message" => "Invitation sent to the user.",
            ]);

        }*/
        else{
            $inviteNotification=[];
            try {
                // Check if the user is already a member of the group
                $existingMember = $group->groupmembers()->where('member_id', $user->id)->first();

                if ($existingMember) {
                    // If the user already exists in the group, update their status to 'invited' (or handle as needed)
                    $existingMember->update([
                        'status' => 'invited',
                        'updated_by' => Auth::user()->id,
                        'updated_at' => now(),
                    ]);

                    Log::info("User is already a member, status updated to invited", [
                        'user_id' => $user->id,
                        'group_id' => $group->id,
                        'status' => 'invited'
                    ]);
                } else {
                    // If the user is not a member, insert them as a new invited member
                    $group->groupmembers()->create([
                        "member_id"   => $user->id,
                        "status"      => "invited",
                        "member_role" => "user",
                        "created_by"  => Auth::user()->id,
                        "created_at"  => now(),
                        "group_id"    => $group->id,  // Ensure group_id is passed for the new member
                        "updated_at"  => now(),
                    ]);

                    Log::info("New user invited to group", [
                        'user_id' => $user->id,
                        'group_id' => $group->id,
                        'status' => 'invited'
                    ]);
                }

                // Prepare and send the notification as before
                $inviteNotification = [
                    'id' => (string) Str::uuid(), 
                    'type' => 'App\\Notifications\\GeneralNotification',
                    'notifiable_type' => 'App\\Models\\User',
                    'notifiable_id' => $user->id,
                    'data' => json_encode([
                        'title' => 'Invited to a group',
                        'type' => 'group_invitation',
                        'subject' => 'Invited to a group',
                        'message' => 'Join my group to view its items',
                        'url' => route('show-group', ['id' => $group->id]),
                        'action' => 'View Group',
                    ]),
                    'read_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // Directly insert the notification into the notifications table
                $sts = DB::table('notifications')->insert($inviteNotification);
                Log::info("Notification inserted directly into the database", [
                    'user_id' => $user->id,
                    'notification' => $inviteNotification,
                    'status' => $sts
                ]);

            } catch (Exception $e) {
                Log::error("Error inserting notification into database", [
                    'user_id' => $user->id,
                    'notification' => $inviteNotification,
                    'error_message' => $e->getMessage(),
                ]);
            }

            // Send email content for the initial invitation
            $accept_link = route("show-group", ["id" => $group->id]);
            $email_content = "
            <p>You have been invited by DixWix User <b>" . htmlspecialchars($invited_by->name) . "</b> to join the group <b>" . htmlspecialchars($group->title) . "</b>.</p>
            <p>Please click one of the following <a href=\"" . $accept_link . "\" style='color: #005cbf'>Link</a> to view the group</p>
            <p>If you have any questions, feel free to contact us at support@dixwix.com.</p>";

            try {
                SendEmail($email_id, 'Group Invitation', $email_content);
                Log::info("Invitation email sent", ['email_id' => $email_id]);
            } catch (\Exception $e) {
                Log::error("Failed to send the invitation email", ['error' => $e->getMessage()]);
                return json_encode([
                    "success" => false,
                    "message" => "Failed to send the invitation email. Please try again later.",
                ]);
            }

            return json_encode([
                "success" => true,
                "message" => "Invitation sent to the user.",
            ]);

        }

        $accept_link = route("show-group", ["id" => $group_id]);
    } else {
        // Handle case when user does not exist in database
        Log::info("User not found in database, adding to GroupUserInvited", ['email_id' => $email_id]);

        if (GroupUserInvited::where("group_id", $group_id)->where("email", $email_id)->exists()) {
            return json_encode([
                "success" => false,
                "message" => "Joining request already sent you cannot send again until user accepts or rejects.",
            ]);
        }

        GroupUserInvited::create([
            "email"    => $email_id,
            "group_id" => $group_id,
        ]);

        $accept_link = route("accept-invite", [
            "group_id"      => $group_id,
            "group_type_id" => $group_type_id,
            "email_id"      => $email_id,
            "created_by"    => $invited_by->id,
        ]);
    }

    // Send email content for invitation
    $email_content = "
    <p>You have been invited by DixWix User <b>" . htmlspecialchars($invited_by->name) . "</b> to join the group <b>" . htmlspecialchars($group->title) . "</b>.</p>
    <p>Please click the following <a href=\"" . $accept_link . "\">Link</a> to view the group</p>
    <p>If you have any questions, feel free to contact us at support@dixwix.com.</p>";

    try {
        SendEmail($email_id, 'Group Invitation', $email_content);
        Log::info("Invitation email sent to external user", ['email_id' => $email_id]);
    } catch (\Exception $e) {
        Log::error("Failed to send the invitation email", ['error' => $e->getMessage()]);
        return json_encode([
            "success" => false,
            "message" => "Failed to send the invitation email. Please try again later.",
        ]);
    }

    return json_encode([
        "success" => true,
        "message" => "User invited to join DixWix group <b>" . htmlspecialchars($group->title) . "</b>.",
    ]);
}



    public function AcceptInvite($group_id, $group_type_id, $email_id, $created_by)
    {
        if (! Auth::check()) {

            session([
                'invite_group_id'      => $group_id,
                'invite_group_type_id' => $group_type_id,
                'invite_email_id'      => $email_id,
                'created_by'           => $created_by,
            ]);

            session()->put('create_group_invite', true);

            return redirect()->route('signup_via_group_invite', [
                'referrer_id'   => 0,
                'group_id'      => $group_id,
                'group_type_id' => $group_type_id,
            ])->with('message', 'Please sign up to accept the group invitation.');
        }

        $user  = User::where('email', $email_id)->first();
        $group = Group::find($group_id);

        return redirect()->route('show-group', ['id' => $group_id])
            ->with('message', 'You have successfully accepted the invitation to join the group.');
    }

    public function RejectInvite($group_id, $email_id)
    {
        if (! Auth::check()) {

            session([
                'invite_group_id' => $group_id,
                'invite_email_id' => $email_id,
            ]);

            $group_type_id = session('invite_group_type_id', null);

            return redirect()->route('signup_via_group_invite', [
                'referrer_id'   => 0,
                'group_id'      => $group_id,
                'group_type_id' => $group_type_id,
            ])->with('message', 'Please sign up to reject the group invitation.');
        }

        $group_member = Groupmember::where('group_id', $group_id)->where('email', $email_id)->first();
        if ($group_member) {
            $group_member->delete();
        }

        return redirect()->route('show-group', ['id' => $group_id])
            ->with('message', 'You have rejected the invitation to join the group.');
    }

    public function DeleteMemberFromGroup(Request $request)
    {
        $groupId       = $request->input('group_id');
        $groupMemberId = $request->input('member_id');
        $groupTypeId   = $request->input('group_type_id');

        $groupMember = Groupmember::with(['member', 'group'])
            ->where('member_id', $groupMemberId)
            ->where('group_id', $groupId)
            ->first();

        if (! $groupMember) {
            return json_encode([
                "success"    => false,
                "message"    => "This is not a member of this group",
                "reload_url" => route('get-members-to-add', [
                    "group_id"      => $groupId,
                    "group_type_id" => $groupTypeId,
                ]),
            ]);
        }

        if ($groupMember->status === "added") {

            $groupMember->forceDelete();

            $groupTitle = $groupMember->group->title ?? "Unknown Group";

            $notificationData = [
                'title'   => 'Group Membership Removed',
                'type'    => 'group_membership_removed',
                'subject' => 'Group Membership Removed',
                'message' => "You have been removed from the group {$groupTitle}",
                'user_id' => auth()->user()->id,
                'url'     => route("show-group", ["id" => $groupId]),
                'action'  => 'View Group',
            ];

            $groupMember->member->notify(new GeneralNotification($notificationData));

            return json_encode([
                "success"    => true,
                "message"    => "Member deleted successfully",
                "reload_url" => route('get-members-to-add', [
                    "group_id"      => $groupId,
                    "group_type_id" => $groupTypeId,
                ]),
            ]);
        }

        return json_encode([
            "success"    => false,
            "message"    => "This is not a member of this group",
            "reload_url" => route('get-members-to-add', [
                "group_id"      => $groupId,
                "group_type_id" => $groupTypeId,
            ]),
        ]);
    }

    public function AcceptGroupMember(Request $request)
    {
        $group_member_id      = $request->input('member_id');
        $group_member         = Groupmember::find($group_member_id);
        $group_member->status = "added";
        $group_member->save();
        return json_encode(["success" => true, "message" => "User Added Successfully"]);
    }

    /*public function UpdateMemberRole(Request $request)
    {
        $member_id     = $request->member_id;
        $group_id      = $request->group_id;
        $group_type_id = $request->group_type_id;
        $role          = $request->role;

        $group_member = Groupmember::with(['group', 'member'])->where('member_id', $member_id)->where('group_id', $group_id)->first();

        if ($group_member) {
            if ($group_member->activated == 0 && $role == 'admin') {
                return json_encode(["success" => false, "message" => "This user is not activated. Activate first please", "reload_url" => route('get-members-to-add', ["group_id" => $group_id, "group_type_id" => $group_type_id])]);
            }
            if ($group_member->status == "added") {

                $group_member->update(['member_role' => $role]);

                $memberUser = $group_member->member;
                $groupName  = $group_member->group->title;

                $roleNotification = [
                    'title'   => 'Role Updated in Group',
                    'type'    => 'role_updated',
                    'subject' => 'Role Updated',
                    'message' => "Your role has been updated to " . ucfirst($group_member->role) . " in the group {$groupName}.",
                    'user_id' => auth()->user()->id,
                    'url'     => route("show-group", ["id" => $group_id]),
                    'action'  => 'View Group',
                ];

                $memberUser->notify(new GeneralNotification($roleNotification));

                return json_encode(["success" => true, "message" => "Role changed to {$role} successfully", "reload_url" => route('get-members-to-add', ["group_id" => $group_id, "group_type_id" => $group_type_id])]);
            } else {
                return json_encode(["success" => false, "message" => "Invitation is pending for this user", "reload_url" => route('get-members-to-add', ["group_id" => $group_id, "group_type_id" => $group_type_id])]);
            }
        } else {
            return json_encode(["success" => false, "message" => "This user is not a member of this group", "reload_url" => route('get-members-to-add', ["group_id" => $group_id, "group_type_id" => $group_type_id])]);
        }
    }*/

    public function UpdateMemberRole(Request $request)
    {
        $member_id     = $request->member_id;
        $group_id      = $request->group_id;
        $group_type_id = $request->group_type_id;
        $role          = $request->role;

        $group_member = Groupmember::with(['group', 'member'])->where('member_id', $member_id)->where('group_id', $group_id)->first();

        if ($group_member) {
            if ($group_member->activated == 0 && $role == 'admin') {
                return json_encode(["success" => false, "message" => "This user is not activated. Activate first please", "reload_url" => route('get-members-to-add', ["group_id" => $group_id, "group_type_id" => $group_type_id])]);
            }
            if ($group_member->status == "added") {

                $group_member->update(['member_role' => $role]);

                $memberUser = $group_member->member;
                $groupName  = $group_member->group->title;

                // Notification for the user whose role was updated
                $roleNotification = [
                    'title'   => 'Role Updated in Group',
                    'type'    => 'role_updated',
                    'subject' => 'Role Updated',
                    'message' => "Your role has been updated to " . ucfirst($group_member->role) . " in the group {$groupName}.",
                    'user_id' => auth()->user()->id,
                    'url'     => route("show-group", ["id" => $group_id]),
                    'action'  => 'View Group',
                ];

                $memberUser->notify(new GeneralNotification($roleNotification));

                // Notification for the admin/user who updated the role
                $adminNotification = [
                    'title'   => 'Role Updated for Member',
                    'type'    => 'role_updated_admin',
                    'subject' => 'Role Update Successful',
                    'message' => "You have successfully updated the role of {$memberUser->name} to " . ucfirst($role) . " in the group {$groupName}.",
                    'user_id' => auth()->user()->id,
                    'url'     => route("show-group", ["id" => $group_id]),
                    'action'  => 'View Group',
                ];

                // Send the notification to the admin/user who updated the role
                auth()->user()->notify(new GeneralNotification($adminNotification));

                return json_encode(["success" => true, "message" => "Role changed to {$role} successfully", "reload_url" => route('get-members-to-add', ["group_id" => $group_id, "group_type_id" => $group_type_id])]);
            } else {
                return json_encode(["success" => false, "message" => "Invitation is pending for this user", "reload_url" => route('get-members-to-add', ["group_id" => $group_id, "group_type_id" => $group_type_id])]);
            }
        } else {
            return json_encode(["success" => false, "message" => "This user is not a member of this group", "reload_url" => route('get-members-to-add', ["group_id" => $group_id, "group_type_id" => $group_type_id])]);
        }
    }


    public function UpdateMemberStatus(Request $request)
    {
        $member_id     = $request->input('member_id');
        $group_id      = $request->input('group_id');
        $group_type_id = $request->input('group_type_id');
        $status        = $request->input('status');
        $group_member  = Groupmember::where('member_id', $member_id)
            ->where('group_id', $group_id);

        if (count($group_member->get()) > 0) {
            if ($group_member->get()[0]->status == "added") {
                $group_member->update([
                    'activated' => $status,
                ]);
                if ($group_member = 1) {
                    return json_encode(["success" => true, "message" => "User " . (($status == 0) ? "deactivated" : "activated") . " successfully", "reload_url" => route('get-members-to-add', ["group_id" => $group_id, "group_type_id" => $group_type_id])]);
                }
            } else {
                return json_encode(["success" => false, "message" => "Invitation is pending for this user", "reload_url" => route('get-members-to-add', ["group_id" => $group_id, "group_type_id" => $group_type_id])]);
            }
        } else {
            return json_encode(["success" => false, "message" => "This user is not a member of this group", "reload_url" => route('get-members-to-add', ["group_id" => $group_id, "group_type_id" => $group_type_id])]);
        }

    }

    public function UpdateGrouptatus(Request $request)
    {

        $group_id = $request->input('group_id');
        $status   = $request->input('status');

        $group = Group::find($group_id);

        if ($group) {
            $group->status = $status;
            $group->save();
            return json_encode(["success" => true, "message" => "Group " . (($status == 0) ? "deactivated" : "activated") . " successfully", "reload_url" => route('my-groups')]);
        }

    }

    public function AcceptRequest(Request $request)
    {
        $isAccepted  = filter_var($request->is_accepted, FILTER_VALIDATE_BOOLEAN);
        $groupMember = Groupmember::where('id', $request->id)
            ->where('member_id', $request->member_id)
            ->where('group_id', $request->group_id)
            ->whereIn('status', ["requested", "invited"])
            ->first();

        $groupName = Group::where('id', $request->group_id)->first()->title;

        $memberUser = User::where('id', $request->member_id)->first();

        $link = "<a href=\"" . url("show-group/" . $request->group_id) . "\">link</a>";

        if ($request->notification_id) {
            $notification = DatabaseNotification::find($request->notification_id);
            if ($notification) {
                $notification->markAsRead();
            }
        }

        if ((! is_null($groupMember) && $isAccepted) || isset($_GET['is_accepted'])) {

            $groupMember->update([
                'status'    => "added",
                'activated' => true,
            ]);

            $emailBody = ["message" => "Request Accepted", "email" => "You are now member of gorup <strong>" . $groupName . "</strong> <br>Please click this " . $link . "  to visit Group."];
            Mail::to($memberUser->email)->send(new MailService($emailBody));

            $inviteNotification = [
                'only_database' => true,
                'title'         => 'Group Request Accepted',
                'type'          => 'group_request_accepted',
                'subject'       => 'Group Request Accepted',
                'message'       => "You are now member of gorup <strong>" . $groupName . "</strong>",
                'user_id'       => auth()->user()->id,
                'url'           => route("show-group", ["id" => $request->group_id]),
                'action'        => 'View Group',
            ];

            $memberUser->notify(new GeneralNotification($inviteNotification));

            if (isset($_GET['is_accepted'])) {
                return redirect()->route('show-group', $request->group_id)->with('message', 'Group joining request accepted successfull');
            }
            return json_encode(["success" => true, "message" => "Group joining request accepted successfully", "reload_url" => route('show-group', $request->group_id)]);
        } else {
            $groupMember->forceDelete();

            $emailBody = ["message" => "Request Declined", "email" => "Your request for joining group <strong>" . $groupName . "</strong> declined.<br>Please click this " . $link . "  to visit Group."];
            Mail::to($memberUser->email)->send(new MailService($emailBody));

            $inviteNotification = [
                'only_database' => true,
                'title'         => 'Group Request Declined',
                'type'          => 'group_request_declined',
                'subject'       => 'Group Request Declined',
                'message'       => "Your request for joining group <strong>" . $groupName . "</strong> was declined.",
                'user_id'       => auth()->user()->id,
                'url'           => route("show-group", ["id" => $request->group_id]),
                'action'        => 'View Group',
            ];

            $memberUser->notify(new GeneralNotification($inviteNotification));

            return json_encode(["success" => false, "message" => "Group joining request declined", "reload_url" => route('show-group', $request->group_id)]);
        }

    }

    public function RejectGroupInvitaions()
    {
        $oneHourAgo = Carbon::now()->subHour();

        $invitations = Groupmember::where('created_at', '<', $oneHourAgo)
            ->where('status', 'invited')
            ->where('activated', 0)
            ->where('member_role', 'user')
            ->delete();

        return json_encode(['success' => true, 'message' => 'All invitaions removed those are set from more than one hours', 'data' => $invitations]);

    }

    // Function to check if the user has reserved the book
    private function hasUserReservedBook($book, $currentUserId, $status)
    {
        foreach ($book['entries'] as $entry) {
            if ($status == "reserved") {
                if ($entry['is_reserved'] == 1 && $entry['reserved_by'] == $currentUserId) {
                    return true; // The current user has reserved this book
                }
            } elseif ($status == "due_date") {
                if ($entry['is_reserved'] == 1 && $entry['reserved_by'] == $currentUserId) {
                    return $entry['due_date'];
                }
            } elseif ($status == "entry_id") {
                if ($entry['is_reserved'] == 1 && $entry['reserved_by'] == $currentUserId) {
                    return $entry['id'];
                }
            } elseif ($status == "state") {
                if ($entry['is_reserved'] == 1 && $entry['reserved_by'] == $currentUserId) {
                    return $entry['state'];
                }
            } else {
                if ($entry['is_reserved'] == 2 && $entry['reserved_by'] == $currentUserId) {
                    return true; // The current user has reserved this book
                }
            }

        }
        return false; // The current user has not reserved this book
    }
}
