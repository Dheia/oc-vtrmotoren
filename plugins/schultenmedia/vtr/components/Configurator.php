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

class Configurator extends ComponentBase
{
    public $obOccasion;
    public $obConfigurator;

    public function componentDetails()
    {
        return [
            'name' => 'Configurator',
            'description' => 'Configurator met filter',
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
            'occasion' => [
                'title' => 'Occasion',
                'description' => 'The Occasion',
                'default' => 0,
                'type' => 'set',
            ]
        ];
    }


    public function onRun()
    {

        $this->addJs('assets/js/configurator.js');

        $occasion =  $this->property('occasion', 0);

        if(!empty($this->param('occasion'))) {
            $occasion = $this->param('occasion');
        }

        $this->obConfigurator = Cache::remember('configurator', 60, function()  {
            return VTRSettings::instance();
        });



        if($occasion == 0) {

        } else if(is_int($occasion)) {
            $this->obOccasion = EntryRecord::inSection('Content\Occasion')->find($occasion);
        } else {

            $this->obOccasion = EntryRecord::inSection('Content\Occasion')->where('slug', $occasion)->first();

        }


        if(!$this->obOccasion) {
            return ;
        }
        $merk = strtolower($this->obOccasion->merk);
        $properties = $this->obConfigurator->properties;

        $limitations = [];
        foreach($properties as $propertyKey => $property) {
            foreach($property['options'] as $optionKey => $option) {
                if(!empty($option['limitations'])) {
                    foreach($option['limitations'] as $limit) {
                        $items = explode('_', $limit);
                        $brand = $items[0];
                        $type = $items[1];
                        $limitations[str_slug($property['property'])][str_slug($option['option'])][$brand][$type] = 1;

                    }

                    if(!array_key_exists($merk,
                        $limitations[str_slug($property['property'])][str_slug($option['option'])])) {

                        unset($properties[$propertyKey]['options'][$optionKey]);

                    }
                }
            }
            if(empty($properties[$propertyKey]['options'])) {
                unset($properties[$propertyKey]);
            }
        }
        $this->page['limitations'] = $limitations;

        $this->obConfigurator->properties = $properties;
//        dd($limitations);

//        dd($this->obConfigurator->properties, $this->obOccasion);



    }

    public function onInit() {
    }

    public function onUpdate() {

        $data = input();

        $occasion =  $this->property('occasion', 0);

        if(!empty($data['item'])) {
            $this->obOccasion = EntryRecord::inSection('Content\Occasion')->find($data['item']);
        }
        if($occasion != 0) {
            $this->obOccasion = EntryRecord::inSection('Content\Occasion')->find($occasion);
        }
        $this->obConfigurator = Cache::remember('configurator', 60, function()  {
            return VTRSettings::instance();
        });

        list($selectedOptions, $price) = $this->calculate();

        return [
            '#summary' => $this->renderPartial('@summary', [
                'details' => $selectedOptions,
                'price' => $price,
            ])
        ];


    }

