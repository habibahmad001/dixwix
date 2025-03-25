<?php

namespace App\Models;

use App\Models\Model;
use App\Validator\User as Validator;
use Illuminate\Support\Facades\Hash;
use App\Message\Error;

class Usermodel extends Model
{
    use Validator;

    protected $primaryKey = "id";
    protected $table = "users";
    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at',
        'group_type',
        'activation_code',
        'profile_pic',
        'source',
        'external_id',
        'biodata',
        'address',
        'state',
        'zipcode',
        'locations',
        'created_at',
        'updated_at',
    ];
    // protected $attributes = ['group_id'=>Null, 'pass_change' => Null, 'status' => 1, 'last_login' => Null, 'role_id' => Null];
    // public $timestamps = true;
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ['password'];


    public function add($data)
    {
        if (!$this->validate($data)) {
            Error::trigger('user.add', $this->getErrors());
            return false;
        }

        $data['name'] = cleanNameString($data['name']);

        if (!isset($data['name']) || $data['name'] == '') {
            Error::trigger("user.add", ["Please Enter name in English"]);
            return false;
        }

        if (isset($data['email'])) {
            $data['email'] = (string)$data['email'];
        }

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        try {
            $user = parent::add($data);
            return $user;//->toArray();
        } catch (\Exception $ex) {
            Error::trigger("user.add", [$ex->getMessage()]);
            return [];
        }
    }

    public function change(array $data, $user_id, array $change_conditions = [])
    {
        $user = static::find($user_id);
        $data['name'] = cleanNameString($data['name']);
        if(isset($data['locations'])){
            $data['locations'] = json_encode($data['locations']);
        }else{
            $data['locations'] = NULL;
        }

        if (!isset($data['name']) || $data['name'] == '') {
            Error::trigger("user.change", ["Please Enter Name. Special Characters are not allowed."]);
            return false;
        }

        try {
            $user = parent::change($data, $user_id);
            return $user;
        } catch (Exception $ex) {
            Error::trigger("user.change", [$ex->getMessage()]);
        }
    }
}
