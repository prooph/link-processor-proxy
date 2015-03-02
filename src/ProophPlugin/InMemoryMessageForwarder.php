<?php
/*
* This file is part of prooph/link.
 * (c) prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * 
 * Date: 10.01.15 - 12:40
 */

namespace Prooph\Link\ProcessorProxy\ProophPlugin;

use Prooph\Processing\Processor\WorkflowEngine;
use Prooph\ServiceBus\Message\MessageDispatcherInterface;
use Prooph\ServiceBus\Message\MessageInterface;

/**
 * Class InMemoryMessageForwarder
 *
 * This class has has a reference to the processing workflow engine. Any incoming service bus message is forwarded to
 * the workflow engine by determining the target channel for the message.
 *
 * @package ProcessorProxy\ProophPlugin
 * @author Alexander Miertsch <kontakt@codeliner.ws>
 */
final class InMemoryMessageForwarder implements MessageDispatcherInterface
{
    /**
     * @var WorkflowEngine
     */
    private $workflowEngine;

    /**
     * @param WorkflowEngine $workflowEngine
     */
    public function __construct(WorkflowEngine $workflowEngine)
    {
        $this->workflowEngine = $workflowEngine;
    }

    /**
     * @param MessageInterface $message
     * @return void
     */
    public function dispatch(MessageInterface $message)
    {
        $this->workflowEngine->dispatch($message);
    }
}
 