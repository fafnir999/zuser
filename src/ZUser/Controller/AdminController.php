<?php
/**
 * Author: Yaroslav Kovalev
 * Date: 05.05.2016
 * Time: 16:52
 */

namespace ZUser\Controller;

use Zend\Json\Server\Response;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\I18n\Translator\TranslatorAwareTrait;
use Zend\I18n\Translator\TranslatorInterface;
use ZUser\Exception\FailAuthorizationException;
use ZUser\Exception\FailAccountServiceException;


class AdminController extends AbstractActionController
{
    use TranslatorAwareTrait;

    /** @var \DoctrineModule\Stdlib\Hydrator\DoctrineObject */
    private $_hydrator;

    /** @var \Doctrine\ORM\EntityManager */
    private $_entityManager;

    /** @var \ZUser\Options\ModuleOptions */
    private $_config;

    /** @var \ZUser\Model\Service\AccountService */
    private $_accountService;

    /**  @var \Zend\Form\Form */
    private $_accountForm;

    /**  @var \Zend\Form\Form */
    private $_registrationForm;

    /**  @var \Zend\Form\Form */
    private $_accountFilterForm;

    /**  @var \Zend\Form\Form */
    private $_accountNumPagesForm;


    /**
     * @param \Doctrine\ORM\EntityManager $_entityManager
     * @param \DoctrineModule\Stdlib\Hydrator\DoctrineObject $_hydrator
     * @param TranslatorInterface $translator
     * @param $config
     * @param $accountService
     * @param $accountForm
     * @param $registrationForm
     * @param $accountFilterForm
     * @param $accountNumPagesForm
     */
    public function __construct(
                                \Doctrine\ORM\EntityManager $_entityManager,
                                \DoctrineModule\Stdlib\Hydrator\DoctrineObject $_hydrator,
                                TranslatorInterface $translator,
                                $config,
                                $accountService,
                                $accountForm,
                                $registrationForm,
                                $accountFilterForm,
                                $accountNumPagesForm
    )
    {
        $this->_hydrator = $_hydrator;
        $this->_entityManager = $_entityManager;
        $this->setTranslator( $translator );
        $this->_config = $config;
        $this->_accountService = $accountService;
        $this->_accountForm = $accountForm;
        $this->_registrationForm = $registrationForm;
        $this->_accountFilterForm = $accountFilterForm;
        $this->_accountNumPagesForm = $accountNumPagesForm;
    }


    /**
     * Страница просмотра списка зарегистрированных пользователей
     *
     * @return array|\Zend\Http\Response|ViewModel
     */
    public function indexAction()
    {
        $queryParams = $this->params()->fromQuery();

        $accountFilterForm = $this->getAccountFilterForm();
        $accountNumPagesForm = $this->getAccountNumPagesForm();

        $accountFilterForm->setData($queryParams);
        $accountNumPagesForm->setData($queryParams);

        $currentPage = (int)$this->params()->fromQuery('page', null);
        $itemsOnPage = (int)$this->params()->fromQuery('numPages', 5);
        $sortParam = (string)$this->params()->fromQuery('sort', '');

        $filterFormData = null;
        if ($accountFilterForm->isValid()) {
            $filterFormData = $accountFilterForm->getData();
        }
        $paginator = $this->getAccountService()->getPaginationForAccount($currentPage, $itemsOnPage, $sortParam, $filterFormData);
        $columns = $this->getAccountService()->getIndexTableColumns($queryParams);

        return new ViewModel(compact('paginator', 'columns', 'queryParams', 'accountFilterForm', 'accountNumPagesForm'));
    }

    /*
     * Страница редактирования аккаунта пользователя
     */
    public function editAccountAction()
    {
        $editAccountId = (int)$this->params()->fromQuery('id', null);
        /** @var \ZUser\Entity\Account $account */
        if(is_null($editAccountId) ||
            !($account = $this->getEntityManager()->getRepository($this->getConfig()->getAccountEntityClass())->findOneBy(['id' => $editAccountId]))) {
            return $this->notFoundAction();
        }
        /** @var \Zend\Http\PhpEnvironment\Request $request */
        $request = $this->getRequest();
        /** @var \ZUser\Model\Form\AccountForm $form */
        $form = $this->getAccountForm();

        $form->setData($this->getHydrator()->extract($account));

        if ($request->isPost()) {
            $form->setData($request->getPost()->toArray());

            if ($form->isValid()) {
                try {
                    $this->getAccountService()->setAccount($account);
                    $this->getAccountService()->generateHashes($form->getData()['password']);
                    $this->getAccountService()->updateAccount();
                    $this->flashMessenger()->addMessage($this->getTranslator()->translate('Account update successfully'), 'success');
                }
                catch(FailAuthorizationException $e) {
                    $this->flashMessenger()->addMessage($e->getMessage(), 'error');
                }
                return $this->redirect()->toRoute(null,[],['query'=>['id' => $editAccountId]]);
            }
        }

        /** @var \Zend\View\Model\ViewModel $view */
        return new ViewModel(['form' => $form, 'id' => $editAccountId]);
    }

