<?php

namespace SchultenMedia\FormBuilder\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use October\Rain\Database\Schema\Blueprint;
use SchultenMedia\FormBuilder\Models\FieldType;
use SchultenMedia\FormBuilder\Models\Form;
use Schema;
use System\Models\MailTemplate;

class Patch extends Command
{
    protected static $defaultName = 'formbuilder:patch';

    protected $signature = 'formbuilder:patch {patch}';

    protected $description = 'Apply specified patch.';

    public function handle()
    {
        $patch = str_replace('.', '', $this->argument('patch'));

        $this->{'patch'.$patch}();
    }

    /*
     * php artisan formbuilder:patch 2.0
     */
    protected function patch20()
    {
        /*if (! $this->confirm('This will patch your database for FormBuilder plugin 2.0. Please make sure you have a backup before proceeding. Proceed?')) {
            return;
        }*/

        /*
         * Add fields to form logs
         */
        Schema::table('sm_formbuilder_form_logs', function (Blueprint $table) {
            $table->string('subject')->nullable();
            $table->string('from')->nullable();
            $table->string('to')->nullable();
            $table->string('cc')->nullable();
            $table->string('bcc')->nullable();
            $table->string('ip_address')->nullable();
        });

        /*
         * Add fields to forms
         */
        Schema::table('sm_formbuilder_forms', function (Blueprint $table) {
            $table->text('recipients')->nullable();
            $table->text('cc_recipients')->nullable();
            $table->text('bcc_recipients')->nullable();
            $table->string('response_email')->nullable();
            $table->string('response_name')->nullable();
            $table->unsignedInteger('response_template_id')->index()->nullable();
            $table->string('redirect_to')->nullable();
        });

        /*
         * Add fields to form fields
         */
        Schema::table('sm_formbuilder_fields', function (Blueprint $table) {
            $table->string('label_class')->nullable();
            $table->unsignedInteger('sort_order')->index()->nullable();
        });

        /*
         * Reset sort order for fields
         */
        DB::table('sm_formbuilder_fields')
            ->orderBy('id')
            ->chunk(100, function ($fields) {
                foreach ($fields as $field) {
                    DB::table('sm_formbuilder_fields')
                        ->where('id', $field->id)
                        ->update([
                            'sort_order' => $field->id,
                        ]);
                }
            });

        /*
         * Migrate recipients form fields
         */
        DB::table('sm_formbuilder_forms')
            ->orderBy('id')
            ->chunk(100, function ($forms) {
                foreach ($forms as $form) {
                    DB::table('sm_formbuilder_forms')
                        ->where('id', $form->id)
                        ->update([
                            'recipients' => json_encode([
                                [
                                    'email' => $form->to_email,
                                    'recipient_name' => $form->to_name,
                                ],
                            ]),
                            'bcc_recipients' => json_encode([
                                [
                                    'email' => $form->bcc_email,
                                    'recipient_name' => $form->bcc_name,
                                ],
                            ]),
                        ]);
                }
            });

        /*
         * Reset field types markup to default
         */
        $path = __DIR__.'/../updates/fields/';
        $types = [
            'checkbox', 'checkbox_list', 'country_select', 'dropdown', 'radio_list', 'recaptcha', 'section',
            'state_select', 'submit', 'text', 'textarea',
        ];

        foreach ($types as $type) {
            FieldType::query()
                ->where('code', $type)
                ->update([
                    'markup' => File::get("{$path}_{$type}.htm"),
                ]);
        }

        /*
         * Create image uploader field
         */
        FieldType::unguard();

        FieldType::create([
            'name' => 'Image uploader',
            'code' => 'image_uploader',
            'description' => 'Renders a image uploader for image files.',
        ]);

        $this->info('FormBuilder plugin patch for version 2.0 applied!');
    }

    /*
     * php artisan formbuilder:patch 2.2
     */
    protected function patch22()
    {
        if (! Schema::hasColumn('sm_formbuilder_forms', 'template_code')) {
            Schema::table('sm_formbuilder_forms', function (Blueprint $table) {
                $table->string('template_code')->nullable();
                $table->string('response_template_code')->nullable();
            });
        }

        foreach (Form::all() as $form) {
            if ($form->template_id && ($template = MailTemplate::find($form->template_id))) {
                DB::table($form->table)
                    ->where('id', $form->id)
                    ->update([
                        'template_code' => $template->code,
                    ]);
            }

            if ($form->response_template_id && ($template = MailTemplate::find($form->response_template_id))) {
                DB::table($form->table)
                    ->where('id', $form->id)
                    ->update([
                        'response_template_code' => $template->code,
                    ]);
            }
        }
    }
}
