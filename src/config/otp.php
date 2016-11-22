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
    'expiry' => env('OTP_EXPIRY', 10),

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
];
