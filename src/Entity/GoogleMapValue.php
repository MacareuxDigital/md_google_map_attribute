<?php

namespace Macareux\Package\GoogleMapAttribute\Entity;

use Concrete\Core\Entity\Attribute\Value\Value\AbstractValue;
use Concrete\Core\Localization\Localization;
use Doctrine\ORM\Mapping as ORM;

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
