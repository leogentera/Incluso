<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'MoodleApi\Controller\Course' => 'MoodleApi\Controller\CourseController',
            'MoodleApi\Controller\User' => 'MoodleApi\Controller\UserController',
            'MoodleApi\Controller\Authentication' => 'MoodleApi\Controller\AuthenticationController',
            'MoodleApi\Controller\UserCourse' => 'MoodleApi\Controller\UserCourseController',
            'MoodleApi\Controller\Avatar' => 'MoodleApi\Controller\AvatarController',
        	'MoodleApi\Controller\Catalog' => 'MoodleApi\Controller\CatalogController', 
            'MoodleApi\Controller\Cache' => 'MoodleApi\Controller\CacheController', 
            'MoodleApi\Controller\Activity' => 'MoodleApi\Controller\ActivityController', 
            'MoodleApi\Controller\Forum' => 'MoodleApi\Controller\ForumController', 
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
            'avatar' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/avatar[/:id]',
                    'constraints' => array(
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'MoodleApi\Controller\Avatar',
                    ),
                ),
            ),
            'activity' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/activity[/:id]',
                    'constraints' => array(
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'MoodleApi\Controller\Activity',
                    ),
                ),
            ),            

        	'user' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/user[/:id]',
                    'constraints' => array(
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'MoodleApi\Controller\User',
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
                )
        	),

            'UserCourse' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/usercourse[/:id]',
                    'defaults' => array(
                        'controller' => 'MoodleApi\Controller\UserCourse',
                    ),
                )
            ),
        		
    		'Catalog' => array(
				'type'    => 'segment',
				'options' => array(
					'route'    => '/catalog',
					'defaults' => array(
							'controller' => 'MoodleApi\Controller\Catalog',
					),
				)
    		),

            'Cache' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/cache',
                    'defaults' => array(
                            'controller' => 'MoodleApi\Controller\Cache',
                    ),
                )
            ),

            'Forum' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/forum',
                    'defaults' => array(
                            'controller' => 'MoodleApi\Controller\Forum',
                    ),
                )
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
