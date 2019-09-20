<div class="form-frame">
    <form id="creating-form" autocomplete="off" method="POST" action="">
        <?= val($data, 'fields') ?>
        <div class="cf-action">
            <input type="hidden" name="csrf-token" value="<?= val($data, 'csrf') ?>">
            <input type="hidden" name="dublicate" value="<?= val($data, 'unique') ?>">
            <button type="submit" class="cf-submit">Создать пользователя</button>
            <button type="reset" class="cf-reset">отменить</button>
        </div>
    </form>
</div>