<?php
/**
 * Author: Yaroslav Kovalev
 * Date: 03.05.2016
 * Time: 13:39
 */

namespace ZUser\Model\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\I18n\Translator\TranslatorAwareTrait;
use Zend\I18n\Translator\TranslatorInterface;

class RenterProfileForm extends Form implements InputFilterProviderInterface
{
    use TranslatorAwareTrait;

    public function __construct(TranslatorInterface $translator)
    {
        $this->setTranslator($translator);

        parent::__construct('renterProfileForm');

        $this->add([
            'name' => 'renterCondition',
            'attributes' => [
                'id' => 'renterCondition',
                'type' => 'text',
                'placeholder' => $this->getTranslator()->translate("renterCondition"),
                'class' => 'form-control'
            ],
            'options' => [
                'label' =>  $this->getTranslator()->translate("renterCondition")
            ]
        ]);

        $this->add([
            'name' => 'subscriber_email',
            'attributes' => [
                'id' => 'subscriber_email',
                'type' => 'text',
                'placeholder' =>  $this->getTranslator()->translate("Enter your email"),
                'class' => 'form-control'
            ],
            'options' => [
                'label' =>  $this->getTranslator()->translate("Subscriber email"),
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
            'subscriber_email' => [
                'required' => true,
                'filter' => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StripTags']
                ],
                'validators' => [
                    ['name' => 'Zend\Validator\EmailAddress'],
                ],
            ],
        ];
    }
}