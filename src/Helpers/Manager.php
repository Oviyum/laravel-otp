<?php

namespace Fleetfoot\OTP\Helpers;

use Config;
use Exception;
use Fleetfoot\OTP\Exceptions\MaxAllowedAttemptsExceededException;
use Fleetfoot\OTP\Exceptions\MaxAllowedOtpsExhaustedException;
use Fleetfoot\OTP\Exceptions\ServiceBlockedException;
use Fleetfoot\OTP\Helpers\OTPGenerator;
use Fleetfoot\OTP\Helpers\OTPValidator;
use Fleetfoot\OTP\Interfaces\Notifier;
use Fleetfoot\OTP\Models\OtpBlacklist;

/**
 * Wrapper class to interact with OTP module.
 * Allows generation, validation, notification.
 */
class Manager
{
    private $otpGenerator;
    private $otpValidator;

    public function __construct()
    {
        $this->otpGenerator = new Generator;
        $this->otpValidator = new Validator;
    }

    /**
     * Generates an OTP.
     * Checks:
     * If the module + id is allowed to request an OTP.
     * If allowed, and any existing token is available,
     * returns that.
     * Otherwise creates a new token.
     *
     * @param string $module  - requesting module
     * @param string $id      - ID of the requesting object
     * @param string $otpSize - Length of OTP
     *
     * @return string $otp
     * @throws ServiceBlockedException
     */
    public function generate($module, $id, $otpSize = null)
    {
        if ($this->otpValidator->isBlocked($module, $id)) {
            throw new ServiceBlockedException("OTP generation is blocked for module '$module' and id '$id'", 403);
        }

        if (Config::get('otp.allowed_otps') <= $this->otpValidator->getTrials($module, $id)) {
            throw new MaxAllowedOtpsExhaustedException("Max allowed OTPs are:" . Config::get('otp.allowed_otps') . ". Exhausted.", 403);
        }

        $otp = $this->otpGenerator->generate($module, $id, $otpSize);

        return $otp->token;
    }

    /**
     * Checks if the OTP is valid.
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
        return $this->otpValidator->isValid($otp, $module, $id);
    }

    /**
     * Notifies OTP by supported drivers.
     * $module and $id are required, if $withValidation is true.
     *
     * @param NotifierInterface $notifier
     * @param string $otp
     * @param string $to
     * @param boolean $withValidation
     * @param string $module
     * @param string $id
     *
     * @return boolean
     */
    public function notify(Notifier $notifier, $otp, $to, $withValidation = false, $module = null, $id = null)
    {
        if ($withValidation === true) {
            $notifier->withValidation($module, $id);
        }

        try {
            $notifier->notify($otp, $to);
            $status = true;
        } catch (Exception $e) {
            $status = false;
        }

        return $status;
    }

    /**
     * Remove the OTP from db.
     * @param string $otp
     * @param string $module
     * @param string $id
     *
     * @return boolean
     */
    public function useOtp($otp, $module, $id)
    {
        $this->otpGenerator->delete($otp, $module, $id);

        return true;
    }

    /**
     * Block (module + id).
     * @param string $module
     * @param string $id
     *
     * @return boolean
     */
    public function block($module, $id)
    {
        $blacklist = new OtpBlacklist;
        $blacklist->module = $module;
        $blacklist->entity_id = $id;
        $blacklist->save();

        return true;
    }

    /**
     * Unblock (module + id).
     * @param string $module
     * @param string $id
     *
     * @return boolean
     */
    public function unblock($module, $id): int
    {
        return (bool)OtpBlacklist::query()
            ->where('module', $module)
            ->where('entity_id', $id)
            ->delete();
    }
}
