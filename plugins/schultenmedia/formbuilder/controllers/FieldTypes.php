<?php

namespace SchultenMedia\FormBuilder\Controllers;

use Backend\Behaviors\FormController;
use Backend\Behaviors\ListController;
use Backend\Classes\Controller;
use Backend\Facades\BackendMenu;
use October\Rain\Support\Facades\Flash;

class FieldTypes extends Controller
{
    public $requiredPermissions = ['schultenmedia.formbuilder.access_field_types'];

    public $implement = [
        ListController::class,
        FormController::class,
    ];

    public $listConfig = 'config_list.yaml';

    public $formConfig = 'config_form.yaml';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('SchultenMedia.FormBuilder', 'formbuilder', 'fieldtypes');
    }

    public function onDuplicate()
    {
        $model = $this->formFindModelObject(post('id'));

        $copy = $model->replicate();

        $copy->name .= ' '.e(trans('schultenmedia.formbuilder::lang.help.copy'));

        $copy->forceSave();

        Flash::success(e(trans('schultenmedia.formbuilder::lang.field_type.duplicate_success')));

        return $this->listRefresh();
    }

    public function onRestore($id)
    {
        $model = $this->formFindModelObject($id);

        $model->restoreMarkupToDefault();

        Flash::success(e(trans('schultenmedia.formbuilder::lang.field_type.restore_success')));

        return redirect()->refresh();
    }
}
