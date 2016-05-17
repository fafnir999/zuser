<?php
namespace ZUser\Controller;

use Zend\View\Model\ViewModel;

use Zend\Mvc\Controller\AbstractActionController;
use ZUser\Exception\FailAuthorizationException;
use Zend\I18n\Translator\TranslatorAwareTrait;
use Zend\I18n\Translator\TranslatorInterface;

/**
 * Контроллер для обработки frontend действий, связанных с пользователями
 *
 * @package ZUser\Controller
 */
class AccountController extends AbstractActionController
{
    use TranslatorAwareTrait;

    /** @var \ZUser\Model\Service\Authentication */
    private $_authenticationService;

    /** @var \ZUser\Model\Service\AccountService */
    private $_accountService;

    /** @var \DoctrineModule\Stdlib\Hydrator\DoctrineObject */
    private $_hydrator;

    /** @var \Doctrine\ORM\EntityManager */
    private $_entityManager;

    /**  @var \Zend\Form\Form */
    private $_registrationForm;

    /**  @var \Zend\Form\Form */
    private $_authForm;

    /**  @var \Zend\Form\Form */
    private $_accountForm;

    //TODO вынести в специфичное приложение
    /**  @var \Zend\Form\Form */
    private $_listerProfileForm;

    /**  @var \Zend\Form\Form */
    private $_renterProfileForm;

    /**  @var \Zend\Form\Form */
    private $_passwordForgotForm;

    /** @var \ZUser\Options\ModuleOptions */
    private $_config;


    /**
     * @param $_authenticationService
     * @param $_accountService
     * @param $_hydrator
     * @param $_registrationForm
     * @param $_authForm
     * @param $_accountForm
     * @param $_listerProfileForm
     * @param $_renterProfileForm
     * @param $_passwordForgotForm
     * @param TranslatorInterface $translator
     * @param $_entityManager
     * @param $_config
     */
    public function __construct($_authenticationService,
                                $_accountService,
                                $_hydrator,
                                $_registrationForm,
                                $_authForm,
                                $_accountForm,
                                $_listerProfileForm,
                                $_renterProfileForm,
                                $_passwordForgotForm,
                                TranslatorInterface $translator,
                                $_entityManager,
                                $_config)
    {
        $this->_authenticationService = $_authenticationService;
        $this->_accountService = $_accountService;
        $this->_hydrator = $_hydrator;

        $this->_registrationForm = $_registrationForm;
        $this->_authForm = $_authForm;
        $this->_accountForm = $_accountForm;
        $this->_listerProfileForm = $_listerProfileForm;
        $this->_renterProfileForm = $_renterProfileForm;
        $this->_passwordForgotForm = $_passwordForgotForm;

        $this->setTranslator( $translator );

        $this->_entityManager = $_entityManager;
        $this->_config = $_config;
    }

    /**
     * Регистрация пользователя
     *
     * @return \Zend\Http\Response|ViewModel
     */
    public function registrationAction()
    {
        if ($this->getAuthenticationService()->checkAuthorization() === true) {
            return $this->redirect()->toRoute('home');
        }

        /** @var \Zend\Http\PhpEnvironment\Request $request */
        $request = $this->getRequest();
        $form = $this->getRegistrationForm();

        if ($request->isPost()) {
            $form->setData($request->getPost()->toArray());

            if ($form->isValid()) {
                try {
                    $this->getAccountService()->createAccount($form->getData());
                    if($this->getConfig()->isEnableLoginInWithoutApprove()) {
                        $this->getAuthenticationService()->doAuthorization($form->getData());
                    } else {
                        $this->flashMessenger()->addMessage($this->getTranslator()->translate('We send to you email approve message'), 'success');
                    }
                    return $this->redirect()->toRoute('home');
                } catch (FailAuthorizationException $e) {
                    $this->flashMessenger()->addMessage($e->getMessage(), 'error');
                    return $this->redirect()->refresh();
                }
            }
        }

        return new ViewModel(compact('form'));
    }


    /**
     * Страница с авторизацией пользователя
     *
     * @return \Zend\Http\Response|ViewModel
     */
    public function authAction()
    {
        if ($this->getAuthenticationService()->checkAuthorization() === true) {
            return $this->redirect()->toRoute('home');
        }

        $form = $this->getAuthForm();
        /** @var \Zend\Http\PhpEnvironment\Request $request */
        $request = $this->getRequest();

        if ($request->isPost()) {
            $form->setData($request->getPost()->toArray());

            if ($form->isValid()) {
                try {
                    $this->getAuthenticationService()->doAuthorization($form->getData());
                    return $this->redirect()->toRoute('home');
                }
                catch(FailAuthorizationException $e) {
                    $this->flashMessenger()->addMessage($e->getMessage(), 'error');
                    return $this->redirect()->refresh();
                }
            }
        }

        return new ViewModel(compact('form'));
    }

