<?php

namespace SchultenMedia\FormBuilder\Listeners;

use Illuminate\Mail\Message;
use October\Rain\Support\Facades\Mail;
use SchultenMedia\FormBuilder\Models\FormLog;

class SendEmailMessage
{
    protected $form;

    protected $data;

    public function handle($form)
    {
        $this->form = $form;

        $this->data = $this->setData();

        $result = event('formBuilder.beforeSendMessage', [$form, $this->data], true);

        if ($result !== false) {
            $this->sendMessage();

            $this->sendAutoresponderMessage();
        }
    }

    protected function sendMessage()
    {
        if (! $this->form->template_code) {
            return;
        }

        Mail::send($this->form->template_code, $this->data, function (Message $message) {
            $message->from($this->form->from_email, $this->form->from_name);

            $this->setRecipients($message);

            $this->setReplyTo($message);

            $log = $this->setHeaders($message);

            $this->attachFiles($message, $log);

        });
    }

    protected function sendAutoresponderMessage()
    {
        $email = $this->data[$this->form->response_email] ?? null;
        $name = $this->data[$this->form->response_name] ?? null;

        if (! $this->form->response_template_code || ! $email) {
            return;
        }

        Mail::send($this->form->response_template_code, $this->data, function (Message $message) use ($email, $name) {
            $this->setHeaders($message);

            $message->to($email, $name);
        });
    }

    protected function setRecipients($message)
    {
        if (! empty($this->form->recipients)) {
            $message->to($this->parseRecipients($this->form->recipients));
        }

        if (! empty($this->form->cc_recipients)) {
            $message->cc($this->parseRecipients($this->form->cc_recipients));
        }

        if (! empty($this->form->bcc_recipients)) {
            $message->bcc($this->parseRecipients($this->form->bcc_recipients));
        }
    }

    protected function setReplyTo($message)
    {
        $email = $this->data[$this->form->reply_email] ?? null;
        $name = $this->data[$this->form->reply_name] ?? null;

        if ($email) {
            $message->replyTo($email, $name);
        }
    }

    protected function setHeaders($message)
    {
        $log = new FormLog;
        $log->log($this->form);

        $message->getHeaders()
            ->addTextHeader('X-SM-LOG', $log->id);

        return $log;
    }

    protected function attachFiles($message, $log)
    {
        return $this->form->uploadFields()->map(function ($field) use ($message, $log) {
            foreach ($log->{$field->name} as $file) {
                $message->attach($file->getLocalPath());
            }
        });
    }

    protected function setData()
    {
        $data = $this->form->getDataArray();

        $data['form'] = $this->form;
        $data['fields'] = $this->form->fields;

        return $data;
    }

    protected function parseRecipients($recipients)
    {
        return collect($recipients)
            ->mapWithKeys(function ($recipient) {
                return [$recipient['email'] => $recipient['recipient_name']];
            })
            ->all();
    }
}
