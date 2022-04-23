<?php

namespace Concrete\Package\MdGoogleMapAttribute;

use Concrete\Core\Attribute\Category\CategoryService;
use Concrete\Core\Attribute\TypeFactory;
use Concrete\Core\Package\Package;
use Macareux\Package\GoogleMapAttribute\Utility\GoogleMapRenderer;
use Macareux\Package\GoogleMapAttribute\Utility\GoogleMapRendererInterface;

class Controller extends Package
{
    protected $pkgHandle = 'md_google_map_attribute';

    protected $appVersionRequired = '9.0.0';

    protected $pkgVersion = '0.0.1';

    protected $pkgAutoloaderRegistries = [
        'src' => '\Macareux\Package\GoogleMapAttribute',
    ];

    public function getPackageName()
    {
        return t('Macareux Google Map Attribute');
    }

    public function getPackageDescription()
    {
        return t('Add a new attribute type for google map.');
    }

    public function install()
    {
        $pkg = parent::install();

        /** @var TypeFactory $factory */
        $factory = $this->app->make(TypeFactory::class);
        $type = $factory->getByHandle('google_map');
        if (!is_object($type)) {
            $type = $factory->add('google_map', 'Google Map', $pkg);
            /** @var CategoryService $service */
            $service = $this->app->make(CategoryService::class);
            $category = $service->getByHandle('collection')->getController();
            $category->associateAttributeKeyType($type);
        }
    }

    public function on_start()
    {
        $this->app->bind(GoogleMapRendererInterface::class, GoogleMapRenderer::class);
    }
}
