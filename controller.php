<?php

namespace Concrete\Package\SlifeC5Events;

use Concrete\Core\Package\Package as BasePackage;
use Concrete\Core\Support\Facade\Events;
use Concrete\Core\Support\Facade\Package;

class Controller extends BasePackage
{
    protected $pkgHandle = 'slife_c5_events';
    protected $appVersionRequired = '8.2';
    protected $pkgVersion = '1.0';
    protected $pkgAutoloaderRegistries = [
        'src' => '\SlifeC5Events',
    ];

    protected $supportedEvents = [
        'on_page_type_publish',
        'on_page_delete',
        'on_file_delete',
        'on_user_add',
        'on_user_login',
        'on_user_change_password',
    ];

    public function getPackageName()
    {
        return t('Slife C5 Events');
    }

    public function getPackageDescription()
    {
        return t('Slife Extension that adds and handles concrete5 events.');
    }

    public function on_start()
    {
        if (!$this->isSlifeInstalled()) {
            return;
        }

        $th = $this->app->make('helper/text');

        // Register event listeners
        foreach ($this->supportedEvents as $eventHandle) {
            $className = $th->camelcase($eventHandle);
            $listener = $this->app->make('SlifeC5Events\Event\\'.$className, [
                'package' => $this->getPackageEntity(),
            ]);
            Events::addListener($eventHandle, [$listener, 'run']);
        }
    }

    public function validate_install($data = [])
    {
        $error = $this->app->make('error');

        if (!$this->isSlifeInstalled()) {
            $error->add(
                t(
                    "Installation requires <a href='%s' target='_blank'>Slife</a> to be installed.",
                    "https://www.concrete5.org/marketplace/addons/slife/"
                )
            );
        }

        return $error;
    }

    /**
     * @return bool
     */
    public function isSlifeInstalled()
    {
        $basePackage = Package::getByHandle('slife');
        return is_object($basePackage);
    }

    public function install()
    {
        parent::install();
        $this->installEvents();
    }

    public function upgrade()
    {
        $this->installEvents();
    }

    protected function installEvents()
    {
        $th = $this->app->make('helper/text');

        foreach ($this->supportedEvents as $eventHandle) {
            $className = $th->camelcase($eventHandle);
            $eventClass = $this->app->make('SlifeC5Events\Event\\'.$className, [
                'package' => $this->getPackageEntity(),
            ]);

            $eventClass->install();
        }
    }
}
