<?php

namespace App;

use App\Traits\Uuids;

class Admin extends User
{
    use Uuids;

    protected $keyType = 'string';

    protected $primaryKey = 'id';

    /**
     * @return Collection of instances of \App\Users of all admins
     */
    public static function allUsers()
    {
        // select all admins ids
        $adminsIDs = self::all()->pluck('id');

        return User::whereIn('id', $adminsIDs)->get();
    }

    /**
     * Return user instance of the admin.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'id');
    }
}
