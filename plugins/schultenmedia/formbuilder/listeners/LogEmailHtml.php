<?php

namespace SchultenMedia\FormBuilder\Listeners;

use SchultenMedia\FormBuilder\Models\FormLog;

class LogEmailHtml
{
    public function handle($mailer, $view, $message)
    {
        $header = $message->getHeaders()->get('X-SM-LOG');

        $logId = $header ? (int) $header->getValue() : null;

        if (! $logId) {
            return;
        }

        if($message->getBody() instanceof \Symfony\Component\Mime\Part\Multipart\AlternativePart) {
            $body = $message->getBody();
            $parts = $body->getParts();
            $body = last($parts)->getBody();
        } else {
            $body = $message->getBody();
        }


        $result = FormLog::query()
            ->where('id', $logId)
            ->update([
                'content_html' => $body,
                'subject' => $message->getSubject(),
                'to' => $this->formatAddress($message->getTo()),
                'cc' => $this->formatAddress($message->getCc()),
                'bcc' => $this->formatAddress($message->getBcc()),
                'from' => $this->formatAddress($message->getFrom()),
                'ip_address' => request()->ip(),
            ]);
    }

    public function formatAddress($address)
    {
        return collect($address)
            ->map(function ($address) {
                $name = $address->getName();
                $email = $address->getAddress();
                if (! $name) {
                    return $email;
                }

                return $name.'<'.$email.'>';
            })
            ->implode(', ');
    }
}
