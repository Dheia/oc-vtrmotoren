<?php

namespace SchultenMedia\FormBuilder\Models;

use October\Rain\Database\Model;
use System\Behaviors\SettingsModel;

class Settings extends Model
{
    public $implement = [SettingsModel::class];

    public $settingsCode = 'sm_formbuilder_settings';

    public $settingsFields = 'fields.yaml';

    public function initSettingsData()
    {
        $this->site_key = '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI';
        $this->secret_key = '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe';
    }
}
