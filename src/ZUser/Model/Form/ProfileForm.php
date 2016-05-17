<?php
namespace ZUser\Model\Form;

use Lib\Form\FormBase;


class ProfileForm extends FormBase
{
    /** @var array Form's inputs */
    protected $__input_list = [
        'first_name' => [
            'attributes' => [
                'id' => 'first_name',
                'type' => 'text',
                'placeholder' => "FORM_PH_ACCOUNT_FIRSTNAME",
                'class' => 'form-control'
            ],
            'options' => [
                'label' => "FORM_LBL_ACCOUNT_FIRSTNAME",
            ]
        ],
        'last_name' => [
            'attributes' => [
                'id' => 'last_name',
                'type' => 'text',
                'placeholder' => "FORM_PH_ACCOUNT_LASTNAME",
                'class' => 'form-control'
            ],
            'options' => [
                'label' => "FORM_LBL_ACCOUNT_LASTNAME",
            ]
        ],
        'email' => [
            'attributes' => [
                'id' => 'email',
                'type' => 'text',
                'placeholder' => "FORM_PH_ACCOUNT_EMAIL",
                'class' => 'form-control',
                'disabled' => true
            ],
            'options' => [
                'label' => "FORM_LBL_ACCOUNT_EMAIL",
            ]
        ],
        'subscriber_email' => [
            'attributes' => [
                'id' => 'subscriber_email',
                'type' => 'text',
                'placeholder' => "FORM_PH_ACCOUNT_SUBSCRIBER_EMAIL",
                'class' => 'form-control',
            ],
            'options' => [
                'label' => "FORM_LBL_ACCOUNT_SUBSCRIBER_EMAIL",
            ]
        ],
        'mobile' => [
            'attributes' => [
                'id' => 'mobile',
                'type' => 'text',
                'placeholder' => "FORM_PH_ACCOUNT_MOBILE",
                'class' => 'form-control'
            ],
            'options' => [
                'label' => "FORM_LBL_ACCOUNT_MOBILE",
            ]
        ],
        'passport_id' => [
            'attributes' => [
                'id' => 'passport_id',
                'type' => 'text',
                'placeholder' => "FORM_PH_ACCOUNT_PASSPORT_ID",
                'class' => 'form-control'
            ],
            'options' => [
                'label' => "FORM_LBL_ACCOUNT_PASSPORT_ID",
            ]
        ],
        'birthday' => [
            'type' => '\Zend\Form\Element\Date',
            'attributes' => [
                'id' => 'birthday',
                'placeholder' => "FORM_PH_ACCOUNT_BIRTHDAY",
                'class' => 'form-control'
            ],
            'options' => [
                'label' => "FORM_LBL_ACCOUNT_BIRTHDAY",
            ]
        ],
        'gender' => [
            'type' => '\Zend\Form\Element\Radio',
            'attributes' => [
                //'id' => 'gender',
            ],
            'options' => [
                'label' => "FORM_LBL_ACCOUNT_GENDER",
                'value_options' => [
                    'TEXT_ACCOUNT_MALE' => "TEXT_ACCOUNT_MALE",
                    'TEXT_ACCOUNT_FEMALE' => "TEXT_ACCOUNT_FEMALE",
                ]
            ]
        ],
        'country' => [
            'type' => '\DoctrineModule\Form\Element\ObjectSelect',
            'attributes' => [
                'id' => 'account_country',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => "FORM_LBL_ACCOUNT_COUNTRY",
                'empty_option' => "FORM_PH_ACCOUNT_COUNTRY",
                'object_manager' => '',
                'target_class' => 'ZUser\Entity\Country',
                'is_method' => true,
                'property' => 'title',
                'find_method' => [
                    'name' => 'getAvailableCountries'
                ]
            ]
        ],
        'region' => [
            'type' => 'Zend\Form\Element\Select',
            'attributes' => [
                'id' => 'account_region',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => "FORM_LBL_ACCOUNT_REGION",
                'empty_option' => "FORM_PH_ACCOUNT_REGION",
                'disable_inarray_validator' => true,
            ]
        ],
        'settlement' => [
            'type' => 'Zend\Form\Element\Hidden',
            'attributes' => [
                'id' => 'account_settlement',
                'class' => 'form-control',
            ]
        ],
        'avatar' => [
            'type' => 'Zend\Form\Element\File',
            'attributes' => [
                'id' => 'account_avatar',
                //'multiple' => true
            ],
            'options' => [
                'label' => "FORM_LBL_ACCOUNT_AVATAR",
            ]
        ]
    ];


    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return [
            'first_name' => [
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
                    [
                        'name' => 'Zend\I18n\Validator\Alpha',
                        'options' => [
                            'allowWhiteSpace' => true
                        ]
                    ]
                ]
            ],
            'last_name' => [
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
                    [
                        'name' => 'Zend\I18n\Validator\Alpha',
                        'options' => [
                            'allowWhiteSpace' => true
                        ]
                    ]
                ]
            ],
            'subscriber_email' => [
                'required' => true,
                'filter' => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StripTags']
                ],
                'validators' => [
                    ['name' => 'Zend\Validator\EmailAddress'],
                    [
                        'name' => '\Application\Model\Account\Validator\CheckEmail',
                        'options' => [
                            '__account_id' => $this->__form_options['__account_id'],
                            '__field' => 'subscriber_email',
                            '__service_locator' => $this->getServiceLocator()
                        ]
                    ],
                ],
            ],
            'gender' => [
                'required' => true
            ],
            'birthday' => [
                'required' => true,
                'validators' => [
                    [
                        'name' => 'Zend\Validator\Date',
                        'option' => [
                            'format' => 'Y-m-d'
                        ]
                    ]
                ]
            ],
            'mobile' => [
                'required' => false,
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
            'passport_id' => [
                'required' => false,
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
            'avatar' => [
                'required' => false,
                'validators' => [
                    [
                        'name' => 'Zend\Validator\File\Extension',
                        'options' => ['jpg', 'jpeg', 'png']
                    ],
                    [
                        'name' => 'Zend\Validator\File\ImageSize',
                        'options' => [
                            'minWidth' => 150,
                            'minHeight' => 150,
                            //'maxWidth' => 480, ??
                            //'maxHeight' => 480, ??
                        ]
                    ],
                    [
                        'name' => 'Zend\Validator\File\MimeType',
                        'options' => ['image']
                    ],
                    [
                        'name' => 'Zend\Validator\File\Size',
                        'options' => ['min' => '1kB', 'max' => '2MB']
                    ],
                ],
            ],
            'country' => [
                'required' => false
            ],
            'region' => [
                'required' => false
            ],
            'settlement' => [
                'required' => false
            ],
        ];
    }


    /**
     * @access protected
     *
     * Method is necessary to prepare form's inputs.
     *
     * @return FormBase
     */
    protected function prepareInputs()
    {
        $this->__input_list['country']['options']['object_manager'] = $this->getEntityManager();

        return $this;
    }
}