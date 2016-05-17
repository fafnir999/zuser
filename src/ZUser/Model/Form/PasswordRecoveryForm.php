<?php
namespace ZUser\Model\Form;

// Use block for Dormis.com
use Lib\Form\FormBase;


class PasswordRecoveryForm extends FormBase
{
    /** @var array Form's inputs */
    protected $__input_list = [
        'email' => [
            'attributes' => [
                'id' => 'recovery_email',
                'type' => 'text',
                'placeholder' => 'FORM_PH_ACCOUNT_RECOVERY_EMAIL',
                'class' => 'form-control'
            ],
            'options' => [
                'label' => 'FORM_LBL_ACCOUNT_RECOVERY_EMAIL'
            ]
        ],
    ];
}