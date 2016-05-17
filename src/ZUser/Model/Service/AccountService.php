<?php
/**
 * Author: Yaroslav Kovalev
 * Date: 29.04.2016
 * Time: 11:09
 */

namespace ZUser\Model\Service;

use ZLib\String\Random;

use ZUser\Entity\Account;
use ZUser\Entity\Profile;
use ZUser\Exception\FailAuthorizationException;
use ZUser\Options\ModuleOptions;
use ZUser\Exception\FailAccountServiceException;

use Zend\I18n\Translator\TranslatorAwareTrait;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\Paginator\Paginator as ZendPaginator;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;

class AccountService
{
    use TranslatorAwareTrait;

    /** @var \ZUser\Entity\Account */
    private $_account;

    /** @var \ZUser\Options\ModuleOptions */
    private $_config;

    /** @var \Doctrine\ORM\EntityManager */
    private $_entityManager;

    /** @var \Zend\Log\Logger */
    private $_errorLog;

    /** @var  \AcMailer\Service\MailServiceInterface $mailService */
    private $_mailService;

    /** @var \ZLib\FilterSorting\Model\Service\FilterSortingService */
    private $_filterSortingService;

    //TODO вынести в специфичный модуль для приложения
    private $_listerProfileForm;

    private $_renterProfileForm;

    /**
     * @param ModuleOptions $_config
     * @param $_entityManager
     * @param TranslatorInterface $translator
     * @param $errorLog
     * @param $_mailService
     * @param $filterSortingService
     */
    public function __construct(ModuleOptions $_config,
                                $_entityManager,
                                TranslatorInterface $translator,
                                $errorLog,
                                $_mailService,
                                $_listerProfileForm,
                                $_renterProfileForm,
                                $filterSortingService)
    {
        $this->_config = $_config;
        $this->_entityManager = $_entityManager;
        $this->_errorLog = $errorLog;
        $this->setTranslator( $translator );
        $this->_mailService = $_mailService;
        $this->_listerProfileForm = $_listerProfileForm;
        $this->_renterProfileForm = $_renterProfileForm;
        $this->_filterSortingService = $filterSortingService;
        //Устанавливаем названия колонок и параметры сортировки и фильтрации для таблицы аккаунтов в панели администрирования
        $this->_filterSortingService->setColumns(
            [
                'id' => ['name' => $this->getTranslator()->translate('ID'), 'filterType' => 'equal'],
                'email' => ['name' => $this->getTranslator()->translate('Email')],
                'date_created' => ['name' => $this->getTranslator()->translate('Register date')],
                'date_last_login' => ['name' => $this->getTranslator()->translate('Last login')],
                'approved' => ['name' => $this->getTranslator()->translate('Approved')],
                'enabled' => ['name' => $this->getTranslator()->translate('Status')],
                'profile.profile_type' => ['name' => $this->getTranslator()->translate('Account type')],
                'ip' => ['name' => $this->getTranslator()->translate('User Ip')],
                'delete' => ['name' => $this->getTranslator()->translate('Delete'), 'sorting' => false],
            ]
        );
    }


