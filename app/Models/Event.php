<?php


namespace App\Models;


use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory, Uuid;

    protected $fillable = [
        'specialist_id',
        'title',
        'description',
        'image',
        'start_at',
        'finish_at',
        'location',
        'latitude',
        'longitude',
        'announcement'
    ];

    public function specialist()
    {
        return $this->belongsTo(Specialist::class, 'specialist_id');
    }
}
