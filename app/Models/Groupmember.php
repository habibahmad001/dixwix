<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Message\Error;
use App\Models\Model;
use App\Validator\Groupmember as Validator;
use Exception;
use Illuminate\Database\Eloquent\SoftDeletes;

class Groupmember extends Model
{
    use Validator, SoftDeletes;
    protected $primaryKey = "id";
    protected $table = "group_member";
    protected $fillable = [
        'member_id',
        'group_id',
        'status',
        'activated',
        'member_role',
        'created_by',
        'created_at',
        'updated_at',
        'updated_by'
    ];

    function member() {
      return $this->belongsTo('App\Models\User','member_id', 'id');
    }

    function group() {
        return $this->belongsTo('App\Models\Group','group_id', 'id');
    }

    function add($data) {
		if(!$this->validate($data)){
			Error::trigger('groupmember.add', $this->getErrors());
			return false;
		}

		try {
			$user =  parent::add($data);
			return $user;
		}
		catch(\Exception $ex){
			Error::trigger("groupmember.add", [$ex->getMessage()]);
			return [];
		}
    }

    function change(array $data, $groupmember_id,  array $change_conditions = []){
      $groupmember = static::find($groupmember_id);
      try {
        $groupmember = parent::change($data, $groupmember_id);
        return $groupmember;
      } catch(Exception $ex) {
        Error::trigger("groupmember.change", [$ex->getMessage()]);
      }
  	}
}
