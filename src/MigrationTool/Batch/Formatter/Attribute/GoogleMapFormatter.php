<?php

namespace Macareux\Package\GoogleMapAttribute\MigrationTool\Batch\Formatter\Attribute;

use Macareux\Package\GoogleMapAttribute\MigrationTool\Entity\Import\AttributeValue\GoogleMapValue;
use PortlandLabs\Concrete5\MigrationTool\Batch\Formatter\TreeContentItemFormatterInterface;

class GoogleMapFormatter implements TreeContentItemFormatterInterface
{
    protected $value;

    public function getBatchTreeNodeJsonObject()
    {
        $node = new \stdClass();
        $node->title = $this->value->getAttribute()->getHandle();
        $node->icon = 'fas fa-map-marker-alt';
        $node->children = array();
        $labels = array();
        $labels[] = array('field' => t('Location'), 'value' => $this->value->getLocation());
        $labels[] = array('field' => t('Latitude'), 'value' => $this->value->getLatitude());
        $labels[] = array('field' => t('Longitude'), 'value' => $this->value->getLongitude());
        $labels[] = array('field' => t('Zoom'), 'value' => $this->value->getZoom());
        $labels[] = array('field' => t('Marker'), 'value' => $this->value->getMarker());
        foreach ($labels as $label) {
            $child = new \stdClass();
            $child->title = $label['field'];
            $child->itemvalue = $label['value'];
            $node->children[] = $child;
        }

        return $node;
    }

    public function __construct(GoogleMapValue $value)
    {
        $this->value = $value;
    }
}