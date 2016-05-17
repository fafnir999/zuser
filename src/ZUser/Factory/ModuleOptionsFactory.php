<?php
/**
 * Author: Yaroslav Kovalev
 * Date: 30.04.2016
 * Time: 23:45
 */

namespace ZUser\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZUser\Options\ModuleOptions;

class ModuleOptionsFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        return new ModuleOptions(isset($config['zuser']) ? $config['zuser'] : []);

    }
}