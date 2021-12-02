<?php

namespace Fleetfoot\OTP\Commands;

use Fleetfoot\OTP\Models\OTP;
use Fleetfoot\OTP\Models\OtpAttempt;
use Throwable;
use Illuminate\Console\Command;

class CleanupCommand extends Command
{
    /** @var string */
    protected $signature = 'otp:clean';

    /** @var string */
    protected $description = 'Remove outdated otp and otp attempts';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('Starting cleanup...');

        try {
            $this->clean();

            $this->info('Cleanup completed!');
        } catch (Throwable $exception) {
            $this->info('Cleanup failed!');
            report($exception);

            return 1;
        }
    }

    private function clean()
    {
        (new OTP)->removeExpiredTokens();
        (new OtpAttempt())->removeOutdatedAttempts();
    }
}
