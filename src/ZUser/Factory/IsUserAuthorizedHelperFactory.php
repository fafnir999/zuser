<?php
/**
 * Author: Yaroslav Kovalev
 * Date: 30.04.2016
 * Time: 23:21
 */

namespace ZUser\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZUser\Model\View\Helper\IsUserAuthorized;

class IsUserAuthorizedHelperFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $isUserAuthorizedHelper = new IsUserAuthorized;
        $isUserAuthorizedHelper->setAuthentication($serviceLocator->getServiceLocator()->get('authenticationService'));
        return $isUserAuthorizedHelper;
    }
}