<?php
/**
 * Author: Yaroslav Kovalev
 * Date: 29.04.2016
 * Time: 9:23
 */

namespace ZUser\Model\View\Helper;

use Zend\View\Helper\AbstractHelper;

/**
 * Хелпер вида для определения, авторизован ли пользователь
 *
 * @package ZUser\Model\View\Helper
 */
class IsUserAuthorized extends AbstractHelper
{
    /** @var \ZUser\Model\Service\Authentication $authentication */
    protected $authentication;

    /**
     * @return \ZUser\Model\Service\Authentication $authentication
     */
    public function getAuthentication()
    {
        return $this->authentication;
    }

    /**
     * @param \ZUser\Model\Service\Authentication $authentication
     */
    public function setAuthentication($authentication)
    {
        $this->authentication = $authentication;
    }

    /**
     * Проверяет, авторизован ли пользователь
     *
     * @return bool
     */
    public function __invoke()
    {
        return $this->getAuthentication()->checkAuthorization();
    }
}