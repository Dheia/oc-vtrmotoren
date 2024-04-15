<?php

namespace SchultenMedia\FormBuilder\Controllers;

use Backend\Behaviors\ReorderController;
use Backend\Classes\Controller;
use Backend\Facades\BackendMenu;
use October\Rain\Database\Builder;

class Fields extends Controller
{
    public $requiredPermissions = ['schultenmedia.formbuilder.access_forms'];

    public $implement = [ReorderController::class];

    public $reorderConfig = 'config_reorder.yaml';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('SchultenMedia.FormBuilder', 'formbuilder', 'forms');
    }

    public function reorderExtendQuery(Builder $query)
    {
        return $query->where('form_id', array_get($this->params, 0));
    }
}
