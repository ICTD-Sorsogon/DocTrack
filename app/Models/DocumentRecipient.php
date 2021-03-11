<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentRecipient extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $primaryKey = 'recipient_id';

    protected $guarded = ['id'];

    public function document()
    {
        return $this->belongsTo('App\Models\Document', 'document_id');
    }

    public function expire()
    {
        $days = Carbon::now()->diffInDays($this->attributes['updated_at']);
        if($this->priority_level() == 1 && $days > 7){
            return true;
        }
        else if($this->priority_level() == 2 && $days > 15){
            return true;
        }
        else if($this->priority_level() == 3 && $days > 30){
            return true;
        }

        return false;
    }

    public function priority_level()
    {
        return $this->document->priority_level;
    }
}
