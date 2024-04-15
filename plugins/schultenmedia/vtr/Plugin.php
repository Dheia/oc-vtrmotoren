<?php namespace SchultenMedia\VTR;

use Event;
use Backend;
use Cms\Classes\Theme;
use Cms\Classes\Controller as CmsController;
use SchultenMedia\VTR\Components\Bookable;
use SchultenMedia\VTR\Models\Settings;
use System\Classes\PluginBase;
use SchultenMedia\VTR\Components\Configurator;

/**
 * Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'VTR configurator',
            'description' => 'VTR Configurator',
            'author'      => 'SchultenMedia',
            'homepage'    => 'https://www.schultenmedia.nl'
        ];
    }

    public function boot()
    {
    }


    public function registerComponents()
    {
        return [
            Configurator::class => 'configurator',
            Bookable::class => 'bookable',
        ];
    }

    public function registerMailTemplates()
    {
        return [
            'schultenmedia.vtr::mail.configurator-admin',
            'schultenmedia.vtr::mail.configurator',
            'schultenmedia.vtr::mail.configurator-en',
            'schultenmedia.vtr::mail.booking-admin',
            'schultenmedia.vtr::mail.booking-en',
            'schultenmedia.vtr::mail.booking',
        ];
    }

    public function registerSettings()
    {
        return [
            'settings' => [
                'label'       => 'VTR configurator',
                'description' => 'VTR configurator instellingen',
                'icon'        => 'icon-plug',
                'category'    => 'Schulten Media',
                'class'       => 'SchultenMedia\VTR\Models\Settings',
                'order'       => 500,
                'permissions' => ['schultenmedia.vtr.settings'],
                'size' => 950
            ],
        ];
    }
}
