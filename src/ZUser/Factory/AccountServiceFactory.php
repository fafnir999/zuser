<?php
/**
 * Author: Yaroslav Kovalev
 * Date: 30.04.2016
 * Time: 23:15
 */

namespace ZUser\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use ZUser\Model\Service\AccountService;

class AccountServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $registrationService = new AccountService(
            $serviceLocator->get('zuserModuleOptions'),
            $serviceLocator->get('doctrine.entitymanager.orm_default'),
            $serviceLocator->get('translator'),
            $serviceLocator->get('errorLog'),
            $serviceLocator->get('acmailer.mailservice.default'),
            //TODO вынести в специфичное приложение
            $serviceLocator->get('listerProfileForm'),
            $serviceLocator->get('renterProfileForm'),

            $serviceLocator->get('zlibTableView'));
        return $registrationService;
    }
}