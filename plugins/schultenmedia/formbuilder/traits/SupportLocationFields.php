<?php

namespace SchultenMedia\FormBuilder\Traits;

use SchultenMedia\Location\Models\State;

trait SupportLocationFields
{
    public function onChangeCountry()
    {
        return [
            '.state-select' => $this->renderPartial(
                '@state_select_options', ['options' => $this->stateOptions()]
            ),
        ];
    }

    protected function stateOptions()
    {
        $countryId = request(request('name'));

        return ['' => request('placeholder')] + State::getNameList($countryId);
    }
}
