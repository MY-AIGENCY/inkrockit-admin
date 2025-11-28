<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequestNote extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'request_notes';

    /**
     * The primary key for the model.
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'request_id',
        'job_id',
        'company_id',
        'author_id',
        'type',
        'type_user',
        'removed',
        'date',
        'text',
        'required_uid',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'date' => 'datetime',
        'removed' => 'boolean',
    ];

    /**
     * Get the sample request this note belongs to.
     */
    public function request(): BelongsTo
    {
        return $this->belongsTo(SampleRequest::class, 'request_id', 'id');
    }

    /**
     * Get the job this note is associated with.
     */
    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class, 'job_id', 'id');
    }

    /**
     * Get the company this note is associated with.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    /**
     * Get the author of this note.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id', 'id');
    }

    /**
     * Scope a query to exclude removed notes.
     */
    public function scopeActive($query)
    {
        return $query->where('removed', 0);
    }
}
