<?php

namespace Fleetfoot\OTP\Models;

use Illuminate\Database\Eloquent\Model;

class OtpBlacklist extends Model
{
    protected $table = 'otp_blacklist';
    protected $dates = ['created_at', 'updated_at'];
}
