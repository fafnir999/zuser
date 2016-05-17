<?php
/**
 * Author: Yaroslav Kovalev
 * Date: 30.04.2016
 * Time: 21:48
 */

namespace ZUser\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZUser\Controller\AccountController;

class AccountControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $srv = $serviceLocator->getServiceLocator();
        $accountController = new AccountController(
            $srv->get('authenticationService'),
            $srv->get('accountService'),
            $srv->get('hydrator'),
            $srv->get('registrationForm'),
            $srv->get('authForm'),
            $srv->get('accountForm'),
            $srv->get('listerProfileForm'),
            $srv->get('renterProfileForm'),
            $srv->get('passwordForgotForm'),
            $srv->get('translator'),
            $srv->get('doctrine.entitymanager.orm_default'),
            $srv->get('zuserModuleOptions')
            );

        return $accountController;
    }
}