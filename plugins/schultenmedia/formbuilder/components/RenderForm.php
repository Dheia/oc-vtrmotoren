<?php

namespace SchultenMedia\FormBuilder\Components;

use Cms\Classes\ComponentBase;
use October\Rain\Exception\ApplicationException;
use October\Rain\Exception\ValidationException;
use SchultenMedia\FormBuilder\Behaviors\FormUploader;
use SchultenMedia\FormBuilder\Classes\FormValidator;
use SchultenMedia\FormBuilder\Models\Form;
use SchultenMedia\FormBuilder\Traits\SupportLocationFields;
use Throwable;

class RenderForm extends ComponentBase
{
    use SupportLocationFields;

    public $form;

    public $message;

    public $implement = [FormUploader::class];

    public function componentDetails()
    {
        return [
            'name' => 'schultenmedia.formbuilder::lang.render_form.name',
            'description' => 'schultenmedia.formbuilder::lang.render_form.description',
        ];
    }

    public function defineProperties()
    {
        return [
            'formCode' => [
                'title' => 'schultenmedia.formbuilder::lang.form.title',
                'description' => 'schultenmedia.formbuilder::lang.form.description',
                'type' => 'dropdown',
                'placeholder' => e(trans('schultenmedia.formbuilder::lang.form.placeholder')),
                'validation' => ['required' => true],
            ],
        ];
    }

    public function init()
    {
        try {
            $this->form = $this->getForm();

            $this->page['uploader_plugin_enabled'] = $this->asExtension('FormUploader')->init();
        } catch (Throwable $throwable) {
            $this->page['formCode'] = $this->property('formCode');
        }
    }

    public function onRun()
    {
        $this->page['form'] = $this->form;

        $this->addJs('assets/js/form.js?v=1');
    }

    public function onSubmit()
    {
        $validator = (new FormValidator($this->form))->make();

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

//        try {
            event('formBuilder.formSubmitted', [$this->form]);
//        } catch (Throwable $throwable) {
//            throw new ApplicationException($this->form->error_message);
//        }

        return $this->response();
    }

    public function getFormCodeOptions()
    {
        return Form::lists('name', 'code');
    }

    protected function getForm()
    {
        return Form::query()
            ->with([
                'fields' => function ($query) {
                    $query->isVisible()->with('field_type');
                },
            ])
            ->where('code', $this->property('formCode'))
            ->firstOrFail();
    }

    protected function response()
    {
        if ($this->form->redirect_to) {
            return redirect()->to($this->form->redirect_to);
        }

        return $this->message = $this->form->success_message;
    }
}
