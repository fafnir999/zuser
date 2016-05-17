<?php
/**
 * Author: Yaroslav Kovalev
 * Date: 02.05.2016
 * Time: 12:32
 */

namespace ZUser\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class HydratorFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new DoctrineHydrator($serviceLocator->get('doctrine.entitymanager.orm_default'));
    }
}