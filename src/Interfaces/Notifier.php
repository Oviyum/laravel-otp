<?php

namespace Fleetfoot\OTP\Interfaces;

interface Notifier
{
    public function notify($otp, $to);
    public function validate();
}
