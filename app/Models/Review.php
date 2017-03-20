<?php

namespace Rogue\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['event_id', 'signup_id', 'northstar_id', 'admin_northstar_id', 'status', 'old_status', 'comment', 'postable_id', 'postable_type'];

    /**
     * Each review has events.
     */
    public function events()
    {
        $this->morphMany('Rogue\Models\Event', 'eventable');
    }

    /**
     * Each review belongs to a post.
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