    /**
     * Страница просмотра и редактирования аккаунта
     *
     * @return \Zend\Http\Response|ViewModel
     */
    public function accountAction()
    {
        if($this->getAuthenticationService()->checkAuthorization() === false) {
            return $this->redirect()->toRoute('home');
        }

        /** @var \Zend\Http\PhpEnvironment\Request $request */
        $request = $this->getRequest();
        $account = $this->getAuthenticationService()->getCurrentAccount();
        $form = $this->getAccountForm();

        $form->setData($this->getHydrator()->extract($account));

        if ($request->isPost()) {
            $form->setData($request->getPost()->toArray());

            if ($form->isValid()) {
                try {
                    $this->getAccountService()->setAccount($account);
                    $this->getAccountService()->generateHashes($form->getData()['password']);
                    $account = $this->getAccountService()->updateAccount();
                    $this->getAuthenticationService()->doAuthorization([
                        'email' => $account->getEmail(),
                        'password' => $form->getData()['password'],
                    ]);
                    $this->flashMessenger()->addMessage($this->getTranslator()->translate('Account update successfully'), 'success');
                }
                catch(FailAuthorizationException $e) {
                    $this->flashMessenger()->addMessage($e->getMessage(), 'error');
                }
                return $this->redirect()->refresh();
            }
        }

        return new ViewModel(compact('form'));
    }

    /**
     * Страница просмотра и редактирования профиля
     *
     * @return \Zend\Http\Response|ViewModel
     */
    public function profileAction()
    {
        if($this->getAuthenticationService()->checkAuthorization() === false) {
            return $this->redirect()->toRoute('home');
        }

        /** @var \ZUser\Entity\Account $account */
        $account = $this->getAuthenticationService()->getCurrentAccount();
        /** @var \Zend\View\Model\ViewModel $view */
        $view = new ViewModel();
        /** @var \Zend\Http\PhpEnvironment\Request $request */
        $request = $this->getRequest();

        $userProfile = $account->getProfile();

        /** @var \ZUser\Model\Form\AccountForm $form */
        list($form, $viewPath) = $this->getAccountService()->getUserProfileRenderParams($userProfile);
        $view->setTemplate($viewPath);

        $form->setData($this->getHydrator()->extract($userProfile));

        if ($request->isPost()) {
            $form->setData($request->getPost()->toArray());

            if ($form->isValid()) {
                try {
                    $this->getHydrator()->hydrate($form->getData(), $userProfile);
                    $this->getAccountService()->updateProfile($userProfile);
                    $this->flashMessenger()->addMessage($this->getTranslator()->translate('Profile update successfully'), 'success');
                }
                catch(FailAuthorizationException $e) {
                    $this->flashMessenger()->addMessage($e->getMessage(), 'error');
                }
                return $this->redirect()->refresh();
            }
        }

        $view->setVariable('form', $form);
        return $view;
    }


    /**
     * Страница для подтверждения аккаунта
     *
     * @return array|\Zend\Http\Response
     */
    public function approveAction()
    {
        $token = $this->params()->fromRoute('token');

        /** @var \ZUser\Entity\Account $account */
        $account = $this->getEntityManager()->getRepository($this->getConfig()->getAccountEntityClass())->findOneBy(['approve_token' => $token]);

        if (!$account || new \DateTime(date('Y-m-d H:i:s')) > $account->getApproveTokenExpires()) {
            return $this->notFoundAction();
        }

        $this->getAccountService()->setAccount($account);
        $this->getAccountService()->approveAccount();

        try {
            $account = $this->getAccountService()->updateAccount();
            $this->getAuthenticationService()->doAuthorization([
                'email' => $account->getEmail(),
                'password' => $account->getPassword(),
        ], false, true);
        } catch(FailAuthorizationException $e) {
            $this->flashMessenger()->addMessage($e->getMessage(), 'error');
        }

        return $this->redirect()->toRoute('home');

    }

