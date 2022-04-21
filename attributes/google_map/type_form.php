<?php
defined('C5_EXECUTE') or die('Access Denied.');

$googleMapApiKey = $googleMapApiKey ?? '';
?>
<fieldset>
    <legend><?= t('Google Map Options') ?></legend>

    <div class="form-group">
        <?= $form->label('googleMapApiKey', t('API Key')) ?>
        <?= $form->text('googleMapApiKey', $googleMapApiKey) ?>
    </div>

</fieldset>