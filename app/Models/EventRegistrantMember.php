<?php


namespace App\Models;


use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventRegistrantMember extends Model
{
    use HasFactory, Uuid;

    protected $fillable = [
        'event_registrant_id',
        'code',
        'name',
        'phone'
    ];

    public function eventRegistrant()
    {
        return $this->belongsTo(EventRegistrant::class, 'event_registrant_id');
    }
}
