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
            'prooph.link.processor_proxy.forward_message_extractor_translator'  => 'Prooph\Link\ProcessorProxy\ProophPlugin\Factory\ForwardMessageExtractorTranslatorFactory',
            'prooph.link.processor_proxy.in_memory_message_forwarder'           => 'Prooph\Link\ProcessorProxy\ProophPlugin\Factory\InMemoryMessageForwarderFactory',
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
    'prooph.psb' => [
        'command_router_map' => [
            //By default a service bus message received by the processor proxy API is wrapped with a ForwardMessage command
            //and then forwarded to the Prooph\Link\ProcessorProxy\\ProophPlugin\InMemoryMessageForwarder which forwards
            //the wrapped message to the processing workflow engine.
            //An add on can override the routing so that the ForwardMessage is send to a message dispatcher which puts
            //the wrapped service bus message into a worker queue so that the API service can respond fast and
            //don't have to wait until the message was processed by the workflow processor
            'Prooph\Link\ProcessorProxy\Command\ForwardHttpMessage' => 'prooph.link.processor_proxy.in_memory_message_forwarder',
        ],
        'command_bus' => [
            //This plugin extracts a service bus message out of a ProcessorProxy\Command\ForwardMessage command
            //when the command is send to a message dispatcher
            'prooph.link.processor_proxy.forward_message_extractor_translator',
        ]
    ],
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