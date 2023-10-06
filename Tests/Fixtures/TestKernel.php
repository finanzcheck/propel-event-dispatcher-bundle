<?php

namespace Bazinga\Bundle\PropelEventDispatcherBundle\Tests\Fixtures;

use Bazinga\Bundle\PropelEventDispatcherBundle\BazingaPropelEventDispatcherBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class TestKernel extends Kernel
{
    public function __construct($environment, $debug)
    {
        parent::__construct($environment, $debug);
    }

    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new BazingaPropelEventDispatcherBundle(),
            new BazingaPropelEventDispatcherTestBundle(),
        ];
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir().'/'.Kernel::VERSION.'/bazinga-propel-event-dispatcher/cache/'.$this->environment;
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir().'/'.Kernel::VERSION.'/bazinga-propel-event-dispatcher/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__.'/config/default.yml');
    }

    public function serialize(): string
    {
        return serialize(array($this->getEnvironment(), $this->isDebug()));
    }

    public function unserialize($str): void
    {
        call_user_func_array(array($this, '__construct'), unserialize($str));
    }
}
