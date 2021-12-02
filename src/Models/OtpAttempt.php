<?php

namespace Fleetfoot\OTP\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class OtpAttempt extends Model
{
    protected $table = 'otp_attempts';
    protected $dates = ['created_at', 'updated_at'];

    public function removeOutdatedAttempts()
    {
        $attemptValidAfterTime = Carbon::now()->subMinutes(Config::get('otp.attempts_count_time'));
        OtpAttempt::where('created_at', '<', $attemptValidAfterTime)->delete();

        return true;
    }
}
