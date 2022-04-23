<?php
defined('C5_EXECUTE') or die('Access Denied.');

$googleMapApiKey = $googleMapApiKey ?? null;
/** @var \Concrete\Core\Form\Service\Form $form */
?>
<fieldset>
    <legend><?= t('Google Map Options') ?></legend>
    <div class="form-group">
        <?= $form->label('googleMapApiKey', t('API Key')) ?>
        <?= $form->text('googleMapApiKey', $googleMapApiKey) ?>
        <p class="form-text"><?= t("Please input an API Key and enables Places API and Maps Static API for it. This API Key will be shared across the entire system. Therefore, you don't need to set different keys for each Google Map attribute. Also, this key will be shared with Google Map blocks.") ?></p>
        <p class="form-text"><a href="https://console.cloud.google.com/apis/dashboard" target="_blank"><?= t('Google Cloud Platform API Dashboard') ?> <i class="fas fa-external-link-alt"></i></a></p>
    </div>
</fieldset>