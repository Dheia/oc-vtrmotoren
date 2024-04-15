<?php

namespace SchultenMedia\FormBuilder\Updates;

use Illuminate\Support\Facades\Artisan;
use October\Rain\Database\Updates\Seeder;

class RunPatch22 extends Seeder
{
    public function run()
    {
//        Artisan::call('formbuilder:patch 2.0');
        Artisan::call('formbuilder:patch 2.2');
    }
}
