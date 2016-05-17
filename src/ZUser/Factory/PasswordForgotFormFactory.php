<?php
/**
 * Author: Yaroslav Kovalev
 * Date: 05.05.2016
 * Time: 10:18
 */

namespace ZUser\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use ZUser\Model\Form\PasswordForgotForm;

class PasswordForgotFormFactory implements  FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $registrationForm = new PasswordForgotForm(
            $serviceLocator->get('translator'),
            $serviceLocator->get('doctrine.entitymanager.orm_default'),
            $serviceLocator->get('zuserModuleOptions')
        );
        return $registrationForm;
    }
}