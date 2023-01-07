<?php


namespace App\Http\Traits;


use App\Student;
use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

trait UtilityTrait
{

    public function sendSMS($phone, $sms)
    {
        $headers = [
            "Content-Type" => "application/json",
            "cmd" => "SEND_SMS",
            "domain" => env("SMS_DOMAIN")
        ];
        $response = Http::withHeaders($headers)->post(env("SMS_URL"), [
            "src" => "TapAndEat",
            "dest" => "25" . $phone,
            "message" => $sms,
            "wait" => 0,
            "contractId" => env("SMS_CONTRACT_ID")
        ]);
        return json_decode($response->body())->code == 100;

    }

    public function momoPay($tx_ref, $amount, $phoneNumber)
    {
        $URL = "https://opay-api.oltranz.com/opay/paymentrequest";
        Http::post($URL, [
            "telephoneNumber" => "25" . $phoneNumber,
            "amount" => $amount,
            "organizationId" => env("OPAY_ORGANIZATION_ID"),
            "description" => "Payment",
            "callbackUrl" => "https://99bb-105-178-121-196.eu.ngrok.io/api/opay/payment-response",
            "transactionId" => $tx_ref
        ]);
    }

    public function pay($amount, $phoneNumber)
    {
        $transactionId = uniqid();
        $student = Student::where('phoneNumber', $phoneNumber)->first();
        $this->momoPay($transactionId, $amount, $phoneNumber);
        Transaction::create(
            [
                "phone_number" => $phoneNumber,
                "transaction_id" => $transactionId,
                "amount" => $amount,
                "student_id" => $student->id
            ]
        );
    }

    public function verifyStudent($phoneNumber, $pin)
    {
        $student = Student::where('phoneNumber', $phoneNumber)->first();
        $pinValid = $pin == $student->pin;
        return $pinValid;

    }
}
