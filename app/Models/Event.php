<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'kind', 'event_type_id', 'created_by', 'series_id',
        'title', 'description', 'starts_at', 'ends_at',
        'capacity', 'status',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function tipo()
    {
        return $this->belongsTo(EventType::class, 'event_type_id');
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

        public function registrations()
    {
        return $this->hasMany(EventRegistration::class);
    }
}