<?php
/*
* This file is part of prooph/link.
 * (c) prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * 
 * Date: 10.01.15 - 22:30
 */

namespace Prooph\Link\ProcessorProxy\ProophPlugin\Factory;

use Prooph\Processing\Environment\Environment;
use Prooph\Link\ProcessorProxy\ProophPlugin\InMemoryMessageForwarder;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class InMemoryMessageForwarderFactory
 *
 * @package ProcessorProxy\ProophPlugin\Factory
 * @author Alexander Miertsch <kontakt@codeliner.ws>
 */
final class InMemoryMessageForwarderFactory implements FactoryInterface
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
        $env = $serviceLocator->get('processing_environment');

        return new InMemoryMessageForwarder($env->getWorkflowEngine());
    }
}
 