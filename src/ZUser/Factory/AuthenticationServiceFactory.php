<?php
/**
 * Author: Yaroslav Kovalev
 * Date: 30.04.2016
 * Time: 23:10
 */

namespace ZUser\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use ZUser\Model\Service\Authentication;

class AuthenticationServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $authService = new Authentication(
            $serviceLocator->get('zuserModuleOptions'),
            $serviceLocator->get('doctrine.entitymanager.orm_default'),
            $serviceLocator->get('translator'),
            $serviceLocator->get('errorLog'));
        return $authService;
    }
}