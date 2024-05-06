<?php

namespace App;

use App\Traits\Uuids;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class DisputeMessage extends Model
{
    use Uuids;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    /**
     * \App\Dispute instance.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function dispute()
    {
        return $this->belongsTo(Dispute::class, 'dispute_id');
    }

    /**
     * Set dispute of this message.
     */
    public function setDispute(Dispute $dispute): void
    {
        $this->dispute_id = $dispute->id;
    }

    /**
     * Set author of the message.
     */
    public function setAuthor(User $author): void
    {
        $this->author_id = $author->id;
    }

    /**
     * Return author of the purchase.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function author()
    {
        return $this->hasOne(User::class, 'id', 'author_id');
    }

    /**
     * Past time since message is created.
     *
     * @return string
     */
    public function getTimeAgoAttribute()
    {
        return Carbon::parse($this->created_at)->diffForHumans();
    }
}
