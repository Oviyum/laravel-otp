<?php

namespace Fleetfoot\OTP\Helpers;

use Carbon\Carbon;
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
    public function generate($module, $id)
    {
        $otp = $this->otpModel
            ->where('module', $module)
            ->where('entity_id', $id)
            ->where('expires_on', '>', Carbon::now())
            ->orderByDesc('id')
            ->first();

        if ($otp) {
            return $otp;
        } else {
            $otp = new OTP;
        }

        $otp = $otp->generate($module, $id, Config::get('otp.size'));

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
