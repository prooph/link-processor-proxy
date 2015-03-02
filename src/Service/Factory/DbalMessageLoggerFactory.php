<?php
/*
* This file is part of prooph/link.
 * (c) prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * 
 * Date: 12.01.15 - 01:26
 */

namespace Prooph\Link\ProcessorProxy\Service\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Prooph\Link\ProcessorProxy\Service\DbalMessageLogger;

/**
 * Class DbalMessageLoggerFactory
 *
 * @package ProcessorProxy\Service
 * @author Alexander Miertsch <kontakt@codeliner.ws>
 */
final class DbalMessageLoggerFactory implements FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new DbalMessageLogger($serviceLocator->get('prooph.link.app.db'));
    }
}
 