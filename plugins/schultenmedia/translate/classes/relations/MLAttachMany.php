<?php namespace SchultenMedia\Translate\Classes\Relations;

use SchultenMedia\Translate\Classes\Translator;
use October\Rain\Database\Relations\AttachMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * MLAttachMany
 *
 * @package october\database
 * @author Alexey Bobkov, Samuel Georges
 */
class MLAttachMany extends AttachMany
{
    /**
     * __construct tweaks the morph class for translatable attachments
     */
    public function __construct(Builder $query, Model $parent, $type, $id, $isPublic, $localKey, $relationName = null)
    {
        parent::__construct($query, $parent, $type, $id, $isPublic, $localKey, $relationName);

        $this->morphClass .= ':' . Translator::instance()->getLocale();
    }
}