    /**
     * Создание нового аккаунта
     *
     * @param array $data Данные для создания аккаунта
     * @param null $sendApproveToken Посылать ли письмо с подтверждающим токеном
     * @return Account
     * @throws FailAuthorizationException
     */
    public function createAccount(array $data, $sendApproveToken = null)
    {
        //Если не передано значение параметра об отсылке письма с подтверждающим токеном, устанавливаем значение параметра из конфигурации модуля
        $sendApproveToken = is_null($sendApproveToken) ? $this->getConfig()->isSendEmailWithApproveToken() : $sendApproveToken;

        /** @var \DoctrineModule\Stdlib\Hydrator\DoctrineObject $hydrator */
        $entityClass = $this->getConfig()->getAccountEntityClass();

        $hydrator = new DoctrineHydrator($this->getEntityManager());

        $this->setAccount($hydrator->hydrate($data, new $entityClass()));

        $this->generateHashes($data['password'], Random::instance()->getString(16));

        $token = sha1(md5(Random::instance()->getString(32).$this->getAccount()->getSalt()));

        $this->getAccount()->setApproved(Account::ACCOUNT_NOT_APPROVED)
                ->setApproveToken($token)
                ->setApproveTokenExpires(new \DateTime(date('Y-m-d H:i:s', strtotime('+25 days'))));

        $this->getAccount()->setEnabled(Account::ACCOUNT_ENABLED);

        try {
            $this->getEntityManager()->persist($this->getAccount());
            $userProfile = $this->createProfile($data);
            $userProfile->setAccount($this->getAccount());
            $userProfile->setSubscriberEmail($this->getAccount()->getEmail());
            $this->getEntityManager()->persist($userProfile);
            if($sendApproveToken) {
                $this->sendApproveEmail();
            }
            $this->getEntityManager()->flush();
        } catch(\Exception $e) {
            $this->getErrorLog()->err('Fail to create account. File: '.__FILE__.'; Line: '.__LINE__.'; Message: '.$e->getMessage());
            throw new FailAuthorizationException($this->getTranslator()->translate("Registration is fail. Please contact the site administrator"));
        }

        return $this->getAccount();
    }


    /**
     * Обновление существующего аккаунта
     *
     * @return Account
     * @throws FailAuthorizationException
     */
    public function updateAccount()
    {
        try {
            $this->getEntityManager()->persist($this->getAccount());
            $this->getEntityManager()->flush();
        } catch(\Exception $e) {
            $this->getErrorLog()->err('Fail to update account. File: '.__FILE__.'; Line: '.__LINE__.'; Message: '.$e->getMessage());
            throw new FailAuthorizationException($this->getTranslator()->translate("Account update is fail. Please contact the site administrator"));
        }

        return $this->getAccount();
    }


    /**
     * Метод для генерации хешей аккаунта
     *
     * @param $password
     * @param null $salt
     * @return Account
     */
    public function generateHashes($password, $salt = null)
    {
        if(is_null($salt)) {
            $salt = Random::instance()->getString(16);
        }

        $password = md5($password);

        $this->getAccount()->setSalt($salt);
        $this->getAccount()->setPassword(sha1(md5($this->getAccount()->getEmail().$salt).$password.$salt));
        $this->getAccount()->setHash(sha1($salt.$this->getAccount()->getPassword()));

        return $this->getAccount();
    }


    /**
     * Посылает email с токеном для восстановаления забытого пароля
     *
     * @param string $email Email, на который надо послать ссылку на смену пароля
     * @throws FailAuthorizationException
     */
    public function sendRecoveryEmail($email)
    {
        $account = $this->getEntityManager()->getRepository($this->getConfig()->getAccountEntityClass())->findOneBy(['email' => $email]);

        $this->setAccount($account);
        $this->createRecoveryToken();

        $mailService = $this->getMailService();
        $mailService->setTemplate($this->getConfig()->getRecoveryEmailTemplate(), ['token' =>  $this->getAccount()->getRecoveryToken()]);

        $message = $mailService->getMessage();
        $message->setSubject('Your recovery token')
            ->addTo($this->getAccount()->getEmail());

        $result = $mailService->send();

        if (!$result->isValid()) {
            $errorMessage = $result->hasException() ? $result->getException()->getTraceAsString() : $result->getMessage();
            $this->getErrorLog()->err('Fail to send recovery email. File: '.__FILE__.'; Line: '.__LINE__.'; Message: '.$errorMessage);
            throw new FailAuthorizationException($this->getTranslator()->translate("Registration is fail. Please contact the site administrator"));
        }
    }


    /**
     * Посылает письмо с токеном для подтверждения email
     *
     * @throws FailAuthorizationException
     */
    public function sendApproveEmail()
    {
        /** @var  \AcMailer\Service\MailServiceInterface $mailService */
        $mailService = $this->getMailService();
        $mailService->setTemplate($this->getConfig()->getApproveEmailTemplate(), ['token' =>  $this->getAccount()->getApproveToken()]);

        $message = $mailService->getMessage();
        $message->setSubject('Approve your email')
            ->addTo($this->getAccount()->getEmail());

        $result = $mailService->send();

        if (!$result->isValid()) {
            $errorMessage = $result->hasException() ? $result->getException()->getTraceAsString() : $result->getMessage();
            $this->getErrorLog()->err('Fail to send approve email. File: '.__FILE__.'; Line: '.__LINE__.'; Message: '.$errorMessage);
            throw new FailAuthorizationException($this->getTranslator()->translate("Registration is fail. Please contact the site administrator"));
        }
    }

    /**
     * TODO вынести в специфичное приложение
     * Создание профиля пользователя
     *
     * @param array $data
     * @return Profile
     * @throws FailAuthorizationException
     */
    protected function createProfile(array $data)
    {
        $userProfile = new Profile();
        if($data['account_type'] === Profile::ACCOUNT_TYPE_LISTER) {
            $userProfile->setProfileType(Profile::ACCOUNT_TYPE_LISTER);
            $userProfile->setListerCondition('I am a lister');
        } elseif($data['account_type'] === Profile::ACCOUNT_TYPE_RENTER) {
            $userProfile->setProfileType(Profile::ACCOUNT_TYPE_RENTER);
            $userProfile->setRenterCondition('I am a renter');
        } else {
            $this->getErrorLog()->err('Fail to create account. File: '.__FILE__.'; Line: '.__LINE__.'; Message: Unknown profile type ');
            throw new FailAuthorizationException($this->getTranslator()->translate("Registration is fail. Please contact the site administrator"));
        }

        return $userProfile;
    }

    /**
     * Создает и сохраняет в установленном аккаунте токен для восстановления пароля
     */
    protected function createRecoveryToken()
    {
        $token = sha1(md5(Random::instance()->getString(32).$this->getAccount()->getSalt()));

        $this->getAccount()->setRecoveryToken($token);
        $this->getAccount()->setRecoveryTokenExpires(new \DateTime(date('Y-m-d H:i:s', strtotime('+25 minutes'))));

        $this->getEntityManager()->persist($this->getAccount());
        $this->getEntityManager()->flush();
    }

    /**
     * Создает объект пагинации для аккаунтов пользователей, вместе с сортировкой и фильтрацией в зависимости от переданных
     * параметров
     *
     * @param int $currentPage Текущая просматриваемая страница
     * @param int $itemsOnPage Количество элементов на странице
     * @param string $sortParams Параметр, по которому следует отсортировать таблицу. Назваться должен также, как
     * соответствующий столбец в базе данных
     * @param array $formData Данные из формы для фильтрации
     * @return ZendPaginator
     */
    public function getPaginationForAccount($currentPage, $itemsOnPage, $sortParams, array $formData)
    {
        /** @var $repository \ZUser\Entity\Repository\AccountRepository */
        $entityClass = $this->getConfig()->getAccountEntityClass();

        $repository = $this->getEntityManager()->getRepository($entityClass);

        $paginationQueryBuilder = $repository->getAccountsLimitQueryBuilder(($currentPage - 1) * $itemsOnPage, $itemsOnPage);
        $paginationQueryBuilder = $this->getFilterSortingService()->addSortForQueryBuilder($paginationQueryBuilder, $sortParams, $entityClass);
        if(!is_null($formData)) {
            $paginationQueryBuilder = $this->getFilterSortingService()->addFilterForQueryBuilder($paginationQueryBuilder, $formData);
        }

        $paginator = new ZendPaginator(new DoctrineAdapter(new DoctrinePaginator($paginationQueryBuilder->getQuery())));
        $paginator->setDefaultItemCountPerPage($itemsOnPage);
        $paginator->setCurrentPageNumber($currentPage);

        return $paginator;
    }

    /**
     * Возвращает массив названий столбцов таблицы с добавленными параметрами сортировки
     *
     * @param array $queryParams Массив параметров из get запроса. Нужен для формирования сортировочных ссылок
     * @return array
     */
    public function getIndexTableColumns($queryParams)
    {
        return $this->getFilterSortingService()->createSortColumnsLinks($queryParams);
    }

