<?php

namespace SchultenMedia\FormBuilder\Models;

use October\Rain\Database\Model;
use October\Rain\Database\Traits\Sluggable;
use October\Rain\Database\Traits\Validation;
use October\Rain\Support\Facades\File;

class FieldType extends Model
{
    use Validation;
    use Sluggable;

    public $implement = ['@SchultenMedia.Translate.Behaviors.TranslatableModel'];

    public $table = 'sm_formbuilder_field_types';

    public $rules = [
        'name' => ['required'],
        'code' => ['required', 'unique:sm_formbuilder_field_types'],
    ];

    protected $slugs = ['code' => 'name'];

    public $translatable = ['name', 'description'];

    public function restoreMarkupToDefault()
    {
        if (! ($markup = $this->getDefaultMarkup())) {
            return;
        }

        $this->markup = $markup;

        $this->forceSave();
    }

    public function isDefault()
    {
        return collect(File::allFiles(__DIR__."/../updates/fields/"))
                ->contains(function ($template) {
                    return $template->getFilename() == "_{$this->code}.htm";
                })
            || in_array($this->code, ['file_uploader', 'image_uploader']);
    }

    public function beforeDelete()
    {
        if ($this->isDefault()) {
            return false;
        }
    }

    protected function getDefaultMarkup()
    {
        $path = __DIR__."/../updates/fields/_{$this->code}.htm";

        if (! File::exists($path)) {
            return false;
        }

        return File::get($path);
    }
}
