<?php

namespace Fleetfoot\OTP\Helpers;

use Config;
use Fleetfoot\OTP\Models\OTP;

/**
 * OTP generation facilitator.
 */
class Generator
{
    private $otpModel;

    public function __construct()
    {
        $this->otpModel = new OTP;
    }

    /**
     * Generates an OTP.
     * Checks:
     * If any existing token is available,
     * returns that.
     * Otherwise creates a new token.
     *
     * @param string $module - requesting module
     * @param string $id     - ID of the requesting object
     *
     * @return OTP $otp
     *
     */
    public function generate($module, $id, $otpSize = null)
    {
        $this->otpModel->removeExpiredTokens();

        $otp = $this->otpModel
            ->whereModule($module)
            ->whereEntityId($id)
            ->first();

        if ($otp) {
            return $otp;
        } else {
            $otp = new OTP;
        }

        if (!$otpSize) {
            $otpSize = Config::get('otp.size');
        }

        $otp = $otp->generate($module, $id, $otpSize);

        return $otp;
    }

    /**
     * Remove OTP from database.
     * @param string $otp
     * @param string $module
     * @param string $id
     *
     * @return boolean
     */
    public function delete($otp, $module, $id)
    {
        $this->otpModel
            ->whereToken($otp)
            ->whereModule($module)
            ->whereEntityId($id)
            ->delete();

        return true;
    }
}
