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

    protected $pkgVersion = '0.9.0';

    protected $pkgAutoloaderRegistries = [
        'src' => '\Macareux\Package\GoogleMapAttribute',
    ];

    public function getPackageName()
    {
        return t('Macareux Google Map Attribute');
    }

    public function getPackageDescription()
    {
        return t('Add a new attribute type to set location to objects with google map interface.');
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
            $collectionCategory = $service->getByHandle('collection');
            if ($collectionCategory) {
                $collectionCategory->getController()->associateAttributeKeyType($type);
            }
            $userCategory = $service->getByHandle('user');
            if ($userCategory) {
                $userCategory->getController()->associateAttributeKeyType($type);
            }
            $fileCategory = $service->getByHandle('file');
            if ($fileCategory) {
                $fileCategory->getController()->associateAttributeKeyType($type);
            }
            $siteCategory = $service->getByHandle('site');
            if ($siteCategory) {
                $siteCategory->getController()->associateAttributeKeyType($type);
            }
            $eventCategory = $service->getByHandle('event');
            if ($eventCategory) {
                $eventCategory->getController()->associateAttributeKeyType($type);
            }
            $expressCategory = $service->getByHandle('express');
            if ($expressCategory) {
                $expressCategory->getController()->associateAttributeKeyType($type);
            }
        }
    }

    public function on_start()
    {
        $this->app->bind(GoogleMapRendererInterface::class, GoogleMapRenderer::class);
    }
}
