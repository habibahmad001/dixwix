<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Message\Error;
use App\Models\Model;
use App\Validator\Usermembership as Validator;
use Exception;

class Usermembership extends Model
{
    use Validator;
    protected $primaryKey = "id";
    protected $table = "user_membership";
    protected $fillable = [
        'user_id',
        'plan_id',
        'start_date',
        'end_date',
        'is_active',
        'created_by',
        'updated_by',
        'stripe_subscription_id'
    ];

    function plan() {
        return $this->belongsTo('App\Models\Membershipplan','plan_id', 'id');
    }

    function add($data) {
		if(!$this->validate($data)){
			Error::trigger('usermembership.add', $this->getErrors());
			return false;
		}

		try {
			$user =  parent::add($data);
			return $user;
		}
		catch(\Exception $ex){
			Error::trigger("usermembership.add", [$ex->getMessage()]);
			return [];
		}
    }

    function change(array $data, $usermembership_id,  array $change_conditions = []){
      $usermembership = static::find($usermembership_id);
      try {
        $usermembership = parent::change($data, $usermembership_id);
        return $usermembership;
      } catch(Exception $ex) {
        Error::trigger("usermembership.change", [$ex->getMessage()]);
      }
  	}
}
