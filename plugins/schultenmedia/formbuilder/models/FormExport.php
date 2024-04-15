<?php

namespace SchultenMedia\FormBuilder\Models;

use Backend\Models\ExportModel;

class FormExport extends ExportModel
{
    public $table = 'sm_formbuilder_forms';

    public $hasMany = [
        'form_fields' => [
            Field::class,
            'order' => 'sort_order',
            'key' => 'form_id',
        ],
    ];

    protected $appends = [
        'fields',
    ];

    public function exportData($columns, $sessionKey = null)
    {
        return static::with('form_fields')->get()->toArray();
    }

    public function getFieldsAttribute()
    {
        return post('file_format') === 'json'
            ? $this->form_fields
            : json_encode($this->form_fields);
    }
}
