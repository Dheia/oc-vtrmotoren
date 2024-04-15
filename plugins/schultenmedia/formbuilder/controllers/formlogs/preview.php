<?php Block::put('breadcrumb') ?>
<ul>
    <li>
        <a href="<?= Backend::url('schultenmedia/formbuilder/formlogs') ?>">
            <?= e(trans('schultenmedia.formbuilder::lang.logs.log')) ?>
        </a>
    </li>
    <li><?= $this->pageTitle ?></li>
</ul>
<?php Block::endPut() ?>

<?php if (! $this->fatalError): ?>
    <div class="form-preview">
        <?= $this->formRenderPreview() ?>
    </div>
<?php else: ?>
    <p class="flash-message static error"><?= $this->fatalError ?></p>
    <p>
        <a href="<?= Backend::url('schultenmedia/formbuilder/formlogs') ?>"
           class="btn btn-default"><?= e(trans('backend::lang.form.return_to_list')) ?>
        </a>
    </p>
<?php endif ?>
