<?php

namespace Fleetfoot\OTP\Models;

use Illuminate\Database\Eloquent\Model;

class OtpAttempt extends Model
{
    protected $table = 'otp_attempts';
    protected $dates = ['created_at', 'updated_at'];
}
