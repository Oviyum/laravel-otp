<?php

namespace Fleetfoot\OTP\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Config;

class OneTimePassword extends Model
{
    protected $table = 'one_time_passwords';
    protected $dates = ['created_at', 'updated_at'];

    public function removeExpiredTokens()
    {
        OneTimePassword::where('expires_on', '<=', Carbon::now())->delete();

        return true;
    }

    public function generate($module, $id, $length)
    {
        $this->removeExpiredTokens();

        $min = str_pad(1, $length, 0);
        $max = str_pad(9, $length, 9);

        $this->token = random_int($min, $max);
        $this->module = $module;
        $this->entity_id = $id;
        $this->expires_on = Carbon::now()->addMinutes(Config::get('otp.expiry'));
        $this->save();

        return $this;
    }

    public function getTrialsCount($module, $id)
    {
        $trials = OneTimePassword::whereModule($module)
            ->whereEntityId($id)
            ->count();
    }
}
