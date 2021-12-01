# laravel-otp

Laravel 5 OTP Generation.

The module generates OTPs and validates them. You can plug your own notifier (such as AWS SNS) to send the OTPs to various channels.

Several configuration options are provided:
- Expiration duration
- Maximum OTPs allowed for a client
- Length of OTP

---

# Installation

### Via composer

1. Run `composer require fleetfoot/otp`
2. Add `Fleetfoot\OTP\OTPServiceProvider` to your providers array in `config/app.php`
3. Run `composer dump-autoload`
4. Run `php artisan vendor:publish`
5. Run `php artisan migrate`

Done!

---

# Configuration options

The package publishes `config/otp.php`. It is well documented.

---

# Usage

The package provides with the following helpers:
1. `Manager`
2. `Generator`
3. `Validator`

You can use `Manager` to interact with the whole module. It acts as a wrapper for the complete functionality. However, you are free to use other helpers to generate and validate the OTPs.

### Generate an OTP
To generate an OTP, call `generate` method of `Manager`. This takes two arguments: module and ID. Both are strings. You can pass anything here, but keep in mind that this combination will be used to validate the OTP.
For e.g. `$manager->generate('users', '1')` will return an OTP for the combination of 'users' module and ID '1'.

### Validate an OTP
To validate, call `isValid()` of the manager. It will return boolean based on the validity of the OTP.
Validation makes sure the module + ID is not blocked, and the token is not expired.

### Blocking and Unblocking
You won't be able to validate OTP and generate anymore OTPs for blocked module + ID combination.

To block use:
`$manager->block('users', '1')`

To unblock use:
`$manager->unblock('users', '1')`

### Notifications
The manager gives `notify()` method which accepts any implementation of `Notifier` interface. You can implement this interface as per your business logic.

You might want to call `useOtp()` of the manager after the varification process completes. If you do not call this method, OTP will remain valid till it reaches its expiry limit.


---

# Contributions

All contributions are welcome! Create a fork, create PRs, discuss!

---

# TODO
1. Add option for numeric/alphanumeric code generation
2. Provide example implementation(s) for Notifier
4. Find a better way to remove expired OTPs from DB
