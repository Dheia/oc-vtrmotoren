<?php

namespace SchultenMedia\FormBuilder\Actions;

class DuplicateForm
{
    protected $form;

    public function __construct($form)
    {
        $this->form = $form;
    }

    public function handle()
    {
        $this->form->load('fields');

        $copy = $this->form->replicate();

        $copy->name .= ' '.e(trans('schultenmedia.formbuilder::lang.help.copy'));

        $copy->forceSave();

        foreach ($copy->fields as $field) {
            $fieldCopy = $field->replicate();

            if ($fieldCopy->push()) {
                $copy->fields()->save($fieldCopy);
            }
        }

        return $copy;
    }
}
