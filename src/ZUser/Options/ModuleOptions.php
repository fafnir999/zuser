<?php
/**
 * Author: Yaroslav Kovalev
 * Date: 30.04.2016
 * Time: 23:32
 */

namespace ZUser\Options;

use Zend\Stdlib\AbstractOptions;

class ModuleOptions extends AbstractOptions
{
    protected $__strictMode__ = false;

    /**
     * @var string
     */
    protected $accountEntityClass = 'ZUser\Entity\Account';

    /**
     * @var string
     */
    protected $logFilePath = 'logs/customErrorLog.txt';

    /**
     * @var bool
     */
    protected $sendEmailWithApproveToken = true;

    /**
     * @var string
     */
    protected $approveEmailTemplate = 'z-user/mail-templates/approveAccount';

    /**
     * @var string
     */
    protected $recoveryEmailTemplate = 'z-user/mail-templates/recoveryPassword';

    /**
     * @var bool
     */
    protected $enableLoginInWithoutApprove = false;


    //---------------------------GETTERS AND SETTERS-----------------------------
    /**
     * @return boolean
     */
    public function isSendEmailWithApproveToken()
    {
        return $this->sendEmailWithApproveToken;
    }

    /**
     * @param boolean $sendEmailWithApproveToken
     */
    public function setSendEmailWithApproveToken($sendEmailWithApproveToken)
    {
        $this->sendEmailWithApproveToken = $sendEmailWithApproveToken;
    }

    /**
     * @return string
     */
    public function getLogFilePath()
    {
        return $this->logFilePath;
    }

    /**
     * @param $logFilePath
     * @return $this
     */
    public function setLogFilePath($logFilePath)
    {
        $this->logFilePath = $logFilePath;
        return $this;
    }

    /**
     * @return string
     */
    public function getAccountEntityClass()
    {
        return $this->accountEntityClass;
    }

    /**
     * @param $accountEntityClass
     * @return $this
     */
    public function setAccountEntityClass($accountEntityClass)
    {
        $this->accountEntityClass = $accountEntityClass;
        return $this;
    }

    /**
     * @return string
     */
    public function getApproveEmailTemplate()
    {
        return $this->approveEmailTemplate;
    }

    /**
     * @param string $approveEmailTemplate
     */
    public function setApproveEmailTemplate($approveEmailTemplate)
    {
        $this->approveEmailTemplate = $approveEmailTemplate;
    }

    /**
     * @return boolean
     */
    public function isEnableLoginInWithoutApprove()
    {
        return $this->enableLoginInWithoutApprove;
    }

    /**
     * @param boolean $enableLoginInWithoutApprove
     */
    public function setEnableLoginInWithoutApprove($enableLoginInWithoutApprove)
    {
        $this->enableLoginInWithoutApprove = $enableLoginInWithoutApprove;
    }

    /**
     * @return string
     */
    public function getRecoveryEmailTemplate()
    {
        return $this->recoveryEmailTemplate;
    }

    /**
     * @param string $recoveryEmailTemplate
     */
    public function setRecoveryEmailTemplate($recoveryEmailTemplate)
    {
        $this->recoveryEmailTemplate = $recoveryEmailTemplate;
    }
}