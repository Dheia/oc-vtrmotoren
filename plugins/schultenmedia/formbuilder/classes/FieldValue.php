<?php

namespace SchultenMedia\FormBuilder\Classes;

use SchultenMedia\Location\Models\Country;
use SchultenMedia\Location\Models\State;

class FieldValue
{
    public function get($field)
    {
        $value = post($field->name);

        if (in_array($field->field_type->code, ['dropdown', 'radio_list'])) {
            return $field->list_options[$value];
        }

        if (is_array($value)) {
            return $this->getArrayValue($value, $field);
        }

        if ($field->field_type->code == 'country_select') {
            return $this->getCountryName($value);
        }

        if ($field->field_type->code == 'state_select') {
            return $this->getStateName($value);
        }

        return $value;
    }

    protected function getCountryName($countryId)
    {
        if (! class_exists(Country::class)) {
            return null;
        }

        return optional(Country::find($countryId))->name;
    }

    protected function getStateName($stateId)
    {
        if (! class_exists(State::class)) {
            return null;
        }

        return optional(State::find($stateId))->name;
    }

    protected function getArrayValue($array, $field)
    {
        return collect($array)
            ->map(function ($value) use ($field) {
                return $field->list_options[$value];
            })
            ->implode(', ');
    }
}
