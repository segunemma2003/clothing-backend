<?php

namespace App\Http\Controllers;

use Digikraaft\PaystackWebhooks\Http\Controllers\WebhooksController as PaystackWebhooksController;
use Illuminate\Http\Request;

class WebhookController extends PaystackWebhooksController
{
     /**
     * Handle charge success.
     *
     * @param  array  $payload
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handleChargeSuccess($payload)
    {
        // Handle The Event
    }
}
