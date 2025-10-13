<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MailLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'direction',
        'status',
        'subject',
        'to_email',
        'to_name',
        'related_type',
        'related_id',
        'context',
        'sent_at',
        'read_at',
    ];

    protected $casts = [
        'context' => 'array',
        'sent_at' => 'datetime',
        'read_at' => 'datetime',
    ];

    public function related(): MorphTo
    {
        return $this->morphTo();
    }

    public static function record(array $attributes): self
    {
        if (isset($attributes['context']) && ! is_array($attributes['context'])) {
            $attributes['context'] = (array) $attributes['context'];
        }

        return self::create(array_merge([
            'direction' => 'outgoing',
            'status' => 'sent',
            'sent_at' => now(),
        ], $attributes));
    }

    public function markRead(): void
    {
        if ($this->read_at === null) {
            $this->forceFill(['read_at' => now()])->save();
        }
    }
}
