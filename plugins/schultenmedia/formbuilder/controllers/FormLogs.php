<?php

namespace SchultenMedia\FormBuilder\Controllers;

use Backend\Behaviors\FormController;
use Backend\Behaviors\ImportExportController;
use Backend\Behaviors\ListController;
use Backend\Classes\Controller;
use Backend\Facades\BackendMenu;
use Illuminate\Database\Eloquent\Builder;

class FormLogs extends Controller
{
    public $requiredPermissions = ['schultenmedia.formbuilder.access_form_logs'];

    public $implement = [
        ListController::class,
        FormController::class,
        ImportExportController::class,
    ];

    public $listConfig = 'config_list.yaml';

    public $formConfig = 'config_form.yaml';

    public $importExportConfig = 'config_import_export.yaml';

    public function __construct()
    {
        parent::__construct();

        $this->addCss('/plugins/schultenmedia/formbuilder/assets/css/backend.css?v=1');

        BackendMenu::setContext('SchultenMedia.FormBuilder', 'formbuilder', 'formlogs');
    }

    public function formExtendQuery(Builder $query)
    {
        $query->with('form.fields.field_type');
    }

    public function formExtendModel($model)
    {
        if (! $model->form) {
            return;
        }

        $model->form->attachLogRelations($model);
    }

    public function previewEmail($id)
    {
        $log = $this->formFindModelObject($id);

        return response($log->content_html);
    }

    public function formExtendFields($form)
    {
        if (! $form->model->form) {
            return;
        }

        foreach ($form->model->form->uploadFields() as $field) {
            $form->addTabFields([
                $field->name => [
                    'label' => $field->label,
                    'tab' => 'schultenmedia.formbuilder::lang.tab.attachments',
                    'mode' => $field->field_type->code == 'file_uploader' ? 'file' : 'image',
                    'type' => 'fileupload',
                ],
            ]);
        }
    }
}