    public function onSubmit() {

        $data = input();

        $occasion =  $this->property('occasion', 0);

        $this->obOccasion = EntryRecord::inSection('Content\Occasion')->find($occasion);

        $this->obConfigurator = Cache::remember('configurator', 60, function()  {
            return VTRSettings::instance();
        });

        $messages = [
            'name.required'     => trans('Naam is verplicht'),
            'surname.required'  => trans('Achternaam is verplicht'),
            'mobile.required'   => trans('Telefoonnummer is verplicht'),
            'email.required'   => trans('Emailadres is verplicht'),
            'street.required'   => trans('Adres is verplicht'),
            'zipcode.required'  => trans('Postcode is verplicht'),
            'city.required'     => trans('Plaats is verplicht'),
            'country.required'     => trans('Land is verplicht'),
            'between'           => trans('Naam moet tussen de 2 en 255 karakters zijn'),
            'surname.between'   => trans('Achternaam moet tussen de 2 en 255 karakters zijn'),
            'mobile.between'   => trans('Telefoonnummer moet tussen de 2 en 255 karakters zijn'),
            'street.between'   => trans('Adres moet tussen de 2 en 255 karakters zijn'),
            'zipcode.between'  => trans('Postcode moet tussen de 2 en 255 karakters zijn'),
            'city.between'     => trans('Plaats moet tussen de 2 en 255 karakters zijn'),
            'country.between'     => trans('Land moet tussen de 2 en 255 karakters zijn'),
        ];
        // Validate
        $rules = [
            'name' => 'required|between:2,255',
            'surname' => 'required|between:2,255',
            'email' => 'required|email|between:2,255',
            'mobile' => 'required|between:2,255',
            'street' => 'required|between:2,255',
            'zipcode' => 'required|between:2,255',
            'city' => 'required|between:2,255',
            'country' => 'required|between:2,255',
        ];

        $validation = \Validator::make(post(), $rules, $messages);
        if ($validation->fails()) {
//            \Flash::error($validation);
            throw new \ValidationException($validation);
        }

        if(!empty($data['item'])) {
            $this->obOccasion = EntryRecord::inSection('Content\Occasion')->find($data['item']);
        }

        list($selectedOptions, $price) = $this->calculate();

        $data['options'] = $selectedOptions;
        $data['object'] = $this->obOccasion;

        $templateName = 'schultenmedia.vtr::mail.configurator';
        try {

            $translator = \SchultenMedia\Translate\Classes\Translator::instance();
            $locale = $translator->getLocale();
            $locale = explode('-', $locale);
            $locale = $locale[0];

            if (file_exists(__DIR__ . '/../views/mail/configurator-' . $locale . '.htm')) {
                $templateName = 'schultenmedia.vtr::mail.configurator-'.$locale;
            }

        } catch (\Exception $e) {
        }

        Mail::send($templateName, $data, function($message) use ($data) {
            $message->to($data['email'], $data['name']);
        });

        Mail::send('schultenmedia.vtr::mail.configurator-admin', $data, function($message) {
            $message->to($this->obConfigurator->admin_email);
        });

        return [
            '#configurator-wrapper' => $this->renderPartial('@steps/success', $data)
        ];

    }

    private function calculate() {

        $data = input();

        $selectedOptions = [];
        $price = 0;

        if($this->obOccasion) {
            $price += (float)$this->obOccasion->verkoopprijs;

            $properties = $this->obConfigurator['properties'];

            if(!empty($data['options'])) {
                foreach ($data['options'] as $property => $selectedOption) {

                    if(str_contains( $selectedOption, '_-_')) {

                        $search = explode('_-_', $selectedOption);
                        $searchChild = $search[1];
                        $search = $search[0];
                     } else {

                        $search = $selectedOption;
                        $searchChild = false;

                     }

                    foreach ($properties as $obProperty) {


                        if (str_slug($obProperty['property']) == $property) {

                            foreach ($obProperty['options'] as $obOption) {

                                if (str_slug($obOption['option']) == $search) {

                                    if($searchChild) {
                                        foreach ($obOption['options'] as $obChild) {

                                            if (str_slug($obChild['option']) == $searchChild) {

                                                $selectedOptions[] = [

                                                    'property' => $obProperty['property'],
                                                    'option' => $obOption['option'] . ' - ' . $obChild['option'],
                                                    'price' =>  (float)$obOption['price']+ (float)$obChild['price']

                                                ];
                                                $price += (float)$obOption['price'];
                                                $price += (float)$obChild['price'];
                                            }

                                        }

                                    } else {

                                        $selectedOptions[] = [

                                            'property' => $obProperty['property'],
                                            'option' => $obOption['option'],
                                            'price' => $obOption['price']

                                        ];
                                        $price += (float)$obOption['price'];

                                    }


                                }


                            }

                            break;
                        }

                    }

                }
            }
        }


        return [$selectedOptions, $price];
    }

}
