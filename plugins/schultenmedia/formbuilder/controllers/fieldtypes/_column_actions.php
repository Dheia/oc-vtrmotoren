<button data-tooltip-text="<?= e(trans('schultenmedia.formbuilder::lang.help.duplicate')) ?>"
        data-request="onDuplicate"
        data-request-data="id: '<?= $record->id ?>'"
        data-request-confirm="<?= e(trans('schultenmedia.formbuilder::lang.field_type.duplicate_confirm')) ?>"
        data-stripe-load-indicator
        class="btn btn-primary btn-sm"><i class="octo-icon-copy"></i></button>

<?php if (! $record->is_default && BackendAuth::userHasAccess('schultenmedia.formbuilder.access_field_types.delete')) : ?>
    <button data-tooltip-text="<?= e(trans('schultenmedia.formbuilder::lang.help.delete')) ?>"
            data-request="onDelete"
            data-request-data="checked: ['<?= $record->id ?>']"
            data-request-confirm="<?= e(trans('backend::lang.form.action_confirm')) ?>"
            data-stripe-load-indicator
            class="btn btn-danger btn-sm ms-1"><i class="octo-icon-delete"></i></button>
<?php endif ?>
