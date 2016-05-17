<?php
/**
 * Author: Yaroslav Kovalev
 * Date: 02.05.2016
 * Time: 10:20
 */

namespace ZUser\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use ZUser\Model\Form\AuthForm;

class AuthFormFactory implements  FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $authForm = new AuthForm($serviceLocator->get('translator'));
        return $authForm;
    }
}