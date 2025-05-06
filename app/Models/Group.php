<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Message\Error;
use App\Models\Model;
use App\Validator\Group as Validator;
use Exception;

class Group extends Model
{
    use Validator;
    protected $primaryKey = "id";
    protected $table = "group";
    protected $fillable = [
        'title',
        'description',
        'state',
        'group_type_id',
        'zip_code',
	    'country',
        'to_be_deleted_at',
        'group_picture',
      	'qrcode_url',
        'created_by',
        'created_at',
        'updated_at',
        'updated_by',
      	'status',
      	'locations',
        'default'
    ];

    function createdBy() {
      return $this->belongsTo(User::class,'created_by', 'id');
    }

    function grouptype() {
      return $this->belongsTo('App\Models\Grouptype','group_type_id', 'id');
    }

    function groupmembers() {
      return $this->hasMany('App\Models\Groupmember','group_id', 'id');
    }

    function loadhistory() {
      return $this->hasMany('App\Models\LoanHistory','group_id', 'id');
    }

  	 function groupMember() {
        return $this->hasOne('App\Models\Groupmember','group_id');
      }

    function addedmembers() {
      return $this->groupmembers()->where("status","added");
    }

    function books() {
      return $this->hasMany('App\Models\Book','group_id', 'id');
    }

    public function invitedMembers()
    {
        return $this->hasMany(GroupUserInvited::class, 'group_id', 'id');
    }

    function add($data) {
		if(!$this->validate($data)){
			Error::trigger('group.add', $this->getErrors());
			return false;
		}

		try {
            if(!empty($data['locations']))
              {
                  $data['locations'] = json_encode($data['locations']);
              }
			$user =  parent::add($data);
			return $user;
		}
		catch(\Exception $ex){
			Error::trigger("group.add", [$ex->getMessage()]);
			return [];
		}
    }

    function change(array $data, $group_id,  array $change_conditions = []){
      $group = static::find($group_id);
      if(!empty($data['locations']))
            {
                $data['locations'] = json_encode($data['locations']);
            }
      try {
        $group = parent::change($data, $group_id);
        return $group;
      } catch(Exception $ex) {
        Error::trigger("group.change", [$ex->getMessage()]);
      }
  	}
}
