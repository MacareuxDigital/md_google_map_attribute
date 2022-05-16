<?php

namespace Macareux\Package\GoogleMapAttribute\MigrationTool\Publisher\Attribute;

use Macareux\Package\GoogleMapAttribute\Entity\GoogleMapValue;
use PortlandLabs\Concrete5\MigrationTool\Entity\Import\AttributeValue\AttributeValue;
use PortlandLabs\Concrete5\MigrationTool\Entity\Import\Batch;
use PortlandLabs\Concrete5\MigrationTool\Publisher\Attribute\PublisherInterface;

class GoogleMapPublisher implements PublisherInterface
{
    public function publish(Batch $batch, $attributeKey, $subject, AttributeValue $value)
    {
        $attributeValue = new GoogleMapValue();
        $attributeValue->setLocation($value->getLocation());
        $attributeValue->setLatitude($value->getLatitude());
        $attributeValue->setLongitude($value->getLongitude());
        $attributeValue->setZoom($value->getZoom());
        $attributeValue->setMarker($value->getMarker());
        $subject->setAttribute($attributeKey->getAttributeKeyHandle(), $attributeValue);
    }
}
