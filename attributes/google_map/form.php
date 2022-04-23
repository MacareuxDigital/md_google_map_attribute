<?php
defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Support\Facade\Application;

$googleMapApiKey = $googleMapApiKey ?? '';
$location = $location ?? null;
$latitude = $latitude ?? 0;
$longitude = $longitude ?? 0;
$zoom = $zoom ?? 14;
/** @var \Concrete\Core\Application\Application $app */
/* @var Concrete\Core\Form\Service\Form $form */
$app = Application::getFacadeApplication();
$config = $app->make('config');
?>
<style>
    #mdGoogleMapAttributeCanvas {
        height: 250px;
    }
    #mdGoogleMapAttributeCanvas > * , .gm-style , .gm-style > iframe {
        position: relative !important;
    }
</style>
<div class="row">
    <div class="col-md-6">
        <div class="form-group" id="ccm-attribute-google-map">
            <?= $form->label('location', t('Google Map')) ?>
            <?= $form->text($this->field('location'), $location, ['placeholder' => t('Enter a location')]) ?>
            <?= $form->hidden('apiKey', $config->get('app.api_keys.google.maps')) ?>
            <?= $form->hidden($this->field('latitude'), $latitude) ?>
            <?= $form->hidden($this->field('longitude'), $longitude) ?>
        </div>
        <div class="form-group">
            <?= $form->label('zoom', t('Zoom')) ?>
            <?php
                $zoomLevels = range(0, 21);
                $zoomArray = array_combine($zoomLevels, $zoomLevels);
            ?>
            <?= $form->select($this->field('zoom'), $zoomArray, $zoom) ?>
        </div>
        <div class="form-group">
            <?= $form->label('marker', t('Marker')) ?>
            <?= $form->checkbox($this->field('marker'), 1, $marker ?? false)?>
        </div>
    </div>
    <div class="col-md-6">
        <div id="mdGoogleMapAttributeCanvas">
        </div>
    </div>
</div>
<script>
    function mdGoogleMapAttributeUpdateValue(latlng) {
        document.querySelector('input[name="<?= $this->field('latitude') ?>"]').value = latlng.lat();
        document.querySelector('input[name="<?= $this->field('longitude') ?>"]').value = latlng.lng();
    }
    function mdGoogleMapAttributeInitMap() {
        const latlng = new google.maps.LatLng(<?= h($latitude) ?>, <?= h($longitude) ?>);
        const map = new google.maps.Map(document.getElementById("mdGoogleMapAttributeCanvas"), {
            zoom: <?= h($zoom) ?>,
            center: latlng,
            disableDefaultUI: true
        });
        const marker = new google.maps.Marker({
            position: latlng,
            map: map,
            draggable: true,
            title: "<?= t('Drag to move center') ?>"
        });
        marker.addListener('dragend', function (e) {
            map.panTo(e.latLng);
            mdGoogleMapAttributeUpdateValue(e.latLng);
        });
        const locationInput = document.querySelector('input[name="<?=$this->field('location')?>"]');
        const autocomplete = new google.maps.places.Autocomplete(locationInput);
        autocomplete.addListener('place_changed', function (e) {
            const place = autocomplete.getPlace();
            map.panTo(place.geometry.location);
            marker.setPosition(place.geometry.location);
            mdGoogleMapAttributeUpdateValue(place.geometry.location);
        });
        const zoomSelector = document.querySelector('select[name="<?=$this->field('zoom')?>"]');
        zoomSelector.addEventListener('change', function () {
            map.setZoom(parseInt(zoomSelector.value));
        })
    }
    window.mdGoogleMapAttributeInitMap = mdGoogleMapAttributeInitMap;

    if (typeof google === 'object' && typeof google.maps === 'object') {
        mdGoogleMapAttributeInitMap();
    } else {
        const script = document.createElement('script');
        script.src = "<?= sprintf('https://maps.googleapis.com/maps/api/js?key=%s&callback=mdGoogleMapAttributeInitMap&v=weekly&libraries=places', $googleMapApiKey) ?>";
        script.async = true;
        script.defer = true;
        document.head.appendChild(script);
    }
</script>
