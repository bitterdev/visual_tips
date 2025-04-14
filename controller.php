<?php

namespace Concrete\Package\VisualTips;

use Bitter\VisualTips\Provider\ServiceProvider;
use Concrete\Core\Entity\Package as PackageEntity;
use Concrete\Core\Package\Package;

class Controller extends Package
{
    protected string $pkgHandle = 'visual_tips';
    protected string $pkgVersion = '0.0.1';
    protected $appVersionRequired = '9.0.0';
    protected $pkgAutoloaderRegistries = [
        'src/Bitter/VisualTips' => 'Bitter\VisualTips',
    ];

    public function getPackageDescription(): string
    {
        return t('Add interactive tooltips to any image with pinpoint accuracy.');
    }

    public function getPackageName(): string
    {
        return t('Visual Tips');
    }

    public function on_start()
    {
        /** @var ServiceProvider $serviceProvider */
        /** @noinspection PhpUnhandledExceptionInspection */
        $serviceProvider = $this->app->make(ServiceProvider::class);
        $serviceProvider->register();
    }

    public function install(): PackageEntity
    {
        $pkg = parent::install();
        $this->installContentFile("data.xml");
        return $pkg;
    }

    public function upgrade()
    {
        parent::upgrade();
        $this->installContentFile("data.xml");
    }
}