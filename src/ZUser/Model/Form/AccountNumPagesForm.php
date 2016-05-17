<?php
/**
 * Author: Yaroslav Kovalev
 * Date: 12.05.2016
 * Time: 16:19
 */
namespace ZUser\Model\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class AccountNumPagesForm  extends Form implements InputFilterProviderInterface
{

    public function __construct()
    {
        $this->setAttribute('method', 'GET');

        parent::__construct('accountNumPagesForm');

        $this->add([
            'name' => 'numPages',
            'type' => 'select',
            'id' => 'numPages',
            'attributes' => [
                'class' => 'form-control'
            ],
            'options' => [
                'value_options' => [
                    5 => 5,
                    10 => 10,
                    20 => 20,
                    50 => 50
                ]
            ]
        ]);
    }

    public function getInputFilterSpecification()
    {
        return [
        ];
    }
}