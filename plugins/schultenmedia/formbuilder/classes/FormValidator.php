<?php

namespace SchultenMedia\FormBuilder\Classes;

use Illuminate\Support\Facades\Validator;

class FormValidator
{
    protected $form;

    public function __construct($form)
    {
        $this->form = $form;
    }

    public function make()
    {
        return Validator::make(request()->all(), $this->rules(), $this->messages())
            ->setAttributeNames($this->names());
    }

    protected function rules()
    {
        return $this->form->fields
            ->filter(function ($field) {
                return $field->validation;
            })
            ->lists('validation', 'name');
    }

    protected function messages()
    {
        return $this->form->fields
            ->filter(function ($field) {
                return ! ! $field->validation_messages;
            })
            ->map(function ($field) {
                return $this->mapFieldToValidationMessages($field);
            })
            ->collapse()
            ->all();
    }

    protected function names()
    {
        return $this->form->fields
            ->mapWithKeys(function ($field) {
                return [$field->name => $field->label ?: ($field->placeholder ?: $field->name)];
            })
            ->all();
    }

    protected function mapFieldToValidationMessages($field)
    {
        return collect($field->validation_messages)
            ->mapWithKeys(function ($message) use ($field) {
                return [$field->name.'.'.$message['rule'] => e(trans($message['message']))];
            });
    }
}
