<?php
defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Support\Facade\Application;

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
            <?= $form->text($this->field('location'), $location, ['placeholder' => 'Enter a location']) ?>
            <?= $form->hidden('apiKey', $config->get('app.api_keys.google.maps')) ?>
            <?= $form->hidden($this->field('latitude'), $latitude) ?>
            <?= $form->hidden($this->field('longitude'), $longitude) ?>
        </div>

        <div>
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
    $(document).ready(function () {
        'use strict';
    
        var $key = $('#apiKey'),
            $location = $('#ccm-attribute-google-map > input[id="<?=$this->field('location')?>"]');
        
        var setupApiKey = (function () {
            var checking = false,
                    $script = null,
                    lastKey = null,
                    lastKeyError = null,
                    autocomplete = null;
    
            function setAutocompletion(places) {
                if (autocomplete) {
                    $location.removeAttr('placeholder autocomplete disabled style').removeClass('gm-err-autocomplete notfound');
                    google.maps.event.removeListener(autocomplete.listener);
                    google.maps.event.clearInstanceListeners(autocomplete.autocomplete);
                    clearInterval(autocomplete.pacTimer);
                    $('.pac-container').remove();
                    $location.off('change');
                    autocomplete = null;
                }
                if (!places) {
                    return;
                }
                autocomplete = {
                    autocomplete: new google.maps.places.Autocomplete($location[0])
                }
                autocomplete.listener = google.maps.event.addListener(autocomplete.autocomplete, 'place_changed', function () {
                    if (autocomplete === null) {
                        return;
                    }
                    var place = autocomplete.autocomplete.getPlace();
                    if (!place.geometry) {
                        $location.addClass('notfound');
                    } else {
                        $('#ccm-attribute-google-map > input[id="<?=$this->field('latitude')?>"]').val(place.geometry.location.lat());
                        $('#ccm-attribute-google-map > input[id="<?=$this->field('longitude')?>"]').val(place.geometry.location.lng());
                        
                        let latlng = new google.maps.LatLng(place.geometry.location.lat(), place.geometry.location.lng());
                        let zoom = parseInt($('select[name="<?=$this->field('zoom')?>"]').val());

                        var mapOptions = {
                            zoom: zoom,
                            center: latlng,
                            mapTypeControl: false
                        };

                       const map = new google.maps.Map(document.getElementById("mdGoogleMapAttributeCanvas"), mapOptions);

                        if($('input[name="<?=$this->field('marker')?>"]').prop('checked') == true){
                            new google.maps.Marker({
                                position: latlng,
                                map: map
                            });
                        }
                        $location.removeClass('notfound');
                    }
                });
                $location.on('change', function () {
                    $location.addClass('notfound');
                });
               
                autocomplete.pacTimer = setInterval(function () {
                    $('.pac-container').css('z-index', '2000');
                    if ($('#ccm-attribute-google-map > input[id="<?=$this->field('location')?>"]').length === 0) {
                        setAutocompletion(null);
                    }
                }, 250);
            }
    
            return function (onSuccess, onError, forceRecheck) {
                if (checking) {
                    onError(<?= json_encode(t('Please wait, operation in progress.')) ?>);
                    return;
                }
                if (!onSuccess) {
                    onSuccess = function () {
                    };
                }
                if (!onError) {
                    onError = function () {
                    };
                }
                var key = $.trim($key.val());
                if (key === lastKey && !forceRecheck) {
                    if (lastKeyError === null) {
                        onSuccess();
                    } else {
                        onError(lastKeyError);
                    }
                    return;
                }
    
                function completed(places) {
                    setAutocompletion(places);
                    if (lastKeyError === null) {
                        onSuccess();
                    } else {
                        onError(lastKeyError);
                    }
                }
    
                setAutocompletion();
                checking = true;
                if ($script !== null) {
                    $script.remove();
                    $script = null;
                }
                var scriptLoadedFunctionName;
                for (var i = 0; ; i++) {
                    scriptLoadedFunctionName = '_ccm_gmapblock_loaded_' + i;
                    if (typeof window[scriptLoadedFunctionName] === 'undefined') {
                        break;
                    }
                }

                function scriptLoaded(error) {
                    delete window[scriptLoadedFunctionName];

                    function placesLoaded(error, places) {
                        lastKey = key;
                        lastKeyError = error;
                        setTimeout(function () {
                            checking = false;
                            completed(places)
                        }, 10);
                    }

                    if (error !== null) {
                        placesLoaded(error);
                        return;
                    }
                   var places = new google.maps.places.PlacesService(document.createElement('div'));
                    places.getDetails(
                            {
                                placeId: 'ChIJJ3SpfQsLlVQRkYXR9ua5Nhw'
                            },
                            function (place, status) {
                                if (status === 'REQUEST_DENIED') {
                                    placesLoaded(<?= json_encode(t('The API Key is NOT valid for Google Places or not linked to billing account.')) ?>);
                                } else {
                                    placesLoaded(null, places);
                                }
                            }
                    );
                }

                window[scriptLoadedFunctionName] = function () {
                    scriptLoaded(null);
                };
                $(document.body).append($script = $('<' + 'script src="https://maps.googleapis.com/maps/api/js?key=' + encodeURIComponent(key) + '&libraries=places&callback=' + encodeURIComponent(scriptLoadedFunctionName) + '"></' + 'script>'));
            };
        })();

        setupApiKey();
    
        $location.on('keydown', function (e) {
            if (e.keyCode === 13) {
                e.preventDefault();
            }
        });
    
    }());
</script>