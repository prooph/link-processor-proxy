<?php
return [
    'router' => [
        'routes' => [
            'prooph.link' => [
                'child_routes' => [
                    'processor_proxy' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/processor-proxy',
                        ],
                        'may_terminate' => false,
                        'child_routes' => [
                            'api' => [
                                'type' => 'Literal',
                                'options' => [
                                    'route' => '/api',
                                ],
                                'may_terminate' => false,
                                'child_routes' => [
                                    'messages' => [
                                        'type' => 'Segment',
                                        'options' => [
                                            'route' => '/messages[/:id]',
                                            'constraints' => array(
                                                'id' => '.+',
                                            ),
                                            'defaults' => [
                                                'controller' => 'Prooph\Link\ProcessorProxy\Api\Message',
                                            ]
                                        ]
                                    ],
                                    'collect_data_triggers' => [
                                        'type' => 'Segment',
                                        'options' => [
                                            'route' => '/collect-data-triggers[/:id]',
                                            'constraints' => array(
                                                'id' => '.+',
                                            ),
                                            'defaults' => [
                                                'controller' => 'Prooph\Link\ProcessorProxy\Api\CollectDataTrigger',
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                    ],
                ],
            ],
        ],
    ],
    'processing' => [
        'plugins' => [
            'prooph.link.processor_proxy.start_message_process_id_logger' => 'prooph.link.processor_proxy.start_message_process_id_logger',
            'prooph.link.processor_proxy.message_flow_logger'             => 'prooph.link.processor_proxy.message_flow_logger',

        ]
    ],
    'service_manager' => [
        'factories' => [
            'prooph.link.processor_proxy.message_logger'                        => 'Prooph\Link\ProcessorProxy\Service\Factory\DbalMessageLoggerFactory',
            'prooph.link.processor_proxy.start_message_process_id_logger'       => 'Prooph\Link\ProcessorProxy\ProcessingPlugin\Factory\StartMessageProcessIdLoggerFactory',
            'prooph.link.processor_proxy.message_flow_logger'                   => 'Prooph\Link\ProcessorProxy\ProcessingPlugin\Factory\MessageFlowLoggerFactory',
        ]
    ],
    'controllers' => array(
        'factories' => [
            'Prooph\Link\ProcessorProxy\Api\CollectDataTrigger' => 'Prooph\Link\ProcessorProxy\Api\Factory\CollectDataTriggerFactory',
            'Prooph\Link\ProcessorProxy\Api\Message' => 'Prooph\Link\ProcessorProxy\Api\Factory\MessageResourceFactory',
        ]
    ),
    'zf-content-negotiation' => [
        'controllers' => [
            'Prooph\Link\ProcessorProxy\Api\Message'            => 'Json',
            'Prooph\Link\ProcessorProxy\Api\CollectDataTrigger' => 'Json',
        ],
        'accept_whitelist' => [
            'Prooph\Link\ProcessorProxy\Api\Message'            => ['application/json'],
            'Prooph\Link\ProcessorProxy\Api\CollectDataTrigger' => ['application/json'],
        ],
        'content_type_whitelist' => [
            'Prooph\Link\ProcessorProxy\Api\Message'            => ['application/json'],
            'Prooph\Link\ProcessorProxy\Api\CollectDataTrigger' => ['application/json'],
        ],
    ],
];