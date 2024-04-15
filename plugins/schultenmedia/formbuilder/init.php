<?php

use SchultenMedia\FormBuilder\Listeners\LogEmailHtml;
use SchultenMedia\FormBuilder\Listeners\SendEmailMessage;

Event::listen('formBuilder.formSubmitted', SendEmailMessage::class);
Event::listen('mailer.send', LogEmailHtml::class);
