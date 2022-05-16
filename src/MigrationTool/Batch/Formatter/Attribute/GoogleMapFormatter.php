<?php

namespace Macareux\Package\GoogleMapAttribute\MigrationTool\Batch\Formatter\Attribute;

use Macareux\Package\GoogleMapAttribute\MigrationTool\Entity\Import\AttributeValue\GoogleMapValue;
use PortlandLabs\Concrete5\MigrationTool\Batch\Formatter\TreeContentItemFormatterInterface;

class GoogleMapFormatter implements TreeContentItemFormatterInterface
{
    protected $value;

    public function __construct(GoogleMapValue $value)
    {
        $this->value = $value;
    }

    public function getBatchTreeNodeJsonObject()
    {
        $node = new \stdClass();
        $node->title = $this->value->getAttribute()->getHandle();
        $node->icon = 'fas fa-map-marker-alt';
        $node->children = [];
        $labels = [];
        $labels[] = ['field' => t('Location'), 'value' => $this->value->getLocation()];
        $labels[] = ['field' => t('Latitude'), 'value' => $this->value->getLatitude()];
        $labels[] = ['field' => t('Longitude'), 'value' => $this->value->getLongitude()];
        $labels[] = ['field' => t('Zoom'), 'value' => $this->value->getZoom()];
        $labels[] = ['field' => t('Marker'), 'value' => $this->value->getMarker()];
        foreach ($labels as $label) {
            $child = new \stdClass();
            $child->title = $label['field'];
            $child->itemvalue = $label['value'];
            $node->children[] = $child;
        }

        return $node;
    }
}
