<?php

namespace SchultenMedia\FormBuilder\Controllers;

use Backend\Behaviors\FormController;
use Backend\Behaviors\ListController;
use Backend\Behaviors\RelationController;
use Backend\Classes\Controller;
use Backend\Facades\BackendMenu;
use October\Rain\Support\Facades\Flash;
use SchultenMedia\FormBuilder\Actions\DuplicateForm;
use Backend\Widgets\Form;

class Forms extends Controller
{
    public $requiredPermissions = ['schultenmedia.formbuilder.access_forms'];

    public $implement = [
        ListController::class,
        FormController::class,
        RelationController::class,
    ];

    public $listConfig = 'config_list.yaml';

    public $formConfig = 'config_form.yaml';

    public $relationConfig = 'config_relation.yaml';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('SchultenMedia.FormBuilder', 'formbuilder', 'forms');
    }

    public function onLoadRawFieldsPopup($formId)
    {
        $config = $this->makeConfig('$/schultenmedia/formbuilder/models/form/raw_fields.yaml');

        $config->model = $this->formFindModelObject($formId);

        $widget = $this->makeWidget(Form::class, $config);

        $widget->bindToController();

        $this->vars['widget'] = $widget;

        return $this->makePartial('raw_fields_popup');
    }

    public function onDuplicate()
    {
        $form = $this->formFindModelObject(post('id'));

        (new DuplicateForm($form))->handle();

        Flash::success(e(trans('schultenmedia.formbuilder::lang.form.duplicate_success')));

        return $this->listRefresh();
    }
}
