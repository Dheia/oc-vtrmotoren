<?php
namespace SchultenMedia\VTR\Components;

use Cms\Classes\ComponentBase;
use Mail;
use SchultenMedia\VTR\Models\Settings;
use SchultenMedia\VTR\Models\Settings as VTRSettings;
use Tailor\Models\GlobalRecord;
use Throwable;
use Session;

use Cache;
use Tailor\Models\EntryRecord;

class Bookable extends ComponentBase
{
    public $obEvent;

    public function componentDetails()
    {
        return [
            'name' => 'Bookable',
            'description' => 'Boekingsmodule van evenementen',
        ];
    }


    /**
     * Define properties.
     *
     * These will be available when you include the component.
     *
     * @return array The properties.
     */
    public function defineProperties()
    {
        return [
            'event' => [
                'title' => 'Evenement',
                'description' => 'The Evenement',
                'default' => 0,
                'type' => 'set',
            ]
        ];
    }


    public function onRun() {
    }

    public function onInit() {
    }

    public function onSubmit() {

        $data = input();

        $event = $this->property('event', 0);

        $obEvent = EntryRecord::inSection('Content\Event')->find($event);

        $messages = [
            'name.required'     => trans('Naam is verplicht'),
            'phone.required'   => trans('Telefoonnummer is verplicht'),
            'email.required'   => trans('Emailadres is verplicht'),
        ];

        // Validate
        $rules = [
            'name' => 'required|between:2,255',
            'email' => 'required|email|between:2,255',
            'phone' => 'required|between:2,255',
        ];
        if(!empty($obEvent->options)) {

            foreach($obEvent->options as $option) {
                $rules['options.' . str_slug($option->label) . ''] = 'required';
            }

        }

        $validation = \Validator::make(post(), $rules, $messages);
        if ($validation->fails()) {
//            \Flash::error($validation);
            throw new \ValidationException($validation);
        }

        $booking = (new \Tailor\Models\EntryRecord)::inSection('Shop\Booking');
//        $booking->addJsonable(['specifications']);
        $booking->title = $data['name'];
        $booking->email = $data['email'];
        $booking->phone = $data['phone'];
        $booking->description = $data['description'];
        $booking->paid = false;
        $booking->event = $obEvent;
        $booking->price = $obEvent->price;
        $booking->identifier = $obEvent->date;
        $booking->is_enabled = true;
        $booking->security_token = uniqid();
        $booking->save();

        // Create entity
//        $specifications = [];
        $options = [];
        if(!empty($data['options'])) {
            foreach($data['options'] as $key => $value) {
//                $specifications[] = [
//                    'label' => ucfirst($key),
//                    'value' => ucfirst($value),
//                ];
                $options[] = sprintf('%s: %s', ucfirst($key), ucfirst($value));
            }
        }

        $booking->specifications = implode(',', $options);
        $booking->save();

        // Go to mollie!

        $mollie = new \Mollie\Api\MollieApiClient();

        if (\SchultenMedia\VTR\Models\Settings::get("mollie_mode") == "live") {
            $apiKey = \SchultenMedia\VTR\Models\Settings::get("live_api_key");
        } else {
            $apiKey = \SchultenMedia\VTR\Models\Settings::get("test_api_key");
        }

        $mollie->setApiKey($apiKey);

        Session::put("sm.security_token", $booking->security_token);

        try {

            $webhookUrl = \Config::get('app.url') . "/mollie/webhook";
            $returnUrl = \Config::get('app.url') . "/bestelling-afgerond";

            $payment = [
                "amount" => [
                    "currency" => 'EUR',
                    "value" => number_format($booking->price, 2, ".", ""),
                ],
                "description" => "Boeking: " . $booking->id . " " . $booking->title . " (" . $booking->identifier->format('d-m-Y') . ")",
                "redirectUrl" => $returnUrl,
                "webhookUrl" => $webhookUrl,
                "metadata" => [
                    "order_number" => $booking->id,
                    "security_token" => $booking->security_token,
                ],
            ];

            $payment = $mollie->payments->create($payment);
        } catch (Throwable $e) {
            dd($e);
        }

        Session::put("sm.mollie_payment", $payment->id);
        return response()->redirectTo($payment->getCheckoutUrl());


        // Todo, make webhook!


        return [
            '#configurator-wrapper' => $this->renderPartial('@steps/success', $data)
        ];

    }


}