    /**
     * Страница с формой ввода email аккаунта с забытым паролем
     *
     * @return \Zend\Http\Response|ViewModel
     */
    public function passwordForgotAction()
    {
        if ($this->getAuthenticationService()->checkAuthorization() === true) {
            return $this->redirect()->toRoute('home');
        }

        $form = $this->getPasswordForgotForm();
        /** @var \Zend\Http\PhpEnvironment\Request $request */
        $request = $this->getRequest();

        if ($request->isPost()) {
            $form->setData($request->getPost()->toArray());

            if ($form->isValid()) {
                try {
                    $this->getAccountService()->sendRecoveryEmail($form->getData()['email']);
                    $this->flashMessenger()->addMessage($this->getTranslator()->translate('We send you recovery email'), 'info');
                    return $this->redirect()->toRoute('home');
                }
                catch(FailAuthorizationException $e) {
                    $this->flashMessenger()->addMessage($e->getMessage(), 'error');
                    return $this->redirect()->refresh();
                }
            }
        }

        return new ViewModel(['form' => $form]);
    }


    /**
     * Страница с формой ввода нового пароля взамен забытого
     *
     * @return array|\Zend\Http\Response|ViewModel
     */
    public function passwordRecoveryAction()
    {
        if($this->getAuthenticationService()->checkAuthorization() === true) {
            return $this->redirect()->toRoute('home');
        }

        $token = $this->params()->fromRoute('token');

        /** @var \ZUser\Entity\Account $account */
        $account = $this->getEntityManager()->getRepository($this->getConfig()->getAccountEntityClass())->findOneBy(['recovery_token' => $token]);

        if (!$account || new \DateTime(date('Y-m-d H:i:s')) > $account->getRecoveryTokenExpires()) {
            return $this->notFoundAction();
        }

        $form = $this->getAccountForm();

        $form->setData($this->getHydrator()->extract($account));

        /** @var \Zend\Http\PhpEnvironment\Request $request */
        $request = $this->getRequest();

        if ($request->isPost()) {
            $form->setData($request->getPost()->toArray());

            if ($form->isValid()) {
                try {
                    $this->getAccountService()->setAccount($account);
                    $this->getAccountService()->generateHashes($form->getData()['password']);
                    $account->setRecoveryToken(null);
                    $account->setRecoveryTokenExpires(null);
                    $account = $this->getAccountService()->updateAccount();
                    $this->getAuthenticationService()->doAuthorization([
                        'email' => $account->getEmail(),
                        'password' => $form->getData()['password'],
                    ]);

                    $this->flashMessenger()->addMessage($this->getTranslator()->translate('Account update successfully'), 'success');
                }
                catch(FailAuthorizationException $e) {
                    $this->flashMessenger()->addMessage($e->getMessage(), 'error');
                }
                return $this->redirect()->refresh();
            }
        }

        /** @var \Zend\View\Model\ViewModel $view */
        $view = new ViewModel();
        $view->setTemplate('z-user/account/account');
        $view->setVariable('form', $form);
        return $view;
    }


    /**
     * Выход с сайта
     *
     * @return \Zend\Http\Response
     */
    public function logoutAction()
    {
        if ($this->getAuthenticationService()->checkAuthorization()) {
            $this->getAuthenticationService()->logout();
        }

        return $this->redirect()->toRoute('home');
    }

    //---------------GETTERS AND SETTERS------------------------------

    /**
     * @return \Zend\Form\Form
     */
    public function getRegistrationForm()
    {
        return $this->_registrationForm;
    }

    /**
     * @return \Zend\Form\Form
     */
    public function getAuthForm()
    {
        return $this->_authForm;
    }

    /**
     * @return \Zend\Form\Form
     */
    public function getAccountForm()
    {
        return $this->_accountForm;
    }

    /**
     * @return \Zend\Form\Form
     */
    public function getPasswordForgotForm()
    {
        return $this->_passwordForgotForm;
    }

    /**
     * @return \ZUser\Model\Service\Authentication
     */
    public function getAuthenticationService()
    {
        return $this->_authenticationService;
    }

    /**
     * @return \DoctrineModule\Stdlib\Hydrator\DoctrineObject
     */
    public function getHydrator()
    {
        return $this->_hydrator;
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->_entityManager;
    }

    /**
     * @return \Zend\Form\Form
     */
    public function getListerProfileForm()
    {
        return $this->_listerProfileForm;
    }

    /**
     * @return \Zend\Form\Form
     */
    public function getRenterProfileForm()
    {
        return $this->_renterProfileForm;
    }

    /**
     * @return \ZUser\Options\ModuleOptions
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * @return \ZUser\Model\Service\AccountService
     */
    public function getAccountService()
    {
        return $this->_accountService;
    }

}