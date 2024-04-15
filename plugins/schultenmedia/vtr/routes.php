<?php

Route::post('/mollie/webhook', function(Request $request) {
    \Log::info('webhook  ' . json_encode(post()));

    $data = post();

    $mollie = new \Mollie\Api\MollieApiClient();

    if (\SchultenMedia\VTR\Models\Settings::get("mollie_mode") == "live") {
        $apiKey = \SchultenMedia\VTR\Models\Settings::get("live_api_key");
    } else {
        $apiKey = \SchultenMedia\VTR\Models\Settings::get("test_api_key");
    }

    $mollie->setApiKey($apiKey);

    try {
        $paymentData = $mollie->payments->get($data['id']);

        if($paymentData->isPaid()) {

            if(!empty($paymentData->metadata->security_token)) {
                $security_token = $paymentData->metadata->security_token;

                $booking = \Tailor\Models\EntryRecord::inSection('Shop\Booking')->where('security_token', $security_token)->firstOrFail();

                $booking->paid = true;
                $booking->save();

//                \Log::info('webhook booking ' . json_encode($booking));

                $data = $booking->toArray();

//                \Log::info('webhook data ' . json_encode($data));

                $admin_email = \SchultenMedia\VTR\Models\Settings::get("admin_email");

                if(!empty($admin_email)) {
                    \Mail::send('schultenmedia.vtr::mail.booking-admin', $data, function ($message) use ($admin_email) {
                        $message->to($admin_email);
                    });
                }

                $templateName = 'schultenmedia.vtr::mail.booking';
                try {

                    $translator = \SchultenMedia\Translate\Classes\Translator::instance();
                    $locale = $translator->getLocale();
                    $locale = explode('-', $locale);
                    $locale = $locale[0];

                    if (file_exists(__DIR__ . '/../views/mail/booking-' . $locale . '.htm')) {
                        $templateName = 'schultenmedia.vtr::mail.booking-'.$locale;
                    }

                } catch (\Exception $e) {
                }

                Mail::send($templateName, $data, function($message) use ($data) {
                    $message->to($data['email']);
                });
            }

        }

    } catch (\Mollie\Api\Exceptions\ApiException $exception) {
        $request = $exception->getRequest();
    }


});