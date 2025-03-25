<?php

namespace App\Models;

use App\Message\Error;
use App\Validator\Setting as Validator;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Model;

class Setting extends Model
{
    use Validator;
    protected $primaryKey = "id";
    protected $table = "settings";

    const TYPE_BOOLEAN = 1;
    const TYPE_NUMBER = 2;
    const TYPE_STRING = 3;

    public static $TYPE_TEXT = [
        self::TYPE_BOOLEAN   => "Boolean",
        self::TYPE_NUMBER  => "Number",
        self::TYPE_STRING  => "String",
    ];

    protected $fillable = [
        'name',
        'value',
        'type',
        'created_at',
        'updated_at'
    ];

    public function add($data) {
        if(!$this->validate($data)){
            Error::trigger('setting.add', $this->getErrors());
            return false;
        }

        try {
            $user =  parent::add($data);
            return $user;
        }
        catch(\Exception $ex){
            Error::trigger("setting.add", [$ex->getMessage()]);
            return [];
        }
    }

    public function change(array $data, $setting_id,  array $change_conditions = []){
        $setting = static::find($setting_id);
        try {
            $setting = parent::change($data, $setting_id);
            return $setting;
        } catch(Exception $ex) {
            Error::trigger("setting.change", [$ex->getMessage()]);
        }
    }
}
