<?php

namespace Fleetfoot\OTP\Helpers;

use Fleetfoot\OTP\Models\OneTimePassword as OTP;
use Fleetfoot\OTP\Models\OtpBlacklist;

/**
 * OTP validaty checker.
 */
class Validator
{
    /**
     * Check if the given OTP is valid.
     *
     * @param string $otp
     * @param string $module
     * @param string $id
     *
     * @return boolean
     */
    public function isValid($otp, $module, $id)
    {
        (new OTP)->removeExpiredTokens();

        if ($this->isBlocked($module, $id)) {
            return false;
        }

        $otp = OTP::where('token', $otp)
            ->where('module', $module)
            ->where('entity_id', $id)
            ->first();

        if ($otp) {
            return false;
        }

        return true;
    }

    /**
     * Check if the module + id has been blacklisted.
     *
     * @param string $module
     * @param string $id
     *
     * @return boolean
     */
    public function isBlocked($module, $id)
    {
        $blocked = OtpBlacklist::whereModule($module)
            ->whereEntityId($id)
            ->first();

        return $blocked ? true : false;
    }

    /**
     * Get number of trials made to generate OTP.
     *
     * @param string $module
     * @param string $id
     *
     * @return int
     */
    public function getTrials($module, $id)
    {
        return (new OTP)->getTrialsCount($module, $id);
    }
}
