<?php namespace SchultenMedia\Translate\Models;

use System\Models\File as FileBase;

/**
 * MLFile makes file attachments translatable
 *
 * @package schultenmedia\translate
 * @author Alexey Bobkov, Samuel Georges
 */
class MLFile extends FileBase
{
    /**
     * @var array implement behaviors
     */
    public $implement = [
        \SchultenMedia\Translate\Behaviors\TranslatableModel::class
    ];

    /**
     * @var array translatable attributes
     */
    public $translatable = ['title', 'description'];
}
