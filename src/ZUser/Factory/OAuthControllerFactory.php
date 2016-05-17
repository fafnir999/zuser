<?php
/**
 * Author: Yaroslav Kovalev
 * Date: 05.05.2016
 * Time: 14:09
 */

namespace ZUser\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZUser\Controller\OAuthController;

class OAuthControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $srv = $serviceLocator->getServiceLocator();
        $oauthController = new OAuthController($srv );
        return $oauthController;
    }
}