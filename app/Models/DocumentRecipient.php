<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;//
use Carbon\Carbon;

class DocumentRecipient extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $primaryKey = 'recipient_id';

    protected $guarded = ['id'];
//
    /*protected $appends = ['year'];
    //protected $with = ['year'];
    public function getYearAttribute() {
        $getValue = new Carbon($this->created_at);
        return $getValue->year;
    }*/

}