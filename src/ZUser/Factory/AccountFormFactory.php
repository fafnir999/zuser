<?php
/**
 * Author: Yaroslav Kovalev
 * Date: 01.05.2016
 * Time: 18:26
 */

namespace ZUser\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use ZUser\Model\Form\AccountForm;

class AccountFormFactory implements  FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $accountForm = new AccountForm($serviceLocator->get('translator'));
        return $accountForm;
    }
}