    /**
     * Страница редактирования профиля пользователя
     *
     * @return array|\Zend\Http\Response|ViewModel
     */
    public function editProfileAction()
    {
        $editAccountId = (int)$this->params()->fromQuery('id', null);

        /** @var \ZUser\Entity\Account $account */
        if(is_null($editAccountId) ||
            !($account = $this->getEntityManager()->getRepository($this->getConfig()->getAccountEntityClass())->findOneBy(['id' => $editAccountId]))) {
            return $this->notFoundAction();
        }

        /** @var \Zend\Http\PhpEnvironment\Request $request */
        $request = $this->getRequest();

        $userProfile = $account->getProfile();

        /** @var \ZUser\Model\Form\AccountForm $form */
        list($form, $viewPath) = $this->getAccountService()->getUserProfileRenderParams($userProfile);

        $form->setData($this->getHydrator()->extract($userProfile));

        if ($request->isPost()) {
            $form->setData($request->getPost()->toArray());

            if ($form->isValid()) {
                try {
                    $this->getHydrator()->hydrate($form->getData(), $userProfile);
                    $this->getAccountService()->updateProfile($userProfile);
                    $this->flashMessenger()->addMessage($this->getTranslator()->translate('Profile update successfully'), 'success');
                }
                catch(FailAccountServiceException $e) {
                    $this->flashMessenger()->addMessage($e->getMessage(), 'error');
                }
                return $this->redirect()->toRoute(null,[],['query'=>['id' => $editAccountId]]);
            }
        }

        return  new ViewModel(['form' => $form, 'profileView' => $viewPath, 'id' => $editAccountId]);
    }


    /**
     * Страница создания пользователя
     *
     * @return \Zend\Http\Response|ViewModel
     */
    public function createAction()
    {
        /** @var \Zend\View\Model\ViewModel $view */
        $view = new ViewModel();
        /** @var \Zend\Http\PhpEnvironment\Request $request */
        $request = $this->getRequest();
        /** @var \ZUser\Model\Form\RegistrationForm $form */
        $form = $this->getRegistrationForm();

        if ($request->isPost()) {
            $form->setData($request->getPost()->toArray());

            if ($form->isValid()) {
                try {
                    $this->getAccountService()->createAccount($form->getData(), false);
                    $this->flashMessenger()->addMessage($this->getTranslator()->translate('Account create successfully'), 'success');
                    return $this->redirect()->toRoute('account/admin/users');
                } catch (FailAuthorizationException $e) {
                    $this->flashMessenger()->addMessage($e->getMessage(), 'error');
                    return $this->redirect()->refresh();
                }
            }
        }

        $view->setTemplate('z-user/account/registration');
        $view->setVariable('form', $form);
        return $view;
    }

    /**
     * Экшен для изменения какого-либо параметра аккаунта пользователя (подтвержден, активирован и т. д.), а также удаления
     * аккаунта пользователя
     *
     * @return \Zend\Http\Response
     */
    public function changeAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $changeAccountId = (int)$this->params()->fromPost('id', null);
            $action = $this->params()->fromPost('action', null);
            try {
                if($action === 'delete') {
                    $this->getAccountService()->deleteAccount($changeAccountId);
                    $this->flashMessenger()->addMessage($this->getTranslator()->translate('User successfully delete'), 'success');
                } elseif($action === 'approve') {
                    $this->getAccountService()->approveAccount($changeAccountId);
                    $this->flashMessenger()->addMessage($this->getTranslator()->translate('Account successfully approved'), 'success');
                } elseif($action === 'enable') {
                    $this->getAccountService()->changeAccountStatus(true, $changeAccountId);
                    $this->flashMessenger()->addMessage($this->getTranslator()->translate('Account successfully enabled'), 'success');
                } elseif($action === 'disable') {
                    $this->getAccountService()->changeAccountStatus(false, $changeAccountId);
                    $this->flashMessenger()->addMessage($this->getTranslator()->translate('Account successfully disabled'), 'success');
                } else {
                    throw new \Exception($this->getTranslator()->translate('Wrong action'));
                }

            } catch(\Exception $e) {
                $this->flashMessenger()->addMessage($e->getMessage(), 'error');
            }

            if ($request->isAjax()) {
                return new JsonModel('success');
            }
        }

        return $this->redirect()->toRoute('account/admin');
    }


    //----------------------SETTERS AND GETTERS--------------------------
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
    public function getRegistrationForm()
    {
        return $this->_registrationForm;
    }

    /**
     * @return \Zend\Form\Form
     */
    public function getAccountFilterForm()
    {
        return $this->_accountFilterForm;
    }

    /**
     * @return \Zend\Form\Form
     */
    public function getAccountNumPagesForm()
    {
        return $this->_accountNumPagesForm;
    }

}