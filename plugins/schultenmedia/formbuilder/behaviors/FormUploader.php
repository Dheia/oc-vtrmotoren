<?php

namespace SchultenMedia\FormBuilder\Behaviors;

use October\Rain\Extension\ExtensionBase;
use SchultenMedia\FormBuilder\Models\FormLog;
use Responsiv\Uploader\Components\FileUploader;
use Responsiv\Uploader\Components\ImageUploader;
use System\Classes\PluginManager;
use System\Models\File;

class FormUploader extends ExtensionBase
{
    protected $component;

    public function __construct($component)
    {
        $this->component = $component;
    }

    public function init()
    {
        if (! PluginManager::instance()->exists('Responsiv.Uploader')) {
            return false;
        }

        foreach ($this->component->form->uploadFields() as $field) {
            $this->bindUploadComponent($field);
        }

        return true;
    }

    protected function bindUploadComponent($field)
    {
        $component = $this->addComponent($field);

        $this->makeRelation($field);

        $component->bindModel($field->name, new FormLog);
    }

    protected function addComponent($field)
    {
        return $this->component->addComponent(
            $this->type($field),
            $field->name,
            $this->options($field)
        );
    }

    protected function makeRelation($field)
    {
        FormLog::extend(function ($model) use ($field) {
            $model->attachMany[$field->name] = [
                File::class,
                'public' => false,
            ];
        });
    }

    protected function type($field)
    {
        return $field->field_type->code == 'file_uploader'
            ? FileUploader::class
            : ImageUploader::class;
    }

    protected function options($field)
    {
        // todo make this customizable
        $options = [
            'placeholderText' => $field->placeholder ?: e(trans('schultenmedia.formbuilder::lang.uploader.file_hint')),
            'maxSize' => $field->max_size ?: 5,
            'fileTypes' => $field->file_types ?: '*',
            'deferredBinding' => true,
        ];

        if ($this->type($field) === ImageUploader::class) {
            $options = array_merge($options, [
                'placeholderText' => $field->placeholder ?: e(trans('schultenmedia.formbuilder::lang.uploader.image_hint')),
                'fileTypes' => $field->file_types ?: '.gif,.jpg,.jpeg,.png',
                'imageWidth' => $field->image_width ?: 100,
                'imageHeight' => $field->image_heigh ?: 100,
                'imageMode' => $field->image_mode ?: 'crop',
            ]);
        }

        return $options;
    }
}
