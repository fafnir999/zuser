<?php
/**
 * Author: Yaroslav Kovalev
 * Date: 11.05.2016
 * Time: 11:06
 */

namespace ZUser\Model\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\I18n\Translator\TranslatorAwareTrait;
use Zend\I18n\Translator\TranslatorInterface;
use ZUser\Entity\Account;
use ZUser\Entity\Profile;

class AccountFilterForm  extends Form implements InputFilterProviderInterface
{
    use TranslatorAwareTrait;

    public function __construct(TranslatorInterface $translator)
    {
        $this->setTranslator( $translator );

        $this->setAttribute('method', 'GET');

        $this->setAttribute('class', 'form-inline');

        parent::__construct('accountFilterForm');

        $this->add([
            'name' => 'id',
            'attributes' => [
                'id' => 'id',
                'data-filter' => '1',
                'type' => 'text',
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'name' => 'email',
            'attributes' => [
                'id' => 'email',
                'data-filter' => '1',
                'type' => 'text',
                'placeholder' => $this->getTranslator()->translate("Enter email"),
                'class' => 'form-control'
            ],
        ]);

        $this->add([
            'name' => 'date_created',
            'type' => 'date',
            'class' => 'form-control',
            'attributes' => [
                'data-filter' => '1',
                'class' => 'form-control'
            ],
        ]);

        $this->add([
            'name' => 'date_last_login',
            'type' => 'date',
            'class' => 'form-control',
            'attributes' => [
                'data-filter' => '1',
                'class' => 'form-control'
            ],
        ]);

        $this->add([
            'name' => 'approved',
            'type' => 'select',
            'class' => 'form-control',
            'attributes' => [
                'data-filter' => '1',
                'class' => 'form-control'
            ],
            'options' => [
                'value_options' => [
                    'empty_option' => $this->getTranslator()->translate("All"),
                    Account::ACCOUNT_APPROVED => $this->getTranslator()->translate("Approved"),
                    Account::ACCOUNT_NOT_APPROVED => $this->getTranslator()->translate("Not approved")
                ]
            ]
        ]);

        $this->add([
            'name' => 'enabled',
            'type' => 'select',

            'attributes' => [
                'data-filter' => '1',
                'class' => 'form-control',
                'id' => 'enabled',
            ],
            'options' => [
                'value_options' => [
                    'empty_option' => $this->getTranslator()->translate("All"),
                    Account::ACCOUNT_ENABLED => $this->getTranslator()->translate("Enabled"),
                    Account::ACCOUNT_DISABLED=> $this->getTranslator()->translate("Disabled")
                ]
            ]
        ]);

        $this->add([
            'name' => 'profile_type',
            'type' => 'select',
            'attributes' => [
                'data-filter' => '1',
                'class' => 'form-control'
            ],
            'options' => [
                'value_options' => [
                    'empty_option' => $this->getTranslator()->translate("All"),
                    'profile.'.Profile::ACCOUNT_TYPE_LISTER => $this->getTranslator()->translate("Lister"),
                    'profile.'.Profile::ACCOUNT_TYPE_RENTER=> $this->getTranslator()->translate("Renter")
                ]
            ]
        ]);
    }

    public function getInputFilterSpecification()
    {
        return [
            'id' => [
                'required' => false,
                'filter' => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StripTags']
                ],
            ],
            'email' => [
                'required' => false,
                'filter' => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StripTags']
                ],
            ],
            'date_created' => [
                'required' => false,
                'filter' => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StripTags']
                ],
            ],
            'date_last_login' => [
                'required' => false,
                'filter' => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StripTags']
                ],
            ],
            'approved' => [
                'required' => false,
            ],
            'enabled' => [
                'required' => false,
            ],
            'profile_type' => [
                'required' => false,
            ],
        ];
    }
}