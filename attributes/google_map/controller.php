<?php

namespace Concrete\Package\MdGoogleMapAttribute\Attribute\GoogleMap;

use Concrete\Core\Attribute\Controller as AttributeController;
use Concrete\Core\Attribute\FontAwesomeIconFormatter;
use Concrete\Core\Error\ErrorList\Error\Error;
use Concrete\Core\Error\ErrorList\Error\FieldNotPresentError;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Error\ErrorList\Field\AttributeField;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Http\Client\Client;
use Concrete\Core\Search\ItemList\Database\AttributedItemList;
use GuzzleHttp\Exception\ClientException;
use Macareux\Package\GoogleMapAttribute\Entity\GoogleMapValue;

class Controller extends AttributeController
{
    public $helpers = ['form'];

    protected $searchIndexFieldDefinition = [
        'location' => [
            'type' => 'string',
            'options' => ['length' => '255', 'default' => '', 'notnull' => false],
        ],
        'latitude' => [
            'type' => 'float',
            'options' => ['default' => null, 'notnull' => false],
        ],
        'longitude' => [
            'type' => 'float',
            'options' => ['default' => null, 'notnull' => false],
        ],
    ];

    /**
     * @var string
     */
    protected $googleMapApiKey;

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

    public function createAttributeValueFromRequest()
    {
        return $this->createAttributeValue($this->post());
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

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\MulticolumnTextExportableAttributeInterface::getAttributeTextRepresentationHeaders()
     */
    public function getAttributeTextRepresentationHeaders()
    {
        return [
            'location',
            'latitude',
            'longitude',
            'zoom',
            'marker',
        ];
    }

    public function getAttributeValueClass()
    {
        return GoogleMapValue::class;
    }

    public function getAttributeValueObject()
    {
        return $this->attributeValue ? $this->entityManager->find(GoogleMapValue::class, $this->attributeValue->getGenericValue()) : null;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\MulticolumnTextExportableAttributeInterface::getAttributeValueTextRepresentation()
     */
    public function getAttributeValueTextRepresentation()
    {
        /** @var GoogleMapValue $value */
        $value = $this->getAttributeValueObject();

        return [
            $value ? $value->getLocation() : '',
            $value ? (string) $value->getLatitude() : '',
            $value ? (string) $value->getLongitude() : '',
            $value ? (string) $value->getZoom() : '',
            $value ? (string) $value->getMarker() : '',
        ];
    }

    public function getIconFormatter()
    {
        return new FontAwesomeIconFormatter('map-marker-alt');
    }

    public function getSearchIndexValue()
    {
        /** @var GoogleMapValue $v */
        $v = $this->getAttributeValue()->getValue();

        return [
            'location' => $v->getLocation(),
            'latitude' => $v->getLatitude(),
            'longitude' => $v->getLongitude(),
        ];
    }

    public function saveKey($data)
    {
        $type = $this->getAttributeKeySettings();

        $config = $this->app->make('config');
        $config->save('app.api_keys.google.maps', trim($data['googleMapApiKey']));

        return $type;
    }

    public function search()
    {
        /** @var Form $form */
        $form = $this->app->make('helper/form');

        echo $form->text($this->field('value'), $this->request('value'));
    }

    public function searchForm($list)
    {
        /** @var AttributedItemList $list */
        $value = $this->request('value');
        $list->getQueryObject()->andWhere(
            $list->getQueryObject()->expr()->like(
                'ak_' . $this->attributeKey->getAttributeKeyHandle() . '_location',
                $list->getQueryObject()->createNamedParameter('%' . $value . '%')
            )
        );
    }

    public function searchKeywords($keywords, $queryBuilder)
    {
        if ($this->attributeKey) {
            $result = $queryBuilder->expr()->like('ak_' . $this->attributeKey->getAttributeKeyHandle() . '_location', ':keywords');
        } else {
            $result = null;
        }

        return $result;
    }

    public function type_form()
    {
        $this->load();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\MulticolumnTextExportableAttributeInterface::updateAttributeValueFromTextRepresentation()
     */
    public function updateAttributeValueFromTextRepresentation(array $textRepresentation, ErrorList $warnings)
    {
        $textRepresentation = array_map('trim', $textRepresentation);
        $value = $this->getAttributeValueObject();
        if ($value === null) {
            if (implode('', $textRepresentation) !== '') {
                $value = new GoogleMapValue();
            }
        }
        if ($value !== null) {
            /** @var GoogleMapValue $value */
            $value->setLocation(trim(array_shift($textRepresentation)));
            $value->setLatitude((float) trim(array_shift($textRepresentation)));
            $value->setLongitude((float) trim(array_shift($textRepresentation)));
            $value->setZoom((int) trim(array_shift($textRepresentation)));
            $value->setMarker((bool) trim(array_shift($textRepresentation)));
        }

        return $value;
    }

    public function validateForm($data)
    {
        $ak = $this->getAttributeKey();
        /** @var ErrorList $errorList */
        $errorList = $this->app->make('helper/validation/error');

        if (
            isset($data['location']) && !empty($data['location'])
            && isset($data['latitude']) && !empty($data['latitude'])
            && isset($data['longitude']) && !empty($data['longitude'])
            && isset($data['zoom']) && !empty($data['zoom'])
        ) {
            $latitude = (float) $data['latitude'];
            if ($latitude > 90 || $latitude < -90) {
                $errorList->add(t('Invalid latitude value.'), new Error(new AttributeField($ak)));
            }
            $longitude = (float) $data['longitude'];
            if ($longitude > 180 || $longitude < -180) {
                $errorList->add(t('Invalid longitude value.'), new Error(new AttributeField($ak)));
            }
            $zoom = (int) $data['zoom'];
            if ($zoom < 0) {
                $errorList->add(t('Invalid zoom value.'), new Error(new AttributeField($ak)));
            }
        } else {
            $errorList->add(new FieldNotPresentError(new AttributeField($ak)));
        }

        return $errorList;
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

        if (isset($data->error_message) && !empty($data->error_message)) {
            $e->add(t('Invalid API key for Places API. Response from API: %s', $data->error_message));
        }

        $static_api_url = 'https://maps.googleapis.com/maps/api/staticmap?center=40.714%2c%20-73.998&zoom=12&size=400x400&key=' . $googleMapApiKey;
        /** @var Client $client */
        $client = $this->app->make('http/client');
        try {
            $client->get($static_api_url);
        } catch (ClientException $clientException) {
            $e->add(t('Invalid API key for Static API. Response from API: %s', $clientException->getResponse()->getBody()));
        }

        return $e;
    }

    public function validateValue()
    {
        /** @var GoogleMapValue $v */
        $v = $this->getAttributeValue()->getValue();
        if (!is_object($v)) {
            return false;
        }

        if (trim((string) $v->getLocation()) === '') {
            return false;
        }

        if ($v->getLatitude() > 90 || $v->getLatitude() < -90) {
            return false;
        }

        if ($v->getLongitude() > 180 || $v->getLongitude() < -180) {
            return false;
        }

        return true;
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
