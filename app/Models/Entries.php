<?php

namespace App\Models;

use App\Message\Error;
use App\Models\Model;
use App\Validator\Entries as Validator;
use Exception;

class Entries extends Model
{
    use Validator;

    protected $primaryKey = "id";

    protected $table = "book_entries";

    protected $guarded = [];

    protected $hidden = ['updated_at', 'deleted_at'];

    public function canceled_by()
    {
        return $this->belongsTo(User::class, 'canceled_by', 'id');
    }
    public function reserver()
    {
        return $this->belongsTo(User::class, 'reserved_by', 'id');
    }
    public function reserved_by()
    {
        return $this->belongsTo(User::class, 'reserved_by', 'id');
    }
    public function purchased_by()
    {
        return $this->belongsTo(User::class, 'purchased_by', 'id');
    }
    public function book()
    {
        return $this->belongsTo('App\Models\Book', 'book_id', 'id');
    }

    public function getImageAtReserveringAttribute($value)
    {
        if (!empty($value)) {
            return asset("storage/{$value}");
        }
        else return $value;
    }

    public function getImageAtReturningAttribute($value)
    {
        if (!empty($value)) {
            return asset("storage/{$value}");
        }
        else return $value;
    }

    public function add($data)
    {
        if (!$this->validate($data)) {
            Error::trigger('entries.add', $this->getErrors());
            return false;
        }

        $data['name'] = cleanNameString($data['name']);

        if (!isset($data['name']) || $data['name'] == '') {
            Error::trigger("entries.add", ["Please Enter name in English"]);
            return false;
        }

        try {
            $user = parent::add($data);
            return $user;
        } catch (\Exception $ex) {
            Error::trigger("entries.add", [$ex->getMessage()]);
            return [];
        }
    }

    public function change(array $data, $entries_id, array $change_conditions = [])
    {
        $entries = static::find($entries_id);

        try {
            $entries = parent::change($data, $entries_id);
            return $entries;
        } catch (Exception $ex) {
            Error::trigger("entries.change", [$ex->getMessage()]);
        }
    }
}
