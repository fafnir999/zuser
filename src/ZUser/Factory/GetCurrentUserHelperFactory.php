<?php
/**
 * Author: Yaroslav Kovalev
 * Date: 30.04.2016
 * Time: 23:22
 */

namespace ZUser\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZUser\Model\View\Helper\GetCurrentUser;

class GetCurrentUserHelperFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $getCurrentUser = new GetCurrentUser;
        $getCurrentUser->setAuthentication($serviceLocator->getServiceLocator()->get('authenticationService'));
        return $getCurrentUser;
    }
}