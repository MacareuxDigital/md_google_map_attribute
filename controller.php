<?php

namespace Concrete\Package\MdGoogleMapAttribute;

use Concrete\Core\Attribute\Category\CategoryService;
use Concrete\Core\Attribute\TypeFactory;
use Concrete\Core\Package\Package;
use Concrete\Core\Package\PackageService;
use Macareux\Package\GoogleMapAttribute\MigrationTool\Import\CIF\Attribute\Value\GoogleMapImporter;
use Macareux\Package\GoogleMapAttribute\Utility\GoogleMapRenderer;
use Macareux\Package\GoogleMapAttribute\Utility\GoogleMapRendererInterface;

class Controller extends Package
{
    protected $pkgHandle = 'md_google_map_attribute';

    protected $appVersionRequired = '9.0.0';

    protected $pkgVersion = '1.1';

    protected $pkgAutoloaderRegistries = [
        'src/Entity' => '\Macareux\Package\GoogleMapAttribute\Entity',
        'src/Utility' => '\Macareux\Package\GoogleMapAttribute\Utility',
    ];

    /**
     * {@inheritdoc}
     */
    public function getPackageAutoloaderRegistries()
    {
        $registries = parent::getPackageAutoloaderRegistries();
        if ($this->isMigrationToolInstalled()) {
            $registries['src/MigrationTool'] = '\Macareux\Package\GoogleMapAttribute\MigrationTool';
        }

        return $registries;
    }

    public function getPackageName()
    {
        return t('Macareux Google Map Attribute');
    }

    public function getPackageDescription()
    {
        return t('Add a new attribute type to set location for objects with google map interface.');
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

    public function on_after_packages_start()
    {
        if ($this->isMigrationToolInstalled()) {
            $db = $this->app->make('database')->connection();
            if (!$db->tableExists('MigrationImportAttributeGoogleMapValues')) {
                $this->installEntitiesDatabase();
            }
            $manager = $this->app->make('migration/manager/import/attribute/value');
            $manager->extend('google_map', function () {
                return new GoogleMapImporter();
            });
        }
    }

    private function isMigrationToolInstalled(): bool
    {
        /** @var PackageService $packageService */
        $packageService = $this->app->make(PackageService::class);
        $migrationToolPackage = $packageService->getByHandle('migration_tool');
        if ($migrationToolPackage && $migrationToolPackage->isPackageInstalled()) {
            return true;
        }

        return false;
    }
}
