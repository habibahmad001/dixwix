<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Message\Error;
use App\Models\Model;
use App\Validator\Importedfile as Validator;
use Exception;

class Importedfile extends Model
{
    use Validator;
    protected $primaryKey = "id";
    protected $table = "importedfile";
    protected $fillable = [
        'path',
        'created_by',
        'created_at',
        'updated_at',
        'updated_by'
    ];

    function add($data) {
		if(!$this->validate($data)){
			Error::trigger('Importedfile.add', $this->getErrors());
			return false;
		}
		try {
			$user =  parent::add($data);
			return $user;
		}
		catch(\Exception $ex){
			Error::trigger("Importedfile.add", [$ex->getMessage()]);
			return [];
		}
    }

    function change(array $data, $importedfile_id,  array $change_conditions = []){
      $importedfile = static::find($importedfile_id);
      try {
        $importedfile = parent::change($data, $importedfile_id);
        return $importedfile;
      } catch(Exception $ex) {
        Error::trigger("Importedfile.change", [$ex->getMessage()]);
      }
  	}
}
