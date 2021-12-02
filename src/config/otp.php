<?php

/**
 * Fleetfoot OTP package configuration options.
 *
 * Modify the parameters to fit your needs.
 */
return [
    /**
     * Duration in minutes after which the
     * code will expire.
     */
    'expiry' => env('OTP_EXPIRY', 20),

    /**
     * Maximum OTPs allowed to be generated
     * during the expiration time.
     * When this limit exceeds, the client
     * will be blocked from further OTP
     * generation.
     */
    'allowed_otps' => env('MAX_OTP', 5),

    /**
     * Length of OTP
     */
    'size' => env('OTP_SIZE', 6),

    /**
     * Validation OTP attempts count time in minutes
     */
    'attempts_count_time' => env('OTP_COUNT_TIME', 10),

    /**
     * Allowed validation OTP attempts within duration of attempts_count_time
     */
    'allowed_attempts' => env('OTP_ALLOWED_ATTEMPTS', 5),
];
