<?php

namespace Macareux\Package\GoogleMapAttribute\MigrationTool\Entity\Import\AttributeValue;

use Doctrine\ORM\Mapping as ORM;
use Macareux\Package\GoogleMapAttribute\MigrationTool\Batch\Formatter\Attribute\GoogleMapFormatter;
use Macareux\Package\GoogleMapAttribute\MigrationTool\Publisher\Attribute\GoogleMapPublisher;
use PortlandLabs\Concrete5\MigrationTool\Entity\Import\AttributeValue\AttributeValue;

/**
 * @ORM\Entity
 * @ORM\Table(name="MigrationImportAttributeGoogleMapValues")
 */
class GoogleMapValue extends AttributeValue
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
     * @return bool
     */
    public function getMarker(): bool
    {
        return $this->marker;
    }

    /**
     * @param bool $marker
     */
    public function setMarker(bool $marker): void
    {
        $this->marker = $marker;
    }

    public function getFormatter()
    {
        return new GoogleMapFormatter($this);
    }

    public function getPublisher()
    {
        return new GoogleMapPublisher();
    }
}
