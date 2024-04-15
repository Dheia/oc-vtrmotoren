<?php

namespace SchultenMedia\FormBuilder\Updates;

use October\Rain\Database\Updates\Seeder;
use SchultenMedia\FormBuilder\Models\Form;

class SeedDefaultForm extends Seeder
{
    public function run()
    {
        $form = $this->createForm();

        foreach ($this->fields() as $field) {
            $form->fields()->create([
                'field_type_id' => $field['type'],
                'label' => $field['label'],
                'name' => $field['name'],
                'options' => $field['options'] ?? null,
                'placeholder' => $field['placeholder'] ?? null,
                'validation' => $field['validation'] ?? null,
            ]);
        }
    }

    protected function createForm()
    {
        return Form::create([
            'template_code' => 'schultenmedia.formbuilder::mail.default',
            'name' => 'Default Form',
            'description' => 'Renders a form with all available system fields.',
            'recipients' => [
                [
                    'email' => 'admin@domain.tld',
                    'recipient_name' => 'Admin Person',
                ],
            ],
        ]);
    }

    protected function fields()
    {
        return [
            [
                'type' => 1,
                'label' => 'Text',
                'name' => 'text',
                'validation' => 'required',
            ],
            [
                'type' => 3,
                'label' => 'Dropdown',
                'name' => 'dropdown',
                'options' => [
                    '1' => [
                        'o_key' => 'option_1',
                        'o_label' => 'Option 1',
                    ],
                    '2' => [
                        'o_key' => 'option_2',
                        'o_label' => 'Option 2',
                    ],
                ],
                'placeholder' => '-- choose --',
                'validation' => 'required',
            ],
            [
                'type' => 4,
                'label' => 'Checkbox',
                'name' => 'checkbox',
                'validation' => 'required',
            ],
            [
                'type' => 5,
                'label' => 'Checkbox list',
                'name' => 'checkbox_list',
                'options' => [
                    '1' => [
                        'o_key' => 'checkbox_option_1',
                        'o_label' => 'Option 1',
                    ],
                    '2' => [
                        'o_key' => 'checkbox_option_2',
                        'o_label' => 'Option 2',
                    ],
                ],
                'validation' => 'required',
            ],
            [
                'type' => 6,
                'label' => 'Radio list',
                'name' => 'radio_list',
                'options' => [
                    '1' => [
                        'o_key' => 'radio_option_1',
                        'o_label' => 'Option 1',
                    ],
                    '2' => [
                        'o_key' => 'radio_option_2',
                        'o_label' => 'Option 2',
                    ],
                ],
                'validation' => 'required',
            ],
            [
                'type' => 9,
                'label' => 'Country select',
                'name' => 'country',
                'validation' => 'required',
                'placeholder' => '-- choose --',
            ],
            [
                'type' => 10,
                'label' => 'State select',
                'name' => 'state',
                'validation' => 'required',
                'placeholder' => '-- choose --',
            ],
            [
                'type' => 2,
                'label' => 'Textarea',
                'name' => 'textarea',
                'validation' => 'required',
            ],
            [
                'type' => 13,
                'label' => 'Files Section',
                'name' => 'section',
            ],
            [
                'type' => 11,
                'label' => 'File uploader',
                'name' => 'files',
            ],
            [
                'type' => 12,
                'label' => 'Image uploader',
                'name' => 'images',
            ],
            [
                'type' => 7,
                'label' => 'reCaptcha',
                'name' => 'g-recaptcha-response',
                'validation' => 'required|recaptcha',
            ],
            [
                'type' => 8,
                'label' => 'Send',
                'name' => 'submit',
            ],
        ];
    }
}
