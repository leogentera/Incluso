<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'MoodleApi\Controller\Course' => 'MoodleApi\Controller\CourseController',
            'MoodleApi\Controller\Catalog' => 'MoodleApi\Controller\CatalogController',
            'MoodleApi\Controller\User' => 'MoodleApi\Controller\UserController',
        	'MoodleApi\Controller\CourseContents' => 'MoodleApi\Controller\CourseContentController',
            'MoodleApi\Controller\Authentication' => 'MoodleApi\Controller\AuthenticationController',
        	'MoodleApi\Controller\Register'       => 'MoodleApi\Controller\RegisterController',
        	'MoodleApi\Controller\ResetPassword'       => 'MoodleApi\Controller\ResetPasswordController',
        	'MoodleApi\Controller\ForgotPassword'       => 'MoodleApi\Controller\ForgotPasswordController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'MoodleApi\Controller\Course',
                        'action' => 'index',
                    ),
                ),
            ),
            'course' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/course[/:id]',
                    'constraints' => array(
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'MoodleApi\Controller\Course',
                    ),
                ),
            ),
            'catalog' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/catalog[/:catalogname]',
                    'constraints' => array(
                        'catalogname'     => '[a-zA-Z0-9\-_]+',
                    ),
                    'defaults' => array(
                        'controller' => 'MoodleApi\Controller\Catalog',
                    ),
                ),
            ),
        	'user' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/user/:id',
                    'constraints' => array(
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'MoodleApi\Controller\User',
                    ),
                ),
            ),
        		'CourseContents' => array(
        				'type'    => 'segment',
        				'options' => array(
        						'route'    => '/CourseContents[/:id]',
        						'constraints' => array(
        								'id'     => '[0-9]+',
        						),
        						'defaults' => array(
        								'controller' => 'MoodleApi\Controller\CourseContents',
        						),
        				),
        		),
            'Authentication' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/authentication',
                    'defaults' => array(
                        'controller' => 'MoodleApi\Controller\Authentication',
                    ),
                ),
            ),
        		
        	'Register' => array(
        		'type'    => 'segment',
        		'options' => array(
        			'route'    => '/Register',
        			'defaults' => array(
        			'controller' => 'MoodleApi\Controller\Register',
        			),
        		),
        	),
        		
        	'ResetPassword' => array(
        		'type'    => 'segment',
        		'options' => array(
        			'route'    => '/ResetPassword',
        			'defaults' => array(
        			'controller' => 'MoodleApi\Controller\ResetPassword',
        			),
        		),
        	),
        	
        	'ForgotPassword' => array(
        		'type'    => 'segment',
        		'options' => array(
        			'route'    => '/ForgotPassword',
        			'defaults' => array(
        			'controller' => 'MoodleApi\Controller\ForgotPassword',
        			),
        		),
        ),
        	
        ),
    ),
    'view_manager' => array(
//         'template_path_stack' => array(
//             'MoodleApi' => __DIR__ . '/../view',
//         ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
);
