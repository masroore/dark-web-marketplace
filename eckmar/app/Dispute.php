<?php

namespace App;

use App\Exceptions\RequestException;
use App\Traits\Uuids;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Dispute extends Model
{
    use Uuids;

    public $incrementing = false;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    /**
     * Messages of the dispute.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messages()
    {
        return $this->hasMany(DisputeMessage::class, 'dispute_id');
    }

    /**
     * Purchase.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function purchase()
    {
        return $this->hasOne(Purchase::class, 'dispute_id', 'id');
    }

    /**
     * Returns if the user can post message on the dispute.
     */
    public function canPostMessage(): bool
    {
        // if it is not logged in
        if (auth()->check() == false) {
            return false;
        }

        // Define when the user can post
        if ($this->purchase->isVendor()) {
            return true;
        }
        if ($this->purchase->isBuyer()) {
            return true;
        }
        if (auth()->user()->isAdmin()) {
            return true;
        }

        return false;

    }

    /**
     * Posts new message to this dispute.
     */
    public function newMessage(string $message): void
    {
        if (!auth()->check()) {
            throw new RequestException('You must be logged in to send new message!');
        }

        if (!$this->canPostMessage()) {
            throw new RequestException('You can\'t post messages to this dispute');
        }

        if ($this->isResolved()) {
            throw new RequestException('Can\'t post new messages when it is resolved');
        }

        $newMessage = new DisputeMessage();
        $newMessage->message = $message;
        $newMessage->setDispute($this);
        $newMessage->setAuthor(auth()->user());
        $newMessage->save();

    }

    /**
     * Returns if the dispute is resolved.
     *
     * @return bool
     */
    public function isResolved()
    {
        return $this->winner_id != null;
    }

    /**
     * Returns the winner of the dispute, can be null.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function winner()
    {
        return $this->hasOne(User::class, 'id', 'winner_id');
    }

    /**
     * Returns true if logged user is the winner of this purchase.
     */
    public function isWinner(): bool
    {
        // if user is logged in
        if (auth()->check()) {
            return auth()->user()->id == $this->winner->id;
        }

        return false;
    }

    /**
     * Time differencfe since the opened dispute.
     *
     * @return string
     */
    public function timeDiff()
    {
        return Carbon::parse($this->created_at)->diffForHumans();
    }
}
