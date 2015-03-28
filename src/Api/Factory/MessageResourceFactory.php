<?php
/*
* This file is part of prooph/link.
 * (c) prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * 
 * Date: 16.01.15 - 23:37
 */

namespace Prooph\Link\ProcessorProxy\Api\Factory;

use Prooph\Link\ProcessorProxy\Api\Message;
use Prooph\Processing\Environment\Environment;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class MessageResourceFactory
 *
 * @package ProcessorProxy\Api\Factory
 * @author Alexander Miertsch <kontakt@codeliner.ws>
 */
final class MessageResourceFactory implements FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var $env Environment */
        $env = $serviceLocator->getServiceLocator()->get('processing_environment');

        return new Message(
            $env->getWorkflowEngine(),
            $serviceLocator->getServiceLocator()->get('prooph.link.processor_proxy.message_logger')
        );
    }
}
 