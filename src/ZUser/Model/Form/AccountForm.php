<?php
/**
 * Author: Yaroslav Kovalev
 * Date: 29.04.2016
 * Time: 13:34
 */

namespace ZUser\Model\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\I18n\Translator\TranslatorAwareTrait;
use Zend\I18n\Translator\TranslatorInterface;

class AccountForm  extends Form implements InputFilterProviderInterface
{
    use TranslatorAwareTrait;

    public function __construct(TranslatorInterface $translator)
    {
        $this->setTranslator( $translator );

        parent::__construct('accountForm');

        $this->add([
            'name' => 'email',
            'attributes' => [
                'id' => 'email',
                'type' => 'text',
                'placeholder' => $this->getTranslator()->translate("Register email"),
                'class' => 'form-control',
                'disabled' => 'disabled'
            ],
            'options' => [
                'label' => $this->getTranslator()->translate('Register email'),
            ]
        ]);

        $this->add([
            'name' => 'password',
            'attributes' => [
                'id' => 'password',
                'type' => 'password',
                'placeholder' => "Enter new password",
                'class' => 'form-control'
            ],
            'options' => [
                'label' => $this->getTranslator()->translate("Password"),
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
}