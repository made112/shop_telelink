<?php
return [
    'version' => "1.0.0",
    'merchantID' => env('MERCHANT_ID', '800609632'),
    'acquirerID' => env('ACQUIRER_ID', '000089'),
    'url' => env('URL_REQUEST_VISA', 'https://e-commerce-test.bop.ps/EcomPayment/RedirectAuthLink'),
    'responseURL' => config('app.url')."/visa/payment/done",
    'responseURL_M' => config('app.url')."/api/v1/visa/payment/done",
    'captureFlag' => "M",
    'password' => env('PASSWORD', 'rqfjy7ui'),
    'signatureMethod' => "SHA1",
];
