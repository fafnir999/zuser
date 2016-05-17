<?php
namespace ZUser\Controller;

// Use block for ZF2
use Zend\Session\Container;
use Zend\Mvc\Controller\AbstractActionController;


class OAuthController extends AbstractActionController
{

    private $_serviceLocator;

    /** @var array Aliases for providers */
    private $__provider_alias = [
        'github' => 'Github',
        'google' => 'Google',
        'facebook' => 'Facebook',
        'linkedin' => 'LinkedIn',
    ];

    /**
     * OAuthController constructor.
     * @param $_serviceLocator
     */
    public function __construct($_serviceLocator)
    {
        $this->_serviceLocator = $_serviceLocator;
    }


    /**
     * Action is necessary for callbacks of OAuth2 APIs
     *
     * Здесь происходит обработка ответа от социальной сети и регистрация юзера
     */
    public function callbackAction()
    {
//        $provider = $this->params()->fromRoute('provider');
//        $adapter_name = implode('\\', ['ReverseOAuth2', $this->__provider_alias[$provider]]);
//
//        $client = $this->getServiceLocator()->get($adapter_name);
//
//        if (strlen($this->params()->fromQuery('code')) > 10) {
//            if($client->getToken($this->request)) {
//                $token = $client->getSessionToken(); // token in session
//            } else {
//                $token = $client->getError(); // last returned error (array)
//            }
//
//            $account_info = $client->getInfo();
//            if (!$account = $this->getEntityManager()->getRepository('Application\Entity\Account')->findOneBy(['provider_id' => $account_info->id, 'provider' => $provider])) {
//                $data = $this->convertProviderDataToAccount($account_info, $provider);
//
//                if (!$account = $this->getEntityManager()->getRepository('Application\Entity\Account')->findOneBy(['email' => $data['email']])) {
//                    /** @var \DoctrineModule\Stdlib\Hydrator\DoctrineObject $hydrator */
//                    $hydrator = $this->getHydratorFor('Application\Entity\Account');
//
//                    /** @var \Application\Entity\Account $account */
//                    $account = $hydrator->hydrate($data, new Account());
//                    $account->generateHashes($data['provider_id'], Random::instance()->getString(16));
//
//                    /** @var \Application\Entity\AccountType $account_type */
//                    $account_type = $this->getEntityManager()->find('Application\Entity\AccountType', 'buyer');
//                    $account->setAccountType($account_type);
//
//                    $account->setApproved(true);
//                } else {
//                    // User with same email exists. Update his info
//                    $account->setProvider($data['provider']);
//                    $account->setProviderId($data['provider_id']);
//                }
//
//                try {
//                    $this->getEntityManager()->persist($account);
//                    $this->getEntityManager()->flush();
//
//                    /** @var \Application\Model\Account\Authentication $authentication */
//                    $authentication = $this->getServiceLocator()->get('Application\Model\Account\Authentication');
//
//                    $data = [
//                        'email' => $account->getEmail(),
//                        'password' => $account->getPassword(),
//                    ];
//
//                    if ($result = $authentication->doAuthorization($data, false, true)) {
//                        Alert::instance()->setData(Alert::SUCCESS, Locale::instance()->translate_formatted("ALERT_ACCOUNT_SIGNED_IN"));
//                    }
//                } catch(\Exception $e) {
//                    Alert::instance()->setData(Alert::ERROR, $e->getMessage());
//                }
//            } else {
//                /** @var \Application\Model\Account\Authentication $authentication */
//                $authentication = $this->getServiceLocator()->get('Application\Model\Account\Authentication');
//
//                $data = [
//                    'email' => $account->getEmail(),
//                    'password' => $account->getPassword(),
//                ];
//
//                if ($result = $authentication->doAuthorization($data, false, true)) {
//                    Alert::instance()->setData(Alert::SUCCESS, Locale::instance()->translate_formatted("ALERT_ACCOUNT_SIGNED_IN"));
//                }
//            }
//        } else {
//            return $this->notFoundAction();
//        }
//
//        /** @var \Zend\Session\Container $container */
//        $container = new Container('remember_referer');
//
//        $referer = $container->offsetExists('referer') ? trim($container->referer, '/') : null;
//        $container->getManager()->getStorage()->clear('remember_referer');
//
//        if (!is_null($referer)) {
//            $this->redirect()->toUrl($referer);
//        } else {
//            $this->redirect()->toRoute('frontend', ['locale' => $this->__locale]);
//        }
    }


    /**
     * Action is necessary for do login with OAuth2
     *
     * Этот экшен вызывается при клике пользователя на ссылку регистрации через соцсети
     * Потом происходит редирект на выбранную соцсеть
     */
    public function doAuthAction()
    {
        $provider = $this->__provider_alias[$this->params()->fromRoute('provider')];
        $adapter_name = implode('\\', ['ReverseOAuth2', $provider]);

        $client = $this->getServiceLocator()->get($adapter_name);
        $url = $client->getUrl();

        /** @var \Zend\Session\Container $container */
        $container = new Container('remember_referer');

        $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
        $container->referer = $referer;

        $this->redirect()->toUrl($url);
    }


    /**
     * @access private
     *
     * This method is necessary to convert data from providers to account's format.
     *
     * @param $account_info
     * @param $provider
     * @return array
     */
    private function convertProviderDataToAccount($account_info, $provider)
    {
        $data = [];
        switch (strtolower($provider)) {
            case 'facebook':
                $data['provider'] = strtolower($provider);
                $data['provider_id'] = strtolower($account_info->id);
                $data['first_name'] = array_key_exists('first_name', $account_info) ? $account_info->first_name : '';
                $data['last_name'] = array_key_exists('last_name', $account_info) ? $account_info->last_name : '';
                $data['email'] = array_key_exists('email', $account_info) ? $account_info->email : $account_info->id.'@facebook.com';
                $data['gender'] = array_key_exists('gender', $account_info) ? 'TEXT_ACCOUNT_'.strtoupper($account_info->gender) : '';
                $data['avatar'] = 'https://graph.facebook.com/'.$account_info->id.'/picture?width=150&height=150';
                $data['subscriber_email'] = $data['email'];

                break;

            case 'google':
                $data['provider'] = strtolower($provider);
                $data['provider_id'] = strtolower($account_info->id);
                $data['first_name'] = array_key_exists('given_name', $account_info) ? $account_info->given_name : '';
                $data['last_name'] = array_key_exists('family_name', $account_info) ? $account_info->family_name : '';
                $data['email'] = $account_info->email;
                $data['gender'] = array_key_exists('gender', $account_info) ? 'TEXT_ACCOUNT_'.strtoupper($account_info->gender) : '';
                $data['avatar'] = array_key_exists('picture', $account_info) ? $account_info->picture : '';
                $data['subscriber_email'] = $data['email'];

                break;
        }

        return $data;
    }

    /**
     * @return mixed
     */
    public function getServiceLocator()
    {
        return $this->_serviceLocator;
    }
}