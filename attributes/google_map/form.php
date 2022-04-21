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
<div>
    <div id="ccm-attribute-google-map-note" class="alert alert-info" role="alert"></div>
    <div class="form-group" id="ccm-attribute-google-map">
        <?= $form->label('location', t('Google Map')) ?>
        <?= $form->text($this->field('location'), $location, ['placeholder' => 'Enter a location']) ?>
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
<script>
    $(document).ready(function () {
        'use strict';
    
        var $key = $('#apiKey'),
            $location = $('#ccm-attribute-google-map > input[id="<?=$this->field('location')?>"]'),
            $note = $("#ccm-attribute-google-map-note");
        
        var setupApiKey = (function () {
            var checking = false,
                     $script = null,
                    originalGMAuthFailure = window.gm_authFailure,
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
                        $note.text(<?= json_encode(t('The place you entered could not be found.')) ?>).addClass('alert-danger').css('visibility', '');
                    } else {
                        $('#ccm-attribute-google-map > input[id="<?=$this->field('latitude')?>"]').val(place.geometry.location.lat());
                        $('#ccm-attribute-google-map > input[id="<?=$this->field('longitude')?>"]').val(place.geometry.location.lng());
                        $location.removeClass('notfound');
                        $note.css('visibility', 'hidden');
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
                        if (originalGMAuthFailure) {
                            window.gm_authFailure = originalGMAuthFailure;
                        } else {
                            delete window.gm_authFailure;
                        }
                       // $checkSpinner.removeClass('fa-refresh fa-spin').addClass('fa-play');
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
                    window.gm_authFailure = function () {
                        placesLoaded(<?= json_encode(t('The API Key is NOT valid.')) ?>);
                    };
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
                window.gm_authFailure = function () {
                    scriptLoaded(<?= json_encode(t('The API Key is NOT valid.')) ?>);
                };
                
                $(document.body).append($script = $('<' + 'script src="https://maps.googleapis.com/maps/api/js?key=' + encodeURIComponent(key) + '&libraries=places&callback=' + encodeURIComponent(scriptLoadedFunctionName) + '"></' + 'script>'));
            };
        })();

        setupApiKey(
                function () {
                    $note.text(<?= json_encode(t('The API Key is valid.')) ?>).removeClass('alert-info alert-danger').addClass('alert-success');
                },
                function (err) {
                    $note.text(err).removeClass('alert-success alert-info').addClass('alert-danger');
                },
                true
        );
    
        $location.on('keydown', function (e) {
            if (e.keyCode === 13) {
                e.preventDefault();
            }
        });
    
    }());
</script>