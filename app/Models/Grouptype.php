<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Message\Error;
use App\Models\Model;
use App\Validator\Grouptype as Validator;
use Exception;

class Grouptype extends Model
{
    use Validator;
    protected $primaryKey = "id";
    protected $table = "group_type";
    protected $fillable = [
        'name',
        'description',
        'created_by',
        'created_at',
        'updated_by'
    ];
    protected $hidden = ['deleted_at'];

    function add($data) {
		if(!$this->validate($data)){
			Error::trigger('grouptype.add', $this->getErrors());
			return false;
		}

		try {
			$user =  parent::add($data);
			return $user;
		}
		catch(\Exception $ex){
			Error::trigger("grouptype.add", [$ex->getMessage()]);
			return [];
		}
    }

    function change(array $data, $grouptype_id,  array $change_conditions = []){
      $grouptype = static::find($grouptype_id);
      try {
        $grouptype = parent::change($data, $grouptype_id);
        return $grouptype;
      } catch(Exception $ex) {
        Error::trigger("grouptype.change", [$ex->getMessage()]);
      }
  	}
}
