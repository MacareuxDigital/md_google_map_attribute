<?php

namespace Macareux\Package\GoogleMapAttribute\Utility;

use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Url\Url;

class GoogleMapRenderer implements GoogleMapRendererInterface
{
    public const BASE_URL = 'https://maps.googleapis.com/maps/api/staticmap';

    /** @var Key */
    protected $attributeKey;
    /** @var string */
    protected $apiKey;
    /** @var float */
    protected $latitude;
    /** @var float */
    protected $longitude;
    /** @var int */
    protected $zoom;
    /** @var string */
    protected $location;
    /** @var bool */
    protected $showMarker;
    /** @var string */
    protected $size;

    /**
     * @param string $apiKey
     * @param float $latitude
     * @param float $longitude
     * @param int $zoom
     * @param string $location
     * @param bool $showMarker
     * @param string $size
     */
    public function __construct(Key $attributeKey, string $apiKey, float $latitude, float $longitude, int $zoom, string $location, bool $showMarker, string $size = '640x480')
    {
        $this->attributeKey = $attributeKey;
        $this->apiKey = $apiKey;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->zoom = $zoom;
        $this->location = $location;
        $this->showMarker = $showMarker;
        $this->size = $size;
    }

    /**
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * @param string $apiKey
     */
    public function setApiKey(string $apiKey): void
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @return float
     */
    public function getLatitude(): float
    {
        return $this->latitude;
    }

    /**
     * @param float $latitude
     */
    public function setLatitude(float $latitude): void
    {
        $this->latitude = $latitude;
    }

    /**
     * @return float
     */
    public function getLongitude(): float
    {
        return $this->longitude;
    }

    /**
     * @param float $longitude
     */
    public function setLongitude(float $longitude): void
    {
        $this->longitude = $longitude;
    }

    /**
     * @return int
     */
    public function getZoom(): int
    {
        return $this->zoom;
    }

    /**
     * @param int $zoom
     */
    public function setZoom(int $zoom): void
    {
        $this->zoom = $zoom;
    }

    /**
     * @return string
     */
    public function getLocation(): string
    {
        return $this->location;
    }

    /**
     * @param string $location
     */
    public function setLocation(string $location): void
    {
        $this->location = $location;
    }

    /**
     * @return bool
     */
    public function isShowMarker(): bool
    {
        return $this->showMarker;
    }

    /**
     * @param bool $showMarker
     */
    public function setShowMarker(bool $showMarker): void
    {
        $this->showMarker = $showMarker;
    }

    /**
     * @return string
     */
    public function getSize(): string
    {
        return $this->size;
    }

    /**
     * @param string $size
     */
    public function setSize(string $size): void
    {
        $this->size = $size;
    }

    public function getOutput(): string
    {
        $options = [
            'center' => $this->latitude . ',' . $this->longitude,
            'zoom' => $this->zoom,
            'size' => $this->size,
            'key' => $this->apiKey,
        ];
        if ($this->showMarker) {
            $options['markers'] = 'size:mid|' . $this->latitude . ',' . $this->longitude;
        }
        $url = Url::createFromUrl(self::BASE_URL);
        $url->setQuery($options);

        return sprintf('<img src="%s" alt="%s" class="img-fluid">', $url, $this->location);
    }
}
