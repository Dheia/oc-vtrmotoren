<div data-control="toolbar">
    <?php if (BackendAuth::userHasAccess('schultenmedia.formbuilder.access_forms.create')) : ?>
        <a href="<?= Backend::url('schultenmedia/formbuilder/forms/create') ?>"
           class="btn btn-primary oc-icon-plus">
            <?= e(trans('schultenmedia.formbuilder::lang.form.new')) ?>
        </a>
    <?php endif ?>

    <?php if (BackendAuth::userHasAccess('schultenmedia.formbuilder.access_forms.import_export')) : ?>
        <div class="btn-group">
            <a href="<?= Backend::url('schultenmedia/formbuilder/forms/export') ?>"
               class="btn btn-default oc-icon-download">
                <?= e(trans('schultenmedia.formbuilder::lang.field.export')) ?>
            </a>

            <a href="<?= Backend::url('schultenmedia/formbuilder/forms/import') ?>"
               class="btn btn-default oc-icon-upload">
                <?= e(trans('schultenmedia.formbuilder::lang.field.import')) ?>
            </a>
        </div>
    <?php endif ?>

    <?php if (BackendAuth::userHasAccess('schultenmedia.formbuilder.access_forms.delete')) : ?>
        <button class="btn btn-danger oc-icon-trash-o"
                disabled="disabled"
                data-request="onDelete"
                data-request-confirm="<?= e(trans('backend::lang.form.action_confirm')) ?>"
                data-list-checked-request
                data-list-checked-trigger
                data-stripe-load-indicator>
            <?= e(trans('backend::lang.list.delete_selected')) ?>
        </button>
    <?php endif ?>
</div>
