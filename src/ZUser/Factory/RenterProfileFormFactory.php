<?php
/**
 * Author: Yaroslav Kovalev
 * Date: 03.05.2016
 * Time: 13:24
 */

namespace ZUser\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use ZUser\Model\Form\RenterProfileForm;

class RenterProfileFormFactory implements  FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $profileForm = new RenterProfileForm(
            $serviceLocator->get('translator'),
            $serviceLocator->get('doctrine.entitymanager.orm_default')
        );
        return $profileForm;
    }
}