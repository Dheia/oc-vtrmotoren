<?php

namespace SchultenMedia\FormBuilder\Traits;

use Illuminate\Support\Facades\DB;

trait TranslatableRelation
{
    /**
     * This is a temporary fix until
     * https://github.com/SchultenMedia/translate-plugin/issues/209
     * is resolved. The problem persists in OctoberCMS 2.x.
     */
    protected function setTranslatableAttributes()
    {
        if (! post('RLTranslate')) {
            return;
        }

        foreach (post("RLTranslate") as $key => $value) {
            $data = collect($value)->intersectByKeys(array_flip($this->translatable));

            $obj = DB::table("SchultenMedia_translate_attributes")
                ->where("locale", $key)
                ->where("model_id", $this->id)
                ->where("model_type", get_class($this));

            if ($obj->count() > 0) {
                $obj->update(["attribute_data" => $data->toJson()]);
            } else {
                DB::table('SchultenMedia_translate_attributes')
                    ->insert([
                            'locale' => $key,
                            'model_id' => $this->id,
                            'model_type' => get_class($this),
                            'attribute_data' => $data->toJson(),
                        ]
                    );
            }
        }
    }
}
