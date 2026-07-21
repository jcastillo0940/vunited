<?php
namespace App\Contracts; interface PaymentProvider { public function create(array $payment):array; public function query(string $reference):array; public function refund(string $reference,int $amount,string $currency):array; public function verifyWebhook(string $payload,?string $signature):bool; }
