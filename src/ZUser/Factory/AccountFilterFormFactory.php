<?php
/**
 * Author: Yaroslav Kovalev
 * Date: 11.05.2016
 * Time: 11:09
 */

namespace ZUser\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use ZUser\Model\Form\AccountFilterForm;

class AccountFilterFormFactory implements  FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $accountFilterForm = new AccountFilterForm($serviceLocator->get('translator'));
        return $accountFilterForm;
    }
}