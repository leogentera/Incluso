<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'MoodleApi\Controller\Course' => 'MoodleApi\Controller\CourseController',
            'MoodleApi\Controller\User' => 'MoodleApi\Controller\UserController',
        	'MoodleApi\Controller\CourseContents' => 'MoodleApi\Controller\CourseContentController',
            'MoodleApi\Controller\Authentication' => 'MoodleApi\Controller\AuthenticationController',
        	'MoodleApi\Controller\Register'       => 'MoodleApi\Controller\RegisterController',
        	'MoodleApi\Controller\ResetPassword'       => 'MoodleApi\Controller\ResetPasswordController',
        	'MoodleApi\Controller\ForgotPassword'       => 'MoodleApi\Controller\ForgotPasswordController',
        	'MoodleApi\Controller\UserProfile'       => 'MoodleApi\Controller\UserProfileController',
        	'MoodleApi\Controller\UpdateUserProfile'       => 'MoodleApi\Controller\UpdateUserProfileController',
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
        			'route'    => '/register',
        			'defaults' => array(
        			'controller' => 'MoodleApi\Controller\Register',
        			),
        		),
        	),
        		
        	'ResetPassword' => array(
        		'type'    => 'segment',
        		'options' => array(
        			'route'    => '/resetpassword',
        			'defaults' => array(
        			'controller' => 'MoodleApi\Controller\ResetPassword',
        			),
        		),
        	),
        	
        	'ForgotPassword' => array(
        		'type'    => 'segment',
        		'options' => array(
        			'route'    => '/forgotpassword',
        			'defaults' => array(
        			'controller' => 'MoodleApi\Controller\ForgotPassword',
        			),
        		),
        ),
        		
        		'UserProfile' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/userprofile/:id',
                    'constraints' => array(
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'MoodleApi\Controller\UserProfile',
                    ),
                ),
            ),
        		
        		'UpdateUserProfile' => array(
        				'type'    => 'segment',
        				'options' => array(
        						'route'    => '/updateuserprofile',
        						'defaults' => array(
        								'controller' => 'MoodleApi\Controller\UpdateUserProfile',
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
