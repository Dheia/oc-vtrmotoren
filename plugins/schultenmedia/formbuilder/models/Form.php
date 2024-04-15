<?php

namespace SchultenMedia\FormBuilder\Models;

use Illuminate\Support\Facades\DB;
use October\Rain\Database\Model;
use October\Rain\Database\Traits\Sluggable;
use October\Rain\Database\Traits\Validation;
use SchultenMedia\FormBuilder\Traits\FormAttributes;
use System\Models\File;

class Form extends Model
{
    use Validation;
    use Sluggable;
    use FormAttributes;

    public $implement = ['@SchultenMedia.Translate.Behaviors.TranslatableModel'];

    public $table = 'sm_formbuilder_forms';

    public $rules = [
        'name' => ['required'],
        'from_email' => ['email'],
        'recipients.*.email' => ['required', 'email'],
        'cc_recipients.*.email' => ['required', 'email'],
        'bcc_recipients.*.email' => ['required', 'email'],
    ];

    public $attributeNames = [
        'recipients.*.email' => 'schultenmedia.formbuilder::lang.field.email',
        'cc_recipients.*.email' => 'schultenmedia.formbuilder::lang.field.email',
        'bcc_recipients.*.email' => 'schultenmedia.formbuilder::lang.field.email',
    ];

    public $translatable = [
        'name',
        'from_email',
        'from_name',
        'description',
        'recipients',
        'cc_recipients',
        'bcc_recipients',
        'success_message',
        'error_message',
    ];

    protected $slugs = ['code' => 'name'];

    protected $jsonable = ['recipients', 'cc_recipients', 'bcc_recipients'];

    public $with = ['fields.field_type'];

    public $hasMany = [
        'fields' => [
            Field::class,
            'order' => 'sort_order',
        ],
    ];

    public function afterDelete()
    {
        foreach ($this->fields as $field) {
            $field->delete();
        }
    }

    public function getFieldsOptions()
    {
        return $this->fields->lists('name', 'name');
    }

    public function getData()
    {
        return $this->fields
            ->filter(function ($field) {
                return $field->is_viewable;
            })
            ->map(function ($field) {
                return [
                    'name' => $field->name,
                    'label' => $field->label,
                    'placeholder' => $field->placeholder,
                    'value' => $field->value,
                ];
            });
    }

    public function getDataArray()
    {
        return $this->getData()
            ->mapWithKeys(function ($record) {
                return [$record['name'] => $record['value']];
            })
            ->all();
    }

    public function attachLogRelations($log)
    {
        foreach ($this->uploadFields() as $field) {
            $log->attachMany[$field->name] = [
                File::class,
                'public' => false,
            ];
        }
    }

    public function uploadFields()
    {
        return $this->fields->filter(function ($field) {
            return in_array($field->field_type->code, ['file_uploader', 'image_uploader']);
        });
    }

    public function getRawFieldsAttribute()
    {
        return $this->fields->reduce(function ($raw, $field) {
            if ($field->field_type->code == 'file_uploader') {
                return $raw."{% partial 'renderForm::file_uploader' field = renderForm.form.fields.where('id', $field->id).first() %}\n";
            }

            if ($field->field_type->code == 'image_uploader') {
                return $raw."{% partial 'renderForm::image_uploader' field = renderForm.form.fields.where('id', $field->id).first() %}\n";
            }

            return $raw.$field->html."\n";
        });
    }

    public function listMailTemplates()
    {
        return DB::table('system_mail_templates')
            ->orderBy('description')
            ->lists('description', 'code');
    }
}
