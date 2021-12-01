<?php

namespace Fleetfoot\OTP\Helpers;

use Fleetfoot\OTP\Exceptions\MaxAllowedAttemptsExceededException;
use Fleetfoot\OTP\Models\OTP;
use Fleetfoot\OTP\Models\OtpAttempt;
use Fleetfoot\OTP\Models\OtpBlacklist;
use Carbon\Carbon;
use Config;

/**
 * OTP validity checker.
 */
class Validator
{
    /**
     * Check if the given OTP is valid.
     *
     * @param string $otp
     * @param string $module
     * @param string $id
     * @throws MaxAllowedAttemptsExceededException
     *
     * @return boolean
     */
    public function isValid($otp, $module, $id)
    {
        (new OTP)->removeExpiredTokens();

        if ($this->isBlocked($module, $id)) {
            return false;
        }

        if ($this->isAttemptsExceeded($module, $id)) {
            throw new MaxAllowedAttemptsExceededException("Max allowed attempts exceeded. Try again later.", 403);
        }

        $otp = OTP::query()
            ->where('token', $otp)
            ->where('module', $module)
            ->where('entity_id', $id)
            ->first();

        if (!$otp) {
            $this->addAttempt($module, $id);

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

    /**
     * Check if attempts exceeded
     *
     * @param string $module
     * @param string $id
     *
     * @return int
     */
    public function isAttemptsExceeded($module, $id)
    {
        if ($this->countAttempts($module, $id) >= Config::get('otp.allowed_attempts')) {
            return true;
        }

        return false;
    }

    /**
     * Get number of attempts made to validate OTP.
     *
     * @param string $module
     * @param string $id
     *
     * @return int
     */
    public function countAttempts($module, $id)
    {
        return OtpAttempt::where('module', $module)
            ->where('entity_id', $id)
            ->where('created_at', '>=', Carbon::now()->subMinutes(Config::get('otp.attempts_count_time')))
            ->count();
    }

    /**
     * Write validate check attempt for (module + id).
     * @param string $module
     * @param string $id
     *
     * @return boolean
     */
    private function _addAttempt($module, $id)
    {
        $otpAttempt = new OtpAttempt;
        $otpAttempt->module = $module;
        $otpAttempt->entity_id = $id;
        $otpAttempt->save();

        return true;
    }
}
