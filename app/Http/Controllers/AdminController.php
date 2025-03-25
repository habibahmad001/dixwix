<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Type;
use App\Models\Group;
use App\Models\Setting;
use App\Models\Grouptype;
use App\Models\Groupmember;
use Illuminate\Http\Request;
use App\Models\GroupUserInvited;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use App\Notifications\GeneralNotification;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AdminController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    public function Dashboard(Request $request)
    {
        $data['title'] = 'Dashboard';
        $data['template'] = 'admin.dashboard';
        $groups = Group::get();
        $data['groups'] = $groups;
        return view('with_login_common', compact('data'));
    }

    public function AddCategory(Request $request, $id = null)
    {
        $retdata = [];
        $retdata["mode"] = "add";
        if (isset($id)) {
            $type = Type::find($id);
            $retdata["type"] = $type;
            $retdata["mode"] = "edit";
            $retdata["type_id"] = $id;
        }
        return $this->ReturnToCategoryAddPage($retdata);
    }

    public function addUser($id = null)
    {
        $retdata = [];
        $roles = Role::all();
        $data['title'] = 'Add User';
        $data['template'] = 'admin.users.add';
        $data['script_file'] = 'admin_user_script';
        return view('with_login_common', compact('data', 'retdata', 'roles'));
    }

    public function storeUser(Request $request)
    {
        $formData = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'required|email|unique:users,email',
            'biodata' => 'nullable|string',
            'address' => 'nullable|string',
            'state' => 'nullable|string|max:100',
            'zipcode' => 'nullable|string|max:10',
            'password' => 'required|string|min:6',
            'profile_pic' => 'nullable|image|mimes:jpg,jpeg,png',
            // 'roles' => 'required|array',
            'roles' => 'sometimes|required|array',
            'roles.*' => 'exists:roles,name',
        ]);

        if (auth()->user()->id === 1 && !$request->has('roles')) {
            throw ValidationException::withMessages(['roles' => 'The roles field is required.']);
        }

        if ($request->profile_pic) {
            $formData['profile_pic'] = $request->file('profile_pic')->store('profile_pictures');
        }

        $formData['email_verified_at'] = now();
        $formData['group_type'] = 1;
        unset($formData['roles']);

        $user = User::create($formData);
        if (auth()->user()->id == 1) {
            $user->syncRoles($request->roles);
        } else {
            $user->syncRoles('user');
        }

        $groupData = [
            "title" => "Dix New Group",
            "description" => "Please change title and description",
            "group_picture" => 'media/group-dummy.jpg',
            "created_by" => $user->id,
            "default" => true,
            "group_type" => 1,
            "status" => 0,
        ];

        $group = $user->createdgroups()->create($groupData);

        $userNotification = [
            'title' => 'Account created',
            'type' => 'account_created',
            'subject' => 'Dixwix Account Created',
            'message' => "Your account is created on dixwix by admin. Your credentials is below \n Email: {$user->email} \n Password: {$request->password}",
            'user_id' => auth()->user()->id,
            'url' => route('login'),
            'action' => 'Login',
        ];

        $groupNotification = [
            'only_email' => true,
            'title' => 'Group created',
            'type' => 'group_created',
            'subject' => 'New Group Created',
            'message' => 'A group created for you',
            'user_id' => auth()->user()->id,
            'url' => route('show-group', ['id' => $group->id]),
            'action' => 'View Group',
        ];

        $user->notify(new GeneralNotification($groupNotification));
        $user->notify(new GeneralNotification($userNotification));

        return redirect()->back()->with('success', 'User added successfully!');
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        $data['title'] = 'Edit User';
        $data['template'] = 'admin.users.edit';
        $data['script_file'] = 'admin_user_script';
        return view('with_login_common', compact('data', 'user', 'roles'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $formData = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => "required|email|unique:users,email,{$user->id}",
            'biodata' => 'nullable|string',
            'address' => 'nullable|string',
            'state' => 'nullable|string|max:100',
            'zipcode' => 'nullable|string|max:10',
            'password' => 'nullable|string|min:6',
            'profile_pic' => 'nullable|image|mimes:jpg,jpeg,png',
            'roles' => 'sometimes|required|array',
            // 'roles' => 'required|array',
            'roles.*' => 'exists:roles,name',
        ]);

        if (auth()->user()->id === 1 && !$request->has('roles')) {
            throw ValidationException::withMessages(['roles' => 'The roles field is required.']);
        }

        if (!$request->password) {
            unset($formData['password']);
        }

        if ($request->profile_pic) {

            if ($user->profile_pic && Storage::exists($user->profile_pic)) {
                Storage::delete($user->profile_pic);
            }
            $formData['profile_pic'] = $request->file('profile_pic')->store('profile_pictures');
        }

        unset($formData['roles']);

        $user->update($formData);
        if ($user->id != 1) {
            if (auth()->user()->id == 1) {
                $user->syncRoles($request->roles);
            } else {
                $user->syncRoles('user');
            }
        }

        return redirect()->back()->with('success', 'User updated successfully!');
    }

    public function allUsers(Request $request)
    {
        $data = [
            'title' => 'All Users',
            'template' => 'admin.users.list',
        ];

        $users = User::with([
            'roles',
            'membership',
            'membership.plan:id,name',
            'createdgroups:created_by,id,title',
            'createdgroups.groupmembers' => function ($query) {
                $query->where('member_role', 'admin')->where('activated', true);
            },
            'createdgroups.groupmembers.member:id,name'
        ])->get();

        return view('with_login_common', compact('data', 'users'));
    }

    public function deleteUser(Request $request)
    {
        try {
            $user = User::findOrFail($request->id);

            if ($user->id == auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can delete your own account successfully',
                ]);
            }
            if ($user->id == 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Super admin account cannot be deleted successfully',
                ]);
            }

            GroupUserInvited::where("email", $user->email)->delete();
            Groupmember::where('member_id', $user->id)->delete();

            $user->forceDelete();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function StoreCategory(Request $request)
    {
        $retdata = [];
        $data = $request->type;
        $mode = $request->mode;
        $type_id = $request->type_id;
        $retdata['mode'] = $mode;
        $retdata['type_id'] = $type_id;
        $type = null;
        $model = new Type();

        if ($mode == "add") {
            $data["created_at"] = date("Y-m-d H:i:s");
            $data["created_by"] = Auth::user()->id;
            $type = $model->add($data);
        } else if ($mode == "edit") {
            $type = $model->change($data, $type_id);
        }

        if (!is_object($type)) {
            if ($mode == "edit") {$errors = \App\Message\Error::get('type.change');} else { $errors = \App\Message\Error::get('type.add');}
            if (count($errors) == 0) {
                if ($mode == "edit") {$errors = \App\Message\Error::get('type.change');} else { $errors = \App\Message\Error::get('type.add');}
            }
        }
        if (isset($errors) && count($errors) > 0) {
            $message = returnErrorMsg($errors);
            $retdata['errs'] = $errors;
            $retdata['type'] = $data;
            $retdata['err_message'] = $message;
            // $this->flashError($retdata['err_message']);
            return $this->ReturnToCategoryAddPage($retdata);
        }
        $retdata["success"] = "Category " . ucfirst($mode) . "ed successfully";
        return back()->with('success', $retdata['success']);
    }

    public function deleteCategory(Request $request)
    {
        try {
            $type = Type::findOrFail($request->input('id'));

            $type->delete();

            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete category: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function ReturnToCategoryAddPage($retdata = array())
    {
        $data['title'] = ucfirst($retdata["mode"]) . ' Category';
        $data['template'] = 'admin.category.add';
        $data['script_file'] = 'add_item';
        $data['group_types'] = Grouptype::get();
        return view('with_login_common', compact('data', 'retdata'));
    }

    public function ViewAllCategories(Request $request)
    {
        $data['title'] = 'View All Categories';
        $data['template'] = 'admin.category.list';
        $data['types'] = Type::with("grouptype")->get();
        $data['group_types'] = Grouptype::get();
        return view('with_login_common', compact('data'));
    }

    public function AddSettings(Request $request, $id = null)
    {
        $mode = $id ? "edit" : "add";
        $setting = $id ? Setting::findOrFail($id) : null;
        $setting_id = $id;

        $data = [
            'title' => ucfirst($mode) . ' Settings',
            'template' => 'admin.settings.add',
            'script_file' => 'add_item',
        ];

        return view('with_login_common', compact('data', 'mode', 'setting_id', 'setting'));
    }

    public function StoreSettings(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'mode' => 'required|in:add,edit',
            'setting_id' => 'nullable|required_if:mode,edit|exists:settings,id',
        ]);
        $mode = $validated['mode'];
        $setting_id = $validated['setting_id'] ?? null;

        $setting = $setting_id ? Setting::find($setting_id) : null;

        $valueRule = 'required';
        if ($setting) {
            if ($setting->type == Setting::TYPE_BOOLEAN) {
                $valueRule .= '|in:0,1';
            } elseif ($setting->type == Setting::TYPE_NUMBER) {
                $valueRule .= '|numeric';
            } elseif ($setting->type == Setting::TYPE_STRING) {
                $valueRule .= '|string';
            }
        } else {
            $valueRule .= '|string';
        }

        $request->validate([
            'value' => $valueRule,
        ]);
        $data = [
            'name' => Str::snake($validated['name']),
            'value' => $request->value,
            'type' => $setting_id?$setting->type:$request->type
        ];

        try {
            if ($mode === "add") {
                Setting::create($data);
            } elseif ($mode === "edit" && $setting_id) {
                $setting->update($data);
            }

            return redirect()
                ->route('view-all-settings')
                ->with('success', "Setting " . ucfirst($mode) . "ed successfully.");
        } catch (\Exception $e) {
            dd($e->getMessage());
            return redirect()
                ->back()
                ->withErrors(['error' => 'An error occurred while processing your request.'])
                ->withInput();
        }
    }


    public function ViewAllSettings(Request $request)
    {
        $data['title'] = 'View All Settings';
        $data['template'] = 'admin.settings.list';
        $data['settings'] = Setting::get();
        return view('with_login_common', compact('data'));
    }

    public function runBackup()
    {
        try {
            // Run the backup command
            Artisan::call('backup:run --only-db');
            $output = Artisan::output();

            // Log the output for debugging
            \Log::info('Backup Output: ' . $output);

            return response()->json([
                'status' => 'success',
                'message' => 'Backup completed successfully.',
                'output' => $output,
            ]);
        } catch (\Exception $e) {
            \Log::error('Backup Error: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Backup failed: ' . $e->getMessage(),
            ], 500);
        }
    }

}
