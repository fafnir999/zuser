<?php
namespace ZUser\Model\Service;

// Use block for ZF2
use Zend\Session\Container;

use ZUser\Entity\Account;

use ZUser\Exception\FailAuthorizationException;
use ZUser\Options\ModuleOptions;
use Zend\I18n\Translator\TranslatorAwareTrait;
use Zend\I18n\Translator\TranslatorInterface;

class Authentication
{
    use TranslatorAwareTrait;
    /**
     * @var \ZUser\Options\ModuleOptions
     */
    private $_config;

    /** @var \Doctrine\ORM\EntityManager */
    private $_entityManager;

    /** @var \Zend\Log\Logger */
    private $_errorLog;

    /**
     * @param ModuleOptions $_config
     * @param $_entityManager
     * @param TranslatorInterface $translator
     */
    public function __construct(ModuleOptions $_config,
                                $_entityManager,
                                TranslatorInterface $translator,
                                $errorLog
    )
    {
        $this->_config = $_config;
        $this->_entityManager = $_entityManager;
        $this->_errorLog = $errorLog;
        $this->setTranslator( $translator );
    }

    /**
     * @access public
     *
     * This method is necessary to check authorized account.
     *
     * @return bool
     */
    public function checkAuthorization()
    {
        $account_info = null;

        /** @var \Zend\Session\Container $authorization */
        $authorization = new Container('frontend_authorization');

        if ($authorization->offsetExists('account')) {
            $account_info = $authorization->account;
        }

        if (isset($_COOKIE['frontend_authorization']) && !empty($_COOKIE['frontend_authorization']) && is_null($account_info)) {
            $account_info = unserialize($_COOKIE['frontend_authorization']);
        }

        if (!is_null($account_info)) {
            if (!$account = $this->getEntityManager()->getRepository($this->getConfig()->getAccountEntityClass())->findOneBy(['hash' => $account_info['hash']])) {
                $this->logout();
                return false;
            }

            if ($account->getEnabled() != 1) {
                $this->logout();
                return false;
            }

            $password = $account_info['password'];
            $password_hash = $account_info['password_hashed'] === false ? sha1(md5($account->getEmail().$account->getSalt()).$password.$account->getSalt()) : $account_info['password'];

            if ($password_hash != $account->getPassword()) {
                $this->logout();
                return false;
            }

            return true;
        }

        return false;
    }


    /**
     * @access public
     *
     * This method is necessary to get current account
     *
     * @return Account
     */
    public function getCurrentAccount()
    {
        $account_info = null;

        /** @var \Zend\Session\Container $authorization */
        $authorization = new Container('frontend_authorization');
        if ($authorization->offsetExists('account')) {
            $account_info = $authorization->account;
        }

        if (isset($_COOKIE['frontend_authorization']) && !empty($_COOKIE['frontend_authorization']) && is_null($account_info)) {
            $account_info = unserialize($_COOKIE['frontend_authorization']);
        }

        /** @var \ZUser\Entity\Account $account */
        $account = $this->getEntityManager()->getRepository($this->getConfig()->getAccountEntityClass())->findOneByHash(['hash' => $account_info['hash']]);
        return $account;
    }


    /**
     * Авторизация аккаунта
     *
     * @param $data
     * @param bool|false $ajax
     * @param bool|false $password_hashed
     * @return bool
     * @throws FailAuthorizationException
     */
    public function doAuthorization($data, $ajax = false, $password_hashed = false)
    {
        if (!$account = $this->getEntityManager()->getRepository($this->getConfig()->getAccountEntityClass())->findOneBy(['email' => $data['email']])) {
            if (!$ajax) {
               throw new FailAuthorizationException($this->getTranslator()->translate("User with this registration email does not exist"));
            }
        }

        if ($account->getEnabled() != 1) {
            if (!$ajax) {
                throw new FailAuthorizationException($this->getTranslator()->translate("This account is inactive"));
            }
            return false;
        }

        if (!$this->getConfig()->isEnableLoginInWithoutApprove() && $account->getApproved() != 1) {
            if (!$ajax) {
                throw new FailAuthorizationException($this->getTranslator()->translate("You need to approve your account before enter"));
            }
            return false;
        }

        $password =  md5($data['password']);
        $password_hash = $password_hashed === false ? sha1(md5($account->getEmail().$account->getSalt()).$password.$account->getSalt()) : $data['password'];

        if ($password_hash != $account->getPassword()) {
            if (!$ajax) {
                throw new FailAuthorizationException($this->getTranslator()->translate("Wrong password"));
            }

            return false;
        }

        $account->setDateLastLogin(new \DateTime(date('Y-m-d H:i:s')));
        $this->getEntityManager()->persist($account);
        $this->getEntityManager()->flush();

        $account = [
            'hash' => $account->getHash(),
            'password' => $password_hashed === false ? $password : $password_hash,
            'password_hashed' => $password_hashed
        ];

        /** @var \Zend\Session\Container $authorization */
        $authorization = new Container('frontend_authorization');
        $authorization->account = $account;

        if (isset($data['remember_me']) && $data['remember_me'] == true) {
            setcookie('frontend_authorization', serialize($account), time() + (60*60*24*30), '/');
        }


        return true;
    }


    /**
     * @access public
     *
     * This method is necessary to do logout
     *
     * @return Authentication
     */
    public function logout()
    {
        /** @var \Zend\Session\Container $authentication */
        $authentication = new Container('frontend_authorization');
        setcookie('frontend_authorization', '',  time() - 3600, '/');
        $authentication->getManager()->getStorage()->clear('frontend_authorization');

        return $this;
    }


    //------------------------GETTERS AND SETTERS---------------------------------
    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->_entityManager;
    }

    /**
     * @return ModuleOptions
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * @return \Zend\Log\Logger
     */
    public function getErrorLog()
    {
        return $this->_errorLog;
    }
}