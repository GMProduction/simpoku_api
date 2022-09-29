<?php


namespace App\Models;


use App\Traits\Uuid;
use Carbon\Carbon;
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
        'announcement',
        'price',
    ];

    public function specialist()
    {
        return $this->belongsTo(Specialist::class, 'specialist_id');
    }

    public function getStatusAttribute()
    {
        $now = Carbon::now('Asia/Jakarta')->format('Y-m-d');
        if ($this->start_at <= $now && $now <= $this->finish_at) {
            return 'ongoing';
        } else if ($now < $this->start_at) {
            return 'upcoming';
        } else {
            return 'finish';
        }
    }
}
