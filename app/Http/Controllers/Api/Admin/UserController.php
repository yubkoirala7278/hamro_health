<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\PhoneNumberRequest;
use App\Http\Requests\OtpRequest;
use App\Exceptions\PhoneNumberException;

class UserController extends Controller
{
    /**
     * Adds phone number to user account.
     *
     * @param  PhoneNumberRequest  $request
     *
     * @return JsonResponse
     */
    public function addPhoneNumber(PhoneNumberRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $user = $request->user();
            $existingPhoneNumber = $user->phone_number;
            $newPhoneNumber = $request->input('phone_number');

            $this->checkConditionsForAddingPhoneNumber($existingPhoneNumber, $newPhoneNumber);
            $this->savePhoneNumberInformation($newPhoneNumber, $user, 1);

            // Code to send sms with otp, needs to be implemented
            $user->sendPhoneVerificationSms();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Phone number added successfully. An OTP has been sent to verify your phone number.'
            ]);
        } catch (PhoneNumberException $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            logger()->error($e);

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.'
            ], 500);
        }
    }

    /**
     * Resends the OTP to the user's phone number.
     *
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function reSendOtp(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $user = $request->user();
            $existingPhoneNumber = $user->phone_number;

            $this->checkConditionsForResendingOtp($existingPhoneNumber, $user);
            $this->savePhoneNumberInformation($existingPhoneNumber, $user, ++$user->otp_attempts);

            // Code to send sms with otp, needs to be implemented
            $user->sendPhoneVerificationSms();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'An OTP has been resent to verify your phone number.'
            ]);
        } catch (PhoneNumberException $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            logger()->error($e);

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.'
            ], 500);
        }
    }

    /**
     * Verifies the OTP provided by the user.
     *
     * @param  OtpRequest  $request
     *
     * @return JsonResponse
     */
    public function verifyOtp(OtpRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $user = $request->user();
            $otp = $request->input('otp_code');

            $this->checkConditionsForVerifyingOtp($user, $otp);
            $user->phone_number_verified_at = now();
            $user->save();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Phone number verified successfully.'
            ]);
        } catch (PhoneNumberException $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            logger()->error($e);

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.'
            ], 500);
        }
    }

    /**
     * Saves the phone number information for the user.
     *
     * @param $newPhoneNumber
     * @param $user
     * @param $attempt
     *
     * @return void
     *
     * @throws \Exception
     */
    public function savePhoneNumberInformation($newPhoneNumber, $user, $attempt): void
    {
        $user->phone_number = $newPhoneNumber;
        $user->otp_created_at = now();
        $user->otp_code = random_int(100000, 999999);
        $user->otp_attempts = $attempt;
        $user->phone_number_verified_at = null;
        $user->save();
    }

    /**
     * Throws exception if the conditions are not met for adding a phone number.
     *
     * @param $existingPhoneNumber
     * @param $newPhoneNumber
     *
     * @return void
     *
     * @throws PhoneNumberException
     */
    public function checkConditionsForAddingPhoneNumber($existingPhoneNumber, $newPhoneNumber): void
    {
        if ($existingPhoneNumber === $newPhoneNumber) {
            throw new PhoneNumberException('Phone number already exists.');
        }
    }

    /**
     * Checks the conditions for resending the OTP.
     *
     * @param $existingPhoneNumber
     * @param $user
     *
     * @return void
     *
     * @throws PhoneNumberException
     */
    public function checkConditionsForResendingOtp($existingPhoneNumber, $user): void
    {
        if (!$existingPhoneNumber) {
            throw new PhoneNumberException('Phone number not found for user.');
        }

        if ($user->phone_number_verified_at) {
            throw new PhoneNumberException('Phone number already verified.');
        }

        if ($user->otp_attempts >= 3) {
            throw new PhoneNumberException('Maximum attempts reached. Please reach out to us to reset.');
        }
    }

    /**
     * Checks the conditions for verifying the OTP.
     *
     * @param $user
     * @param $otp
     *
     * @return void
     *
     * @throws PhoneNumberException
     */
    public function checkConditionsForVerifyingOtp($user, $otp): void
    {
        if (!$user->phone_number) {
            throw new PhoneNumberException('Phone number not found for user.');
        }

        if ($user->phone_number_verified_at) {
            throw new PhoneNumberException('Phone number already verified.');
        }

        if ($user->otp_code !== (int)$otp) {
            throw new PhoneNumberException('Invalid OTP code.');
        }

        if ($user->otp_created_at->diffInMinutes(now()) > 30) {
            throw new PhoneNumberException('OTP expired. Please request a new one.');
        }
    }
}
