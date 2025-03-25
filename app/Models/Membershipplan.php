<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Message\Error;
use App\Models\Model;
use Exception;

class Membershipplan extends Model
{
    protected $primaryKey = "id";
    protected $table = "membership_plans";
    protected $fillable = [
        'name',
        'price',
        'created_by',
        'created_at',
        'updated_at',
        'updated_by',
        'allowed_items',
        'allowed_groups',
        'stripe_price_id',
    ];

    public function add($data) {
		if(!$this->validate($data)){
			Error::trigger('membershipplan.add', $this->getErrors());
			return false;
		}

		try {
			$user =  parent::add($data);
			return $user;
		}
		catch(Exception $ex){
			Error::trigger("membershipplan.add", [$ex->getMessage()]);
			return [];
		}
    }

    public function change(array $data, $membershipplan_id,  array $change_conditions = []){
      $membershipplan = static::find($membershipplan_id);
      try {
        $membershipplan = parent::change($data, $membershipplan_id);
        return $membershipplan;
      } catch(Exception $ex) {
        Error::trigger("membershipplan.change", [$ex->getMessage()]);
      }
  	}
}
