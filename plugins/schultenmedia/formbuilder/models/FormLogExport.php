<?php

namespace SchultenMedia\FormBuilder\Models;

use Backend\Models\ExportModel;

class FormLogExport extends ExportModel
{
    public function exportData($columns, $sessionKey = null)
    {
        $logs = FormLog::with('form')->get();

        $export = [];

        foreach ($logs as $key => $log) {
            $export[$key] = [
                'id' => $log->id,
                'form_id' => $log->form_id,
                'form_name' => optional($log->form)->name,
                'form_data' => json_encode($log->form_data),
                'content_html' => $log->content_html,
                'subject' => $log->subject,
                'from' => $log->from,
                'to' => $log->to,
                'cc' => $log->cc,
                'bcc' => $log->bcc,
                'ip_address' => $log->ip_address,
                'created_at' => $log->created_at,
                'updated_at' => $log->updated_at,
            ];
        }

        return $export;
    }
}
