<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Message\Error;
use App\Models\Model;
use App\Validator\Contact as Validator;
use Exception;

class Contact extends Model
{
    use Validator;
    protected $primaryKey = "id";
    protected $table = "contactus";
    protected $fillable = [
        'name',
        'email',
        'comment',
        'created_at'
    ];

    function add($data) {
		if(!$this->validate($data)){
			Error::trigger('contact.add', $this->getErrors());
			return false;
		}

		try {
			$user =  parent::add($data);
			return $user;
		}
		catch(\Exception $ex){
			Error::trigger("contact.add", [$ex->getMessage()]);
			return [];
		}
    }

    function change(array $data, $contact_id,  array $change_conditions = []){
      $contact = static::find($contact_id);
      try {
        $contact = parent::change($data, $contact_id);
        return $contact;
      } catch(Exception $ex) {
        Error::trigger("contact.change", [$ex->getMessage()]);
      }
  	}
}
