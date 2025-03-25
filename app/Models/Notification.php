<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Message\Error;
use App\Models\Model;
use App\Validator\Notification as Validator;
use Exception;

class Notification extends Model
{
    use Validator;
    protected $primaryKey = "id";
    protected $table = "notification";
    protected $fillable = [
        'ref_type',
        'ref_id',
        'notify_to',
        'subject',
        'description',
        'created_by',
        'created_at',
        'updated_at',
        'updated_by'
    ];

    function add($data) {
		if(!$this->validate($data)){
			Error::trigger('notification.add', $this->getErrors());
			return false;
		}

		try {
			$user =  parent::add($data);
			return $user;
		}
		catch(\Exception $ex){
			Error::trigger("notification.add", [$ex->getMessage()]);
			return [];
		}
    }

    function change(array $data, $notification_id,  array $change_conditions = []){
      $notification = static::find($notification_id);
      try {
        $notification = parent::change($data, $notification_id);
        return $notification;
      } catch(Exception $ex) {
        Error::trigger("notification.change", [$ex->getMessage()]);
      }
  	}
}
