<?php

namespace Fleetfoot\OTP\Helpers;

use Config;
use Exception;
use Fleetfoot\OTP\Exceptions\MaxAllowedOtpsExhaustedException;
use Fleetfoot\OTP\Exceptions\ServiceBlockedException;
use Fleetfoot\OTP\Models\OneTimePassword as OTP;
use Fleetfoot\OTP\Helpers\OTPGenerator;
use Fleetfoot\OTP\Helpers\OTPNotifier;
use Fleetfoot\OTP\Helpers\OTPValidator;

/**
 * Wrapper class to interact with OTP module.
 * Allows generation, validation, notification.
 */
class Manager
{
    private $otpGenerator;
    private $otpNotifier;
    private $otpValidator;

    public function __construct()
    {
        $this->otpGenerator = new Generator;
        $this->otpNotifier = new Notifier;
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
     * @param string $module - requesting module
     * @param string $id     - ID of the requesting object
     *
     * @return string $otp
     * @throws ServiceBlockedException
     */
    public function generate($module, $id)
    {
        if ($this->otpValidator->isBlocked()) {
            throw new ServiceBlockedException("Service blocked due to too many requests", 403);
        }

        if (Config::get('otp.allowed_otps') >= $this->otpValidator->getTrials()) {
            $this->_block($module, $id);

            throw new MaxAllowedOtpsExhaustedException("Max allowed OTPs are:" . Config::get('otp.allowed_otps') . ". Exahusted.", 403);
        }

        $otp = $this->otpGenerator->generate($module, $id);

        return $otp->token;
    }

    /**
     * Checks if the OTP is valid.
     *
     * @param string $otp
     * @param string $module
     * @param string $id
     *
     * @return boolean
     */
    public function isValid($otp, $module, $id)
    {
        return $this->otpValidator->isValid($otp, $module, $id);
    }

    /**
     * Notifies OTP by supported drivers.
     *
     * @param NotifierInterface $notifier
     * @param string $otp
     * @param string $to
     *
     * @return boolean
     */
    public function notify(NotifierInterface $notifier, $otp, $to)
    {
        if ($withValidation === true) {
            $notifier->validate();
        }

        try {
            $this->otpNotifier->notify($otp, $to);
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
    private function _block($module, $id)
    {
        $blacklist = new OtpBlacklist;
        $blacklist->module = $module;
        $blacklist->entity_id = $id;
        $blaclist->save();

        return true;
    }
}
