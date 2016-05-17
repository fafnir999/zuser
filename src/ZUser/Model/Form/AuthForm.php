<?php
namespace ZUser\Model\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\I18n\Translator\TranslatorAwareTrait;
use Zend\I18n\Translator\TranslatorInterface;

class AuthForm extends Form implements InputFilterProviderInterface
{
    use TranslatorAwareTrait;

    public function __construct(TranslatorInterface $translator)
    {
        $this->setTranslator($translator);

        parent::__construct('authForm');

        $this->add([
            'name' => 'email',
            'attributes' => [
                'id' => 'auth_email',
                'type' => 'text',
                'placeholder' => $this->getTranslator()->translate("Enter your register email"),
                'class' => 'form-control'
            ],
            'options' => [
                'label' =>  $this->getTranslator()->translate("Email")
            ]
        ]);

        $this->add([
            'name' => 'password',
            'attributes' => [
                'id' => 'auth_password',
                'type' => 'password',
                'placeholder' =>  $this->getTranslator()->translate("Enter password"),
                'class' => 'form-control'
            ],
            'options' => [
                'label' =>  $this->getTranslator()->translate("Password"),
            ]
        ]);

        $this->add([
            'name' => 'remember_me',
            'type' => 'Zend\Form\Element\Checkbox',
            'options' => [
                'use_hidden_element' => true,
                'checked_value' => '1',
                'unchecked_value' => '0'
            ],
            'attributes' => [
                'value' => '0'
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
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min' => 3,
                            'max' => 50
                        ],
                    ],
                    ['name' => 'NotEmpty']
                ]
            ],
            'password' => [
                'required' => true
            ],
            'remember_me' => [
                'required' => false,
            ]
        ];
    }
}