<?php

namespace SchultenMedia\FormBuilder\Updates;

use Illuminate\Support\Facades\File;
use October\Rain\Database\Updates\Seeder;
use SchultenMedia\FormBuilder\Models\FieldType;

class SeedFieldTypesTable extends Seeder
{
    public function run()
    {
        $path = __DIR__.'/fields/';

        FieldType::create([
            'name' => 'Text',
            'code' => 'text',
            'description' => 'Renders a single line text box.',
            'markup' => File::get($path.'_text.htm'),
        ]);

        FieldType::create([
            'name' => 'Textarea',
            'code' => 'textarea',
            'description' => 'Renders a multiline text box.',
            'markup' => File::get($path.'_textarea.htm'),
        ]);

        FieldType::create([
            'name' => 'Dropdown',
            'code' => 'dropdown',
            'description' => 'Renders a dropdown with specified options.',
            'markup' => File::get($path.'_dropdown.htm'),
        ]);

        FieldType::create([
            'name' => 'Checkbox',
            'code' => 'checkbox',
            'description' => 'Renders a single checkbox.',
            'markup' => File::get($path.'_checkbox.htm'),
        ]);

        FieldType::create([
            'name' => 'Checkbox List',
            'code' => 'checkbox_list',
            'description' => 'Renders a list of checkboxes.',
            'markup' => File::get($path.'_checkbox_list.htm'),
        ]);

        FieldType::create([
            'name' => 'Radio List',
            'code' => 'radio_list',
            'description' => 'Renders a list of radio options, where only one item can be selected at a time.',
            'markup' => File::get($path.'_radio_list.htm'),
        ]);

        FieldType::create([
            'name' => 'ReCaptcha',
            'code' => 'recaptcha',
            'description' => 'Renders a reCaptcha box.',
            'markup' => File::get($path.'_recaptcha.htm'),
        ]);

        FieldType::create([
            'name' => 'Submit',
            'code' => 'submit',
            'description' => 'Renders a submit button.',
            'markup' => File::get($path.'_submit.htm'),
        ]);

        FieldType::create([
            'name' => 'Country select',
            'code' => 'country_select',
            'description' => 'Renders a dropdown with country options.',
            'markup' => File::get($path.'_country_select.htm'),
        ]);

        FieldType::create([
            'name' => 'State select',
            'code' => 'state_select',
            'description' => 'Renders a dropdown with state options.',
            'markup' => File::get($path.'_state_select.htm'),
        ]);

        FieldType::create([
            'name' => 'File uploader',
            'code' => 'file_uploader',
            'description' => 'Renders a file uploader for regular files.',
        ]);

        FieldType::create([
            'name' => 'Image uploader',
            'code' => 'image_uploader',
            'description' => 'Renders a image uploader for image files.',
        ]);

        FieldType::create([
            'name' => 'Section',
            'code' => 'section',
            'description' => 'Renders a section heading and subheading.',
            'markup' => File::get($path.'_section.htm'),
        ]);
    }
}
