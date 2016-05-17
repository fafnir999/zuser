<?php
/**
 * Author: Yaroslav Kovalev
 * Date: 02.05.2016
 * Time: 13:40
 */

namespace ZUser\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Log\Writer\Stream;
use Zend\Log\Logger;

class ErrorLogFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $options = $serviceLocator->get('zuserModuleOptions');
        $logFilePath = $options->getLogFilePath();

        $writer = new Stream($logFilePath);
        $logger = new Logger();
        $logger->addWriter($writer);

        return $logger;
    }
}