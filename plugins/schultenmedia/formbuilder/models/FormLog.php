<?php

namespace SchultenMedia\FormBuilder\Models;

use October\Rain\Database\Model;

class FormLog extends Model
{
    public $table = 'sm_formbuilder_form_logs';

    protected $jsonable = ['form_data'];

    public $belongsTo = [
        'form' => Form::class,
    ];

    public function afterDelete()
    {
        // todo delete attachments
//        $this->deleteFiles();
    }

    public function afterFetch()
    {
        if (request()->segment(3) != 'files') {
            return;
        }

        if (! $this->form) {
            return;
        }

        $this->load('form.fields.field_type');

        $this->form->attachLogRelations($this);
    }

    public function log($form)
    {
        $this->form_id = $form->id;
        $this->form_data = $form->getData();

        $this->save(null, post('_session_key'));
    }
}
