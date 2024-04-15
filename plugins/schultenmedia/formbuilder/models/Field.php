<?php

namespace SchultenMedia\FormBuilder\Models;

use October\Rain\Database\Model;
use October\Rain\Database\Traits\Sortable;
use October\Rain\Database\Traits\Validation;
use October\Rain\Support\Facades\Twig;
use SchultenMedia\FormBuilder\Classes\FieldValue;
use SchultenMedia\FormBuilder\Traits\TranslatableRelation;
use System\Classes\PluginManager;

class Field extends Model
{
    use Validation;
    use Sortable;
    use TranslatableRelation;

    public $implement = ['@SchultenMedia.Translate.Behaviors.TranslatableModel'];

    public $table = 'sm_formbuilder_fields';

    public $rules = [
        'field_type' => 'required',
        'name' => 'required',
    ];

    public $translatable = ['label', 'default', 'placeholder', 'comment', 'options', 'validation_messages'];

    protected $jsonable = ['options', 'validation_messages'];

    public $belongsTo = [
        'form' => Form::class,
        'field_type' => [
            FieldType::class,
            'order' => 'name asc',
        ],
    ];

    public function afterSave()
    {
        $this->setTranslatableAttributes();
    }

    public function scopeIsVisible($query)
    {
        return $query->where('is_visible', true);
    }

    public function getHtmlAttribute()
    {
        return Twig::parse($this->field_type->markup, $this->prepareFieldOptions());
    }

    public function getValueAttribute()
    {
        return (new FieldValue)->get($this);
    }

    public function getFormValueAttribute()
    {
        return post($this->name);
    }

    public function getListOptionsAttribute()
    {
        return collect($this->options)->lists('o_label', 'o_key');
    }

    public function getIsViewableAttribute()
    {
        return ! in_array($this->field_type->code, [
            'recaptcha',
            'submit',
            'file_uploader',
            'image_uploader',
            'section',
        ]);
    }

    protected function prepareFieldOptions()
    {
        return [
            'field' => $this,
            'field_id' => $this->field_id,
            'label' => $this->label,
            'label_class' => $this->label_class,
            'name' => $this->name,
            'default' => $this->default,
            'comment' => $this->comment,
            'class' => $this->class,
            'wrapper_class' => $this->wrapper_class,
            'placeholder' => $this->placeholder,
            'options' => $this->list_options,
            'custom_attributes' => $this->custom_attributes,
            'settings' => Settings::instance(),
            'location_plugin_enabled' => PluginManager::instance()->exists('SchultenMedia.Location'),
        ];
    }
}
