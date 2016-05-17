<?php
/**
 * Author: Yaroslav Kovalev
 * Date: 29.04.2016
 * Time: 10:37
 */

namespace ZUser\Model\View\Helper;

use Zend\View\Helper\AbstractHelper;

/**
 * Хелпер вида для получения аккаута текущего пользователя
 *
 * @package ZUser\Model\View\Helper
 */
class GetCurrentUser extends AbstractHelper
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
     * Возвращает аккаунт текущего зарегистрированного пользователя
     *
     * @return null|\ZUser\Entity\Account
     */
    public function __invoke()
    {
        if($this->getAuthentication()->checkAuthorization()) {
            return $this->getAuthentication()->getCurrentAccount();
        } else {
            return null;
        }
    }
}