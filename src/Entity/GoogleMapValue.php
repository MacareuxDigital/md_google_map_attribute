<?php

namespace Macareux\Package\GoogleMapAttribute\Entity;

use Concrete\Core\Entity\Attribute\Value\Value\AbstractValue;
use Concrete\Core\Support\Facade\Application;
use Doctrine\ORM\Mapping as ORM;
use Macareux\Package\GoogleMapAttribute\Utility\GoogleMapRendererInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="atMacareuxGoogleMap")
 */
class GoogleMapValue extends AbstractValue
{
    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $location;

    /**
     * @var float
     * @ORM\Column(type="float", nullable=true)
     */
    protected $latitude;

    /**
     * @var float
     * @ORM\Column(type="float", nullable=true)
     */
    protected $longitude;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $zoom;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $marker = false;

    public function __toString()
    {
        $html = '';
        $app = Application::getFacadeApplication();
        $config = $app->make('config');
        $googleMapApiKey = $config->get('app.api_keys.google.maps');
        if ($googleMapApiKey) {
            $latitude = $this->getLatitude() ?? 0;
            $longitude = $this->getLongitude() ?? 0;
            $zoom = $this->getZoom() ?? 14;
            $location = $this->getLocation() ?? '';
            $showMarker = $this->getMarker() ?? false;
            $html = $app->make(GoogleMapRendererInterface::class, [
                'apiKey' => $googleMapApiKey,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'zoom' => $zoom,
                'location' => $location,
                'showMarker' => $showMarker,
            ])->getOutput();
        }

        return $html;
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param string $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param float $latitude
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }

    /**
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param float $longitude
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    }

    /**
     * @return int
     */
    public function getZoom()
    {
        return $this->zoom;
    }

    /**
     * @param int $zoom
     */
    public function setZoom($zoom)
    {
        $this->zoom = $zoom;
    }

    /**
     * @return bool
     */
    public function getMarker()
    {
        return $this->marker;
    }

    /**
     * @param bool $marker
     */
    public function setMarker($marker)
    {
        $this->marker = $marker;
    }
}