    /**
     * Удаление аккаунта
     *
     * @param $accountId
     * @throws FailAccountServiceException
     */
    public function deleteAccount($accountId)
    {
        try{
            $repository = $this->getEntityManager()->getRepository($this->getConfig()->getAccountEntityClass());
            $deleteAccount = $repository->findOneBy(['id' => $accountId]);
            $this->getEntityManager()->remove($deleteAccount);
            $this->getEntityManager()->flush();
        } catch(\Exception $e) {
            $this->getErrorLog()->err('Fail to delete account. File: '.__FILE__.'; Line: '.__LINE__.'; Message: '.$e->getMessage());
            throw new FailAccountServiceException($this->getTranslator()->translate("Unable to delete account"));
        }
    }


    /**
     * Подтверждение аккаунта
     *
     * @param null $accountId
     * @return Account
     * @throws FailAuthorizationException
     */
    public function approveAccount($accountId = null)
    {
        if(isset($accountId)) {
            $this->setAccount($this->findAccountById($accountId));
        }

        $this->getAccount()->setApproved(Account::ACCOUNT_APPROVED);
        $this->getAccount()->setApproveToken(null);
        $this->getAccount()->setApproveTokenExpires(null);

        return $this->updateAccount();
    }

    /**
     * Изменение флага enabled для аккаунта
     *
     * @param bool $enableCondition Если true, меняется на true, если false то на false
     * @param null|int $accountId
     * @return Account
     * @throws FailAuthorizationException
     */
    public function changeAccountStatus($enableCondition, $accountId = null)
    {
        if(isset($accountId)) {
            $this->setAccount($this->findAccountById($accountId));
        }

        $this->getAccount()->setEnabled((bool)$enableCondition);

        return $this->updateAccount();
    }

    /**
     * Находим аккаунт по его id
     *
     * @param $id
     * @return null|Account
     */
    protected function findAccountById($id)
    {
        $repository = $this->getEntityManager()->getRepository($this->getConfig()->getAccountEntityClass());
        return $repository->findOneBy(['id' => $id]);
    }

    /**
     * Обновление профиля пользователя
     *
     * @param Profile $profile
     * @throws FailAccountServiceException
     */
    public function updateProfile(Profile $profile)
    {
        try {
            $this->getEntityManager()->persist($profile);
            $this->getEntityManager()->flush();
        } catch(\Exception $e) {
            $this->getErrorLog()->err('Fail to update profile. File: '.__FILE__.'; Line: '.__LINE__.'; Message: '.$e->getMessage());
            throw new FailAccountServiceException($this->getTranslator()->translate("Profile update is fail. Please contact the site administrator"));
        }
    }

    public function getUserProfileRenderParams(Profile $userProfile)
    {
        //TODO Вынести в специфичный модуль пользователя для данного приложения
        $form = $viewPath = null;
        if($userProfile->getProfileType() === Profile::ACCOUNT_TYPE_LISTER) {
            $form = $this->_listerProfileForm;
            $viewPath = 'z-user/account/listerProfile';
        } elseif($userProfile->getProfileType() === Profile::ACCOUNT_TYPE_RENTER) {
            $form = $this->_renterProfileForm;
            $viewPath = 'z-user/account/renterProfile';
        }
        return [$form, $viewPath];
    }

    //----------------------------------------SETTERS AND GETTERS-------------------

    /**
     * @return \ZUser\Entity\Account
     */
    public function getAccount()
    {
        return $this->_account;
    }

    /**
     * @param \ZUser\Entity\Account | object $account
     */
    public function setAccount($account)
    {
        $this->_account = $account;
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->_entityManager;
    }

    /**
     * @return \Zend\Log\Logger
     */
    public function getErrorLog()
    {
        return $this->_errorLog;
    }

    /**
     * @return ModuleOptions
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * @return \AcMailer\Service\MailServiceInterface
     */
    public function getMailService()
    {
        return $this->_mailService;
    }

    /**
     * @return \ZLib\FilterSorting\Model\Service\FilterSortingService
     */
    public function getFilterSortingService()
    {
        return $this->_filterSortingService;
    }
}