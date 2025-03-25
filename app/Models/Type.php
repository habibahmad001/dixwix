<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Message\Error;
use App\Models\Model;
use App\Validator\Type as Validator;
use Exception;

class Type extends Model
{
    use Validator;
    protected $primaryKey = "id";
    protected $table = "item_type";
    protected $fillable = [
        'name',
        'percentage',
        'group_type_id',
        'description',
        'created_by',
        'created_at',
        'updated_at',
        'updated_by'
    ];

    function grouptype() {
      return $this->belongsTo('App\Models\Grouptype','group_type_id', 'id');
    }

    function add($data) {
		if(!$this->validate($data)){
			Error::trigger('type.add', $this->getErrors());
			return false;
		}

		try {
			$user =  parent::add($data);
			return $user;
		}
		catch(\Exception $ex){
			Error::trigger("type.add", [$ex->getMessage()]);
			return [];
		}
    }

    function change(array $data, $type_id,  array $change_conditions = []){
      $type = static::find($type_id);
      try {
        $type = parent::change($data, $type_id);
        return $type;
      } catch(Exception $ex) {
        Error::trigger("type.change", [$ex->getMessage()]);
      }
  	}
}
