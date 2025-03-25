<?php

namespace App\Models;

use App\Message\Error;
use App\Models\Model;
use App\Validator\Book as Validator;
use Exception;

class Book extends Model
{
    use Validator;
    protected $primaryKey = "id";
    protected $table = "book";
    protected $fillable = [
        'item_id',
        'name',
        'description',
        'group_id',
        'type_id',
        'writers',
        'cover_page',
        'latest_image',
        'year',
        'pages',
        'journal_name',
        'ean_isbn_no',
        'upc_isbn_no',
        'added_date',
        'copies',
        'ref_type',
        'ref_link',
        'group_type_id',
        'barcode',
        'barcode_url',
        'sale_or_rent',
        'price',
        'rent_price',
        'keyword',
        'featured',
        'status',
        'in_maintenance',
        'locations',
        'serial_number',
        'status_options',
        'condition',
        'weight',
        'weightKgLbs',
        'is_notify',
        'created_by',
        'updated_at',
        'updated_by',
    ];

    protected $hidden = ['deleted_at'];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function entries()
    {
        return $this->hasMany('App\Models\Entries', 'book_id', 'id');
    }

    public function group()
    {
        return $this->belongsTo('App\Models\Group', 'group_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo('App\Models\Type', 'type_id', 'id');
    }

    public function soldentries()
    {
        return $this->hasMany('App\Models\Entries', 'book_id', 'id')->where('is_sold', 1);
    }

    public function availableentries()
    {
        return $this->hasMany('App\Models\Entries', 'book_id', 'id')->where('is_sold', 0)->where('is_reserved', 0);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'item_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'item_id', 'id');
    }

    public function averageRating()
    {
        return $this->reviews()->avg('rating');
    }

    public function add($data)
    {
        if (!$this->validate($data)) {
            Error::trigger('book.add', $this->getErrors());
            return false;
        }

        $data['name'] = cleanNameString($data['name']);
        $data['locations'] = json_encode($data['locations']);

        if (!isset($data['name']) || $data['name'] == '') {
            Error::trigger("book.add", ["Please Enter name in English"]);
            return false;
        }

        try {
            $user = parent::add($data);
            return $user;
        } catch (\Exception $ex) {
            Error::trigger("book.add", [$ex->getMessage()]);
            return [];
        }
    }

    public function change(array $data, $book_id, array $change_conditions = [])
    {
        $book = static::find($book_id);
        $data['locations'] = json_encode($data['locations']);

        try {
            $book = parent::change($data, $book_id);
            return $book;
        } catch (Exception $ex) {
            Error::trigger("book.change", [$ex->getMessage()]);
        }
    }

    public function getCoverPageAttribute($value)
    {
        if (empty($value)) {
            return 'media/default-item.png';
        }

        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }
        return asset("storage/{$value}");
    }
    public function getLatestImageAttribute($value)
    {
        if (!empty($value)) {
            return asset("storage/{$value}");
        }
    }
}
