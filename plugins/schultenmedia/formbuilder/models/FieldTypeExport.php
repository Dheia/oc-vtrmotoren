<?php

namespace SchultenMedia\FormBuilder\Models;

use Backend\Models\ExportModel;

class FieldTypeExport extends ExportModel
{
    public $table = 'sm_formbuilder_field_types';

    public function exportData($columns, $sessionKey = null)
    {
        return static::get()->toArray();
    }
}
