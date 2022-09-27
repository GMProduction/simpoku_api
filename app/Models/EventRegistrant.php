<?php


namespace App\Models;


use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventRegistrant extends Model
{
    use HasFactory, Uuid;

    protected $fillable = [
        'user_id',
        'reference_id',
        'sub_total',
        'discount',
        'total',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
