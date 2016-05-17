<?php
namespace ZUser;

use ZUser\Factory\AccountControllerFactory;
use ZUser\Factory\OAuthControllerFactory;
use ZUser\Factory\AuthenticationServiceFactory;
use ZUser\Factory\AccountServiceFactory;
use ZUser\Factory\IsUserAuthorizedHelperFactory;
use ZUser\Factory\GetCurrentUserHelperFactory;
use ZUser\Factory\ModuleOptionsFactory;
use ZUser\Factory\AccountFormFactory;
use ZUser\Factory\AuthFormFactory;
use ZUser\Factory\RegistrationFormFactory;
use ZUser\Factory\AccountFilterFormFactory;
use ZUser\Factory\HydratorFactory;
use ZUser\Factory\ErrorLogFactory;
use ZUser\Factory\ListerProfileFormFactory;
use ZUser\Factory\RenterProfileFormFactory;
use ZUser\Factory\PasswordForgotFormFactory;
use ZUser\Factory\AdminControllerFactory;
use ZUser\Model\Form\AccountNumPagesForm;

return [
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view'
        ]
    ],
    'router' => [
        'routes' => [
            'account' => [
                'type' => 'literal',
                'options' => [
                    'route' => '/account',
                    'defaults' => [
                        'controller' => 'ZUser\Controller\Account',
                        'action' => 'profile',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    // Registration on page, without ajax
                    'registration' => [
                        'type' => 'literal',
                        'options' => [
                            'route' => '/registration',
                            'defaults' => [
                                'action' => 'registration',
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'auth' => [
                        'type' => 'literal',
                        'options' => [
                            'route' => '/auth',
                            'defaults' => [
                                'action' => 'auth',
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'account' => [
                        'type' => 'literal',
                        'options' => [
                            'route' => '/account',
                            'defaults' => [
                                'action' => 'account',
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'profile' => [
                        'type' => 'literal',
                        'options' => [
                            'route' => '/profile',
                            'defaults' => [
                                'action' => 'profile',
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'approve' => [
                        'type' => 'segment',
                        'options' => [
                            'route' => '/:token/approve',
                            'constraints' => [
                                'token' => '\w+'
                            ],
                            'defaults' => [
                                'action' => 'approve',
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'password-forgot' => [
                        'type' => 'literal',
                        'options' => [
                            'route' => '/password-forgot',
                            'defaults' => [
                                'action' => 'password-forgot',
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'password-recovery' => [
                        'type' => 'segment',
                        'options' => [
                            'route' => '/:token/password-recovery',
                            'constraints' => [
                                'token' => '\w+'
                            ],
                            'defaults' => [
                                'action' => 'password-recovery',
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'logout' => [
                        'type' => 'literal',
                        'options' => [
                            'route' => '/logout',
                            'defaults' => [
                                'action' => 'logout',
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    //Роутинг для панели администратора модуля
                    'admin' => [
                        'type' => 'literal',
                        'options' => [
                            'route' => '/admin',
                            'defaults' => [
                                'controller' => 'ZUser\Controller\Admin',
                                'action' => 'index'
                            ]
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'users' => [
                                'type' => 'literal',
                                'options' => [
                                    'route' => '/users',
                                    'defaults' => [
                                        'action' => 'index'
                                    ]
                                ],
                                'may_terminate' => true,
                                'child_routes' => [
                                    'change' => [
                                        'type' => 'literal',
                                        'options' => [
                                            'route' => '/change',
                                            'defaults' => [
                                                'action' => 'change'
                                            ]
                                        ],
                                    ],
                                    'edit-account' => [
                                        'type' => 'literal',
                                        'options' => [
                                            'route' => '/edit-account',
                                            'defaults' => [
                                                'action' => 'edit-account'
                                            ]
                                        ],
                                    ],
                                    'edit-profile' => [
                                        'type' => 'literal',
                                        'options' => [
                                            'route' => '/edit-profile',
                                            'defaults' => [
                                                'action' => 'edit-profile'
                                            ]
                                        ],
                                    ],
                                    'create' => [
                                        'type' => 'literal',
                                        'options' => [
                                            'route' => '/create',
                                            'defaults' => [
                                                'action' => 'create'
                                            ]
                                        ],
                                    ],
                                ]
                            ],

                        ]
                    ],
                ]
            ],



            'oauth2' => [
                'type' => 'literal',
                'options' => [
                    'route' => '/oauth2',
                    'defaults' => [
                        'controller' => 'ZUser\Controller\OAuth',
                    ],
                ],
                'may_terminate' => false,
                'child_routes' => [
                    'callback' => [
                        'type' => 'segment',
                        'options' => [
                            'route' => '/:provider/callback',
                            'constraints' => [
                                'provider' => '(google|github|facebook|linkedin)'
                            ],
                            'defaults' => [
                                'action' => 'callback'
                            ]
                        ],
                    ],
                    'do-auth' => [
                        'type' => 'segment',
                        'options' => [
                            'route' => '/:provider/do-auth',
                            'constraints' => [
                                'provider' => '(google|github|facebook|linkedin)'
                            ],
                            'defaults' => [
                                'action' => 'do-auth'
                            ]
                        ],
                    ],
                ],
            ],
        ]
    ],
    'controllers' => [
        'factories' => [
            'ZUser\Controller\Account' => AccountControllerFactory::class,
            'ZUser\Controller\OAuth' => OAuthControllerFactory::class,
            'ZUser\Controller\Admin' => AdminControllerFactory::class
        ],
    ],
    'service_manager' => [
        'factories' => [
            'authenticationService' => AuthenticationServiceFactory::class,
            'accountService' => AccountServiceFactory::class,

            'zuserModuleOptions' => ModuleOptionsFactory::class,
            'hydrator' => HydratorFactory::class,
            'errorLog' =>ErrorLogFactory::class,

            'accountForm' => AccountFormFactory::class,
            'authForm' => AuthFormFactory::class,
            'registrationForm' => RegistrationFormFactory::class,
            'listerProfileForm' =>ListerProfileFormFactory::class,
            'renterProfileForm' =>RenterProfileFormFactory::class,
            'passwordForgotForm' =>PasswordForgotFormFactory::class,
            'accountFilterForm' =>AccountFilterFormFactory::class,

        ],
        'invokables' => [
            'accountNumPagesForm' => AccountNumPagesForm::class
        ],
    ],
    'view_helpers' => [
        'factories' => [
            'isUserAuthorized' => IsUserAuthorizedHelperFactory::class,
            'getCurrentUser' => GetCurrentUserHelperFactory::class
        ]
    ],

    // Doctrine configuration
    'doctrine' => [
        'eventmanager' => array(
            'orm_default' => array(
                'subscribers' => array(
                    // pick any listeners you need
                    //Дополнительные параметры свойств для doctrine
                    'Gedmo\Timestampable\TimestampableListener',
                    'Gedmo\IpTraceable\IpTraceableListener',
                ),
            ),
        ),
        'driver' => [
            __NAMESPACE__.'_driver' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => [
                    __DIR__.'/../src/'.__NAMESPACE__.'/Entity'
                ]
            ],
            'orm_default' => [
                'drivers' => [
                    __NAMESPACE__.'\Entity' => __NAMESPACE__.'_driver'
                ]
            ]
        ]
    ],
    'translator' => array(
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
];