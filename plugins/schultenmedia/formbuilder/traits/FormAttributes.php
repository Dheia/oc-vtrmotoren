<?php

namespace SchultenMedia\FormBuilder\Traits;

use System\Models\MailSetting;

trait FormAttributes
{
    public function getFromEmailAttribute($value)
    {
        if (! $value) {
            return MailSetting::get('sender_email');
        }

        return $value;
    }

    public function getFromNameAttribute($value)
    {
        if (! $value) {
            return MailSetting::get('sender_name');
        }

        return $value;
    }

    public function getSuccessMessageAttribute($value)
    {
        if (! $value) {
            return e(trans('schultenmedia.formbuilder::lang.message.success'));
        }

        return $value;
    }

    public function getErrorMessageAttribute($value)
    {
        if (! $value) {
            return e(trans('schultenmedia.formbuilder::lang.message.error'));
        }

        return $value;
    }
}
