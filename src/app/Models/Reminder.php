<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'remind_at',
        'event_at',
        'sent_at',
    ];

    protected $casts = [
        'remind_at' => 'integer',
        'event_at' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
