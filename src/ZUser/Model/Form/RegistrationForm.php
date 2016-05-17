<?php
namespace ZUser\Model\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\I18n\Translator\TranslatorAwareTrait;
use Zend\I18n\Translator\TranslatorInterface;

use ZUser\Entity\Account;
use ZUser\Entity\Profile;
use ZUser\Model\Validator\CheckEmail;

class RegistrationForm extends Form implements InputFilterProviderInterface
{
    use TranslatorAwareTrait;

    /** @var \Doctrine\ORM\EntityManager */
    private $_entityManager;

    /** @var \ZUser\Options\ModuleOptions */
    private $_config;

    public function __construct(TranslatorInterface $translator,
                                \Doctrine\ORM\EntityManager $entityManager,
                                $_config)
    {
        $this->setTranslator($translator);

        $this->setEntityManager($entityManager);

        $this->setConfig($_config);

        parent::__construct('registrationForm');

        $this->add([
            'name' => 'email',
            'attributes' => [
                'id' => 'email',
                'type' => 'text',
                'placeholder' =>  $this->getTranslator()->translate("Enter your email"),
                'class' => 'form-control'
            ],
            'options' => [
                'label' =>  $this->getTranslator()->translate("Email"),
            ]
        ]);

        $this->add([
            'name' => 'password',
            'attributes' => [
                'id' => 'password',
                'type' => 'password',
                'placeholder' =>  $this->getTranslator()->translate("Enter password"),
                'class' => 'form-control'
            ],
            'options' => [
                'label' =>  $this->getTranslator()->translate("Password"),
            ]
        ]);

        $this->add([
            'name' => 'account_type',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => [
                'id' => 'account_type',
                'class' => 'form-control'
            ],
            'options' => [
                'label' =>  $this->getTranslator()->translate("Register as"),
                'value_options' => [
                    Profile::ACCOUNT_TYPE_RENTER => $this->getTranslator()->translate("renter"),
                    Profile::ACCOUNT_TYPE_LISTER => $this->getTranslator()->translate("lister")
                ]
            ]
        ]);

    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return [
            'email' => [
                'required' => true,
                'filter' => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StripTags']
                ],
                'validators' => [
                    ['name' => 'Zend\Validator\EmailAddress'],
                    [
                        'name' => CheckEmail::class,
                        'options' => [
                            'field' => 'email',
                            'accountClass' => $this->getConfig()->getAccountEntityClass(),
                            '_entityManager' => $this->getEntityManager(),
                            'translator' => $this->getTranslator(),
                            'existWrong' => true
                        ]
                    ],
                ],
            ],
            'password' => [
                'required' => true,
                'filter' => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StripTags']
                ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min' => 3,
                            'max' => 50
                        ],
                    ]
                ]
            ],
        ];
    }

    /**
     * @return mixed
     */
    public function getEntityManager()
    {
        return $this->_entityManager;
    }

    /**
     * @param mixed $entityManager
     */
    public function setEntityManager(\Doctrine\ORM\EntityManager $entityManager)
    {
        $this->_entityManager = $entityManager;
    }

    /**
     * @return mixed
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * @param mixed $config
     */
    public function setConfig($config)
    {
        $this->_config = $config;
    }
}