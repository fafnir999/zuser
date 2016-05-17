<?php
/**
 * Author: Yaroslav Kovalev
 * Date: 04.05.2016
 * Time: 16:17
 */

namespace ZUser\Model\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\I18n\Translator\TranslatorAwareTrait;
use Zend\I18n\Translator\TranslatorInterface;
use ZUser\Model\Validator\CheckEmail;

class PasswordForgotForm extends Form implements InputFilterProviderInterface
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

        parent::__construct('passwordForgotForm');

        $this->add([
            'name' => 'email',
            'attributes' => [
                'id' => 'email',
                'type' => 'text',
                'placeholder' => $this->getTranslator()->translate("Enter your register email"),
                'class' => 'form-control'
            ],
            'options' => [
                'label' =>  $this->getTranslator()->translate("Your register email")
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
                    ['name' => 'NotEmpty'],
                    [
                        'name' => CheckEmail::class,
                        'options' => [
                            'field' => 'email',
                            'accountClass' => $this->getConfig()->getAccountEntityClass(),
                            '_entityManager' => $this->getEntityManager(),
                            'translator' => $this->getTranslator(),
                            'existWrong' => false
                        ]
                    ],

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