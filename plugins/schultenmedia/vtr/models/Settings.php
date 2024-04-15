<?php namespace SchultenMedia\VTR\Models;

use Cms\Classes\Theme as CmsTheme;
use Lang;
use Model;
use System\Models\MailTemplate;

class Settings extends Model
{

    public const CATEGORY = 'VTR';

    public $implement = ['System.Behaviors.SettingsModel'];

    public $settingsCode = 'sm_vtr_settings';
    public $settingsFields = 'fields.yaml';


    public function getLimitationsOptions() {

        $options = [];

        $settings = self::instance();

        foreach($settings['configurator_brands'] as $brand) {
            foreach($brand['types'] as $type) {
                $options[$brand['slug'] . '_' . $type['slug']] = $type['title'];
            }
        }

        return $options;

    }

}