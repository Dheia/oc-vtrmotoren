<?php

namespace SchultenMedia\FormBuilder;

use Backend\Facades\Backend;
use SchultenMedia\FormBuilder\Components\RenderForm;
use SchultenMedia\FormBuilder\Console\Patch;
use SchultenMedia\FormBuilder\Models\Settings;
use SchultenMedia\FormBuilder\Providers\ReCaptchaServiceProvider;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function pluginDetails()
    {
        return [
            'name' => 'schultenmedia.formbuilder::lang.plugin.name',
            'description' => 'schultenmedia.formbuilder::lang.plugin.description',
            'author' => 'SchultenMedia',
            'icon' => 'icon-code',
        ];
    }

    public function boot()
    {
        app()->register(ReCaptchaServiceProvider::class);
    }

    public function register()
    {
        $this->registerConsoleCommand('formbuilder:patch', Patch::class);
    }

    public function registerNavigation()
    {
        return [
            'formbuilder' => [
                'label' => 'schultenmedia.formbuilder::lang.navigation.formbuilder',
                'url' => Backend::url('schultenmedia/formbuilder/forms'),
                'icon' => 'icon-check-square-o',
                'permissions' => ['schultenmedia.formbuilder.*'],
                'order' => 500,
                'sideMenu' => [
                    'forms' => [
                        'label' => 'schultenmedia.formbuilder::lang.navigation.forms',
                        'icon' => 'icon-check-square-o',
                        'url' => Backend::url('schultenmedia/formbuilder/forms'),
                        'permissions' => ['schultenmedia.formbuilder.access_forms'],
                    ],
                    'fieldtypes' => [
                        'label' => 'schultenmedia.formbuilder::lang.navigation.fieldtypes',
                        'icon' => 'icon-code',
                        'url' => Backend::url('schultenmedia/formbuilder/fieldtypes'),
                        'permissions' => ['schultenmedia.formbuilder.access_field_types'],
                    ],
                    'formlogs' => [
                        'label' => 'schultenmedia.formbuilder::lang.navigation.formlogs',
                        'icon' => 'icon-archive',
                        'url' => Backend::url('schultenmedia/formbuilder/formlogs'),
                        'permissions' => ['schultenmedia.formbuilder.access_form_logs'],
                    ],
                ],
            ],
        ];
    }

    public function registerPermissions()
    {
        return [
            'schultenmedia.formbuilder.access_settings' => [
                'label' => 'schultenmedia.formbuilder::lang.permissions.access_settings',
                'tab' => 'schultenmedia.formbuilder::lang.permissions.tab',
            ],
            'schultenmedia.formbuilder.access_forms' => [
                'label' => 'schultenmedia.formbuilder::lang.permissions.access_forms',
                'tab' => 'schultenmedia.formbuilder::lang.permissions.tab',
            ],
            'schultenmedia.formbuilder.access_form_logs' => [
                'label' => 'schultenmedia.formbuilder::lang.permissions.access_form_logs',
                'tab' => 'schultenmedia.formbuilder::lang.permissions.tab',
            ],
            'schultenmedia.formbuilder.access_field_types' => [
                'label' => 'schultenmedia.formbuilder::lang.permissions.access_field_types',
                'tab' => 'schultenmedia.formbuilder::lang.permissions.tab',
            ],
        ];
    }

    public function registerSettings()
    {
        return [
            'settings' => [
                'label' => 'schultenmedia.formbuilder::lang.settings.label',
                'description' => 'schultenmedia.formbuilder::lang.settings.description',
                'category' => 'schultenmedia.formbuilder::lang.settings.category',
                'icon' => 'icon-google',
                'class' => Settings::class,
                'order' => 500,
                'keywords' => 'form builder contact',
                'permissions' => ['schultenmedia.formbuilder.access_settings'],
            ],
        ];
    }

    public function registerComponents()
    {
        return [
            RenderForm::class => 'renderForm',
        ];
    }

    public function registerPageSnippets()
    {
        return [
            RenderForm::class => 'renderForm',
        ];
    }

    public function registerMailTemplates()
    {
        return [
            'schultenmedia.formbuilder::mail.contact',
            'schultenmedia.formbuilder::mail.default',
        ];
    }
}
