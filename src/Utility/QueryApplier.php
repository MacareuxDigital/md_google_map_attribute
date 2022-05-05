<?php

namespace Macareux\Package\GoogleMapAttribute\Utility;

use Concrete\Core\Search\ItemList\Database\AttributedItemList;

class QueryApplier
{
    public static function applySortByNearest(AttributedItemList $list, string $attributeKeyHandle, float $latitude, float $longitude, string $order = 'ASC')
    {
        $latitude_column = 'ak_' . $attributeKeyHandle . '_latitude';
        $longitude_column = 'ak_' . $attributeKeyHandle . '_longitude';
        $list->getQueryObject()
            ->addSelect("ST_Distance_Sphere(POINT({$longitude_column}, {$latitude_column}), POINT({$longitude}, {$latitude})) AS distance")
            ->andWhere($list->getQueryObject()->expr()->and(
                $list->getQueryObject()->expr()->isNotNull($longitude_column),
                $list->getQueryObject()->expr()->isNotNull($latitude_column)
            ))
            ->orderBy('distance')
        ;
    }
}
