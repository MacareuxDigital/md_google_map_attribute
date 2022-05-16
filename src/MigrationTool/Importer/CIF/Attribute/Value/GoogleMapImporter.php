<?php

namespace Macareux\Package\GoogleMapAttribute\MigrationTool\Import\CIF\Attribute\Value;

use Macareux\Package\GoogleMapAttribute\MigrationTool\Entity\Import\AttributeValue\GoogleMapValue;
use PortlandLabs\Concrete5\MigrationTool\Importer\CIF\Attribute\Value\AbstractImporter;

class GoogleMapImporter extends AbstractImporter
{
    public function parse(\SimpleXMLElement $node)
    {
        $value = new GoogleMapValue();
        $value->setLocation((string) $node->value['location']);
        $value->setLatitude((string) $node->value['latitude']);
        $value->setLongitude((string) $node->value['longitude']);
        $value->setZoom((string) $node->value['zoom']);
        $value->setMarker((bool) $node->value['marker']);

        return $value;
    }
}