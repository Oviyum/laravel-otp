<?php

return [
    'expiry' => env('OTP_EXPIRY', 10), // minutes
    'allowed_otps' => env('MAX_OTP', 5),
    'size' => env('OTP_SIZE', 6), // number of letters
];
