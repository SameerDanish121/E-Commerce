<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class chat_messages extends Model
{
    protected $table = 'chat_messages';
    public $timestamps = false;
    protected $fillable = [
        'customer_id',
        'sender_type',
        'sender_id',
        'message',
        'media_type',
        'media_url',
        'reply_to',
        'is_opened',
        'is_deleted',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'is_opened' => 'boolean',
        'is_deleted' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(customers::class, 'customer_id');
    }

    public function replyTo()
    {
        return $this->belongsTo(chat_messages::class, 'reply_to');
    }

    public function replies()
    {
        return $this->hasMany(chat_messages::class, 'reply_to');
    }
    public function sender()
    {
        if ($this->sender_type === 'admin') {
            return users::find($this->sender_id);
        } elseif ($this->sender_type === 'customer') {
            return customers::find($this->sender_id);
        }
        return null;
    }
}
