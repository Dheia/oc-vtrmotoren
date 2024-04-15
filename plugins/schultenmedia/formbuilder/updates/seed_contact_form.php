<?php

namespace SchultenMedia\FormBuilder\Updates;

use October\Rain\Database\Updates\Seeder;
use SchultenMedia\FormBuilder\Models\Form;

class SeedContactForm extends Seeder
{
    public function run()
    {
        $form = $this->createForm();

        foreach ($this->fields() as $field) {
            $form->fields()->create([
                'field_type_id' => $field['type'],
                'label' => $field['label'],
                'name' => $field['name'],
                'validation' => $field['validation'] ?? null,
            ]);
        }
    }

    protected function createForm()
    {
        return Form::create([
            'template_code' => 'schultenmedia.formbuilder::mail.contact',
            'name' => 'Contact Form',
            'description' => 'Renders a contact form.',
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
                'label' => 'Name',
                'name' => 'name',
                'validation' => 'required',
            ],
            [
                'type' => 1,
                'label' => 'Subject',
                'name' => 'subject',
                'validation' => 'required',
            ],
            [
                'type' => 1,
                'label' => 'E-mail',
                'name' => 'email',
                'validation' => 'required|email',
            ],
            [
                'type' => 1,
                'label' => 'Phone',
                'name' => 'phone',
            ],
            [
                'type' => 2,
                'label' => 'Message',
                'name' => 'content_message',
                'validation' => 'required',
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
