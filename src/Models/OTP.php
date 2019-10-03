<?php

namespace Fleetfoot\OTP\Models;

use Carbon\Carbon;
use Config;
use Illuminate\Database\Eloquent\Model;

class OTP extends Model
{
    protected $table = 'otp';
    protected $dates = ['created_at', 'updated_at'];

    public function removeExpiredTokens()
    {
        OTP::where('expires_on', '<=', Carbon::now())->delete();

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
        return OTP::whereModule($module)
            ->whereEntityId($id)
            ->count();
    }
}
