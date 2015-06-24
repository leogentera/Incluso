<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'MoodleApi\Controller\Course' => 'MoodleApi\Controller\CourseController',
            'MoodleApi\Controller\User' => 'MoodleApi\Controller\UserController',
            'MoodleApi\Controller\Index' => 'MoodleApi\Controller\IndexController',
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
                    'route'    => '/user[/:id]',
                    'constraints' => array(
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'MoodleApi\Controller\User',
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
