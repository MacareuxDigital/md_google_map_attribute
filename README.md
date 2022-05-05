# Macareux Google Map Attribute

A Concrete CMS package to add a new attribute type to set location for objects with google map interface.

## How to use

This package installs the "Google Map" attribute type.

You can set a location to objects like pages, users, express entries, etc.
Please add an attribute key for objects.
You have to get a Google Cloud Platform API Key that enables Places API and Maps Static API for it.

You can show Google Static MAP by Page Attribute Display block with no coding.

### Get coordinates by code

```php
/* @var \Concrete\Core\Page\Page $page */

// Display Static MAP
echo $page->getAttribute('your_attribute_handle');

/** @var \Macareux\Package\GoogleMapAttribute\Entity\GoogleMapValue $value */
$value = $page->getAttribute('your_attribute_handle');
if ($value) {
    // Get values manually
    $location = $value->getLocation();
    $latitude = $value->getLatitude();
    $longitude = $value->getLongitude();
    $zoom = $value->getZoom();
    $showMarker = $value->getMarker();
}
```

### Sort ItemList the nearest item first

```php
use Concrete\Core\Page\PageList;
use Macareux\Package\GoogleMapAttribute\Utility\QueryApplier;

$list = new PageList();
$currentLatitude = 35.6681625;
$currentLongitude = 139.6007834;
QueryApplier::applySortByNearest($list, 'your_attribute_handle', $currentLatitude, $currentLongitude);
$pages = $list->getResults();
```

If you don't familiar with `PageList` class, please check the official documentation:
[Searching and Sorting with the PageList object](https://documentation.concretecms.org/developers/pages-themes/working-with-pages/searching-and-sorting-with-the-pagelist-object)

## License

MIT License.
