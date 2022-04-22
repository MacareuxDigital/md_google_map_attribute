<?php

namespace Concrete\Package\MdGoogleMapAttribute\Attribute\GoogleMap;

use Concrete\Core\Attribute\Controller as AttributeController;
use Concrete\Core\Attribute\FontAwesomeIconFormatter;
use Concrete\Core\Http\Client\Client;
use Macareux\Package\GoogleMapAttribute\Entity\GoogleMapValue;

class Controller extends AttributeController
{
    public $helpers = ['form'];

    protected $searchIndexFieldDefinition = [
        'location' => [
            'type' => 'string',
            'options' => ['length' => '255', 'default' => '', 'notnull' => false],
        ],
    ];

    /**
     * @var string
     */
    protected $googleMapApiKey;

    public function getIconFormatter()
    {
        return new FontAwesomeIconFormatter('map-marker-alt');
    }

    public function getAttributeValueClass()
    {
        return GoogleMapValue::class;
    }

    public function getAttributeValueObject()
    {
        return $this->attributeValue ? $this->entityManager->find(GoogleMapValue::class, $this->attributeValue->getGenericValue()) : null;
    }

    public function createAttributeValueFromRequest()
    {
        return $this->createAttributeValue($this->post());
    }

    public function validateValue()
    {
        $v = $this->getAttributeValue()->getValue();
        if (!is_object($v)) {
            return false;
        }

        if (trim((string) $v->getLocation()) == '') {
            return false;
        }

        return true;
    }

    public function getSearchIndexValue()
    {
        /** @var GoogleMapValue $v */
        $v = $this->getAttributeValue()->getValue();

        return [
            'location' => $v->getLocation(),
        ];
    }

    public function getDisplayValue()
    {
        $value = $this->getAttributeValue()->getValue();
        $this->requireAsset('javascript', 'google_map_attribute');
        $zoom = $value->getZoom() ?? '';
        $latitude = $value->getLatitude() ?? '';
        $longitude = $value->getLongitude() ?? '';
        $marker = $value->getMarker() ?? 0;

        if ($value->getLocation() !== '') {
            $config = $this->app->make('config');
            $googleMapApiKey = $config->get('app.api_keys.google.maps');
            if ($googleMapApiKey) {
                $this->addFooterItem(
                    '<script async defer src="https://maps.googleapis.com/maps/api/js?callback=mdGoogleMapAttributeInit&key='
                    . $googleMapApiKey
                    . '"></script>'
                );
            }

            return "<div class=\"googleMapAttributeCanvas\"
		         data-zoom=\"{$zoom}\"
		         data-latitude=\"{$latitude}\"
		         data-longitude=\"{$longitude}\"
		         data-marker=\"{$marker}\"
		         /></div>";
        }

        return null;
    }

    public function validateKey($data = false)
    {
        $googleMapApiKey = $data['googleMapApiKey'];

        $e = $this->app->make('error');

        if (empty($googleMapApiKey)) {
            $e->add(t('You must specify a API Key.'));
        }
	
	    $api_url = 'https://maps.googleapis.com/maps/api/place/findplacefromtext/json?input=Museum%20of%20Contemporary%20Art%20Australia&inputtype=textquery&fields=formatted_address%2Cname%2Crating%2Copening_hours%2Cgeometry&key=' . $googleMapApiKey;
        /** @var Client $client */
        $client = $this->app->make('http/client');
        $response = $client->get($api_url);
        $data = json_decode($response->getBody());

        if ($data->error_message) {
            $e->add(t($data->error_message));
        }

        return $e;
    }

    public function createAttributeValue($data)
    {
        if ($data instanceof GoogleMapValue) {
            return clone $data;
        }
        extract($data);

        $av = new GoogleMapValue();
        $av->setLocation($location);
        $av->setLatitude($latitude);
        $av->setLongitude($longitude);
        $av->setZoom($zoom);
        $av->setMarker($marker);

        return $av;
    }

    public function saveKey($data)
    {
        $type = $this->getAttributeKeySettings();

        $config = $this->app->make('config');
        $config->save('app.api_keys.google.maps', trim($data['googleMapApiKey']));

        return $type;
    }

    public function type_form()
    {
        $this->load();
    }

    public function form()
    {
        $this->load();

        if (is_object($this->attributeValue)) {
            /** @var GoogleMapValue $value */
            $value = $this->getAttributeValue()->getValue();
            if ($value) {
                $this->set('location', $value->getLocation());
                $this->set('latitude', $value->getLatitude());
                $this->set('longitude', $value->getLongitude());
                $this->set('zoom', $value->getZoom());
                $this->set('marker', $value->getMarker());
            }
        }
    }

    protected function load()
    {
        $ak = $this->getAttributeKey();
        if (!is_object($ak)) {
            return false;
        }

        $config = $this->app->make('config');
        $googleMapApiKey = $config->get('app.api_keys.google.maps');
        $this->set('googleMapApiKey', $googleMapApiKey);
    }
}
