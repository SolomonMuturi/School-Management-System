<?php

namespace App\Services;

use App\Helpers\Qs;
use Illuminate\Support\Facades\Http;

class MpesaService
{
    protected $env;
    protected $key;
    protected $secret;
    protected $passkey;
    protected $shortcode;
    protected $callbackUrl;
    protected $accountRef;
    protected $transactionType;
    protected $baseUrl;

    public function __construct()
    {
        $this->env = Qs::getSetting('mpesa_environment') ?: 'sandbox';
        $this->key = Qs::getSetting('mpesa_consumer_key');
        $this->secret = Qs::getSetting('mpesa_consumer_secret');
        $this->passkey = Qs::getSetting('mpesa_passkey');
        $this->shortcode = Qs::getSetting('mpesa_shortcode');
        $this->callbackUrl = Qs::getSetting('mpesa_callback_url') ?: url('/finance/mpesa/callback');
        $this->accountRef = Qs::getSetting('mpesa_account_reference') ?: 'FEE';
        $this->transactionType = Qs::getSetting('mpesa_transaction_type') ?: 'CustomerPayBillOnline';
        $this->baseUrl = $this->env === 'live'
            ? 'https://api.safaricom.co.ke'
            : 'https://sandbox.safaricom.co.ke';
    }

    public function configured()
    {
        return $this->key && $this->secret && $this->passkey && $this->shortcode;
    }

    public function getToken()
    {
        $res = Http::withBasicAuth($this->key, $this->secret)
            ->get($this->baseUrl . '/oauth/v1/generate?grant_type=client_credentials');

        return $res->json('access_token');
    }

    public function stkPush($phone, $amount, $accountRef = null, $description = 'School fees payment')
    {
        $token = $this->getToken();
        if (!$token) {
            return ['ok' => false, 'errorMessage' => 'Could not obtain an M-Pesa access token.'];
        }

        $timestamp = date('YmdHis');
        $password = base64_encode($this->shortcode . $this->passkey . $timestamp);

        $payload = [
            'BusinessShortCode' => $this->shortcode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => $this->transactionType,
            'Amount' => (int) round($amount),
            'PartyA' => $this->normalizePhone($phone),
            'PartyB' => $this->shortcode,
            'PhoneNumber' => $this->normalizePhone($phone),
            'CallBackURL' => $this->callbackUrl,
            'AccountReference' => $accountRef ?: $this->accountRef,
            'TransactionDesc' => substr($description, 0, 30),
        ];

        $res = Http::withToken($token)->post($this->baseUrl . '/mpesa/stkpush/v1/processrequest', $payload);

        return $res->json() ?: ['ok' => false, 'errorMessage' => 'No response from M-Pesa.'];
    }

    public function queryStatus($checkoutRequestId)
    {
        $token = $this->getToken();
        if (!$token) {
            return ['ok' => false, 'errorMessage' => 'Could not obtain an M-Pesa access token.'];
        }

        $timestamp = date('YmdHis');
        $password = base64_encode($this->shortcode . $this->passkey . $timestamp);

        $payload = [
            'BusinessShortCode' => $this->shortcode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'CheckoutRequestID' => $checkoutRequestId,
        ];

        $res = Http::withToken($token)->post($this->baseUrl . '/mpesa/stkpushquery/v1/query', $payload);

        return $res->json() ?: ['ok' => false, 'errorMessage' => 'No response from M-Pesa.'];
    }

    public function normalizePhone($phone)
    {
        $phone = preg_replace('/\D/', '', (string) $phone);

        if (strlen($phone) === 9) {
            $phone = '254' . $phone;
        } elseif (strlen($phone) === 10 && $phone[0] === '0') {
            $phone = '254' . substr($phone, 1);
        } elseif (strlen($phone) === 13 && $phone[0] === '254') {
            $phone = substr($phone, 1);
        }

        return $phone;
    }
}