<?php

namespace App\Services;

use App\Models\Central\PlatformSetting;

class SmsService
{
    public function send(string $phone, string $message): bool
    {
        $enabled = PlatformSetting::get('sms_enabled', '0');
        if ($enabled !== '1') return false;

        $provider = PlatformSetting::get('sms_provider', 'africastalking');

        if ($provider === 'africastalking') {
            return $this->sendViaAfricasTalking($phone, $message);
        } elseif ($provider === 'twilio') {
            return $this->sendViaTwilio($phone, $message);
        }

        return false;
    }

    private function sendViaAfricasTalking(string $phone, string $message): bool
    {
        $username  = PlatformSetting::get('africastalking_username');
        $apiKey    = PlatformSetting::get('africastalking_api_key');
        $senderId  = PlatformSetting::get('africastalking_sender_id');

        if (!$username || !$apiKey) throw new \Exception('AfricasTalking credentials not configured.');

        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'apiKey'       => $apiKey,
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Accept'       => 'application/json',
        ])->asForm()->post('https://api.africastalking.com/version1/messaging', [
            'username' => $username,
            'to'       => $phone,
            'message'  => $message,
            'from'     => $senderId,
        ]);

        return $response->successful();
    }

    private function sendViaTwilio(string $phone, string $message): bool
    {
        $sid   = PlatformSetting::get('twilio_sid');
        $token = PlatformSetting::get('twilio_token');
        $from  = PlatformSetting::get('twilio_from');

        if (!$sid || !$token) throw new \Exception('Twilio credentials not configured.');

        $response = \Illuminate\Support\Facades\Http::withBasicAuth($sid, $token)
            ->asForm()->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json", [
                'From' => $from,
                'To'   => $phone,
                'Body' => $message,
            ]);

        return $response->successful();
    }

    public function sendLeaveApproved(string $phone, string $employeeName, int $days): void
    {
        $this->send($phone, "Dear {$employeeName}, your leave request for {$days} day(s) has been approved. - OpEx HRIS");
    }

    public function sendLeaveRejected(string $phone, string $employeeName): void
    {
        $this->send($phone, "Dear {$employeeName}, your leave request has been declined. Please contact HR for details. - OpEx HRIS");
    }

    public function sendPayslipReady(string $phone, string $employeeName, string $period): void
    {
        $this->send($phone, "Dear {$employeeName}, your payslip for {$period} is ready. Login to HRIS to download. - OpEx HRIS");
    }

    public function sendLoanApproved(string $phone, string $employeeName, float $amount): void
    {
        $this->send($phone, "Dear {$employeeName}, your loan of KES " . number_format($amount, 0) . " has been approved and will be disbursed shortly. - OpEx HRIS");
    }

    public function sendLoanDisbursed(string $phone, string $employeeName, float $amount): void
    {
        $this->send($phone, "Dear {$employeeName}, KES " . number_format($amount, 0) . " loan has been disbursed to your bank account. - OpEx HRIS");
    }

    public function sendExpenseApproved(string $phone, string $employeeName, float $amount): void
    {
        $this->send($phone, "Dear {$employeeName}, your expense claim of KES " . number_format($amount, 0) . " has been approved. - OpEx HRIS");
    }
}
