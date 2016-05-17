<?php
/**
 * Author: Yaroslav Kovalev
 * Date: 02.05.2016
 * Time: 10:22
 */

namespace ZUser\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use ZUser\Model\Form\RegistrationForm;

class RegistrationFormFactory implements  FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $registrationForm = new RegistrationForm(
            $serviceLocator->get('translator'),
            $serviceLocator->get('doctrine.entitymanager.orm_default'),
            $serviceLocator->get('zuserModuleOptions')
            );
        return $registrationForm;
    }
}