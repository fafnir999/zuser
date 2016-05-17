<?php
/**
 * Author: Yaroslav Kovalev
 * Date: 06.05.2016
 * Time: 10:29
 */

namespace ZUser\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZUser\Controller\AdminController;

class AdminControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $srv = $serviceLocator->getServiceLocator();
        $adminController = new AdminController(
            $srv->get('doctrine.entitymanager.orm_default'),
            $srv->get('hydrator'),
            $srv->get('translator'),
            $srv->get('zuserModuleOptions'),
            $srv->get('accountService'),
            $srv->get('accountForm'),
            $srv->get('registrationForm'),
            $srv->get('accountFilterForm'),
            $srv->get('accountNumPagesForm')
        );

        return $adminController;
    }
}