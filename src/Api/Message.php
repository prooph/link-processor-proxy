<?php
/*
* This file is part of prooph/link.
 * (c) prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * 
 * Date: 09.01.15 - 21:49
 */

namespace Prooph\Link\ProcessorProxy\Api;

use Prooph\Link\Application\Service\AbstractRestController;
use Prooph\Link\Application\Service\ActionController;
use Prooph\Link\ProcessorProxy\Command\ForwardHttpMessage;
use Prooph\Link\ProcessorProxy\Model\MessageLogger;
use Prooph\Processing\Processor\WorkflowEngine;
use Prooph\ServiceBus\CommandBus;
use Prooph\ServiceBus\Message\StandardMessage;
use Rhumsaa\Uuid\Uuid;
use ZF\ApiProblem\ApiProblem;
use ZF\ApiProblem\ApiProblemResponse;

/**
 * Class Message
 *
 * @package ProcessorProxy\Api
 * @author Alexander Miertsch <kontakt@codeliner.ws>
 */
final class Message extends AbstractRestController
{
    /**
     * @var WorkflowEngine
     */
    private $workflowEngine;

    /**
     * @var MessageLogger
     */
    private $messageLogger;

    /**
     * @param WorkflowEngine $workflowEngine
     * @param MessageLogger $messageLogger
     */
    public function __construct(WorkflowEngine $workflowEngine, MessageLogger $messageLogger)
    {
        $this->workflowEngine = $workflowEngine;
        $this->messageLogger = $messageLogger;
    }

    /**
     * @param array $data
     * @return mixed|void
     */
    public function create($data)
    {
        $message = StandardMessage::fromArray($data);

        $this->workflowEngine->dispatch($message);

        //@TODO: improve response, provide get service which returns status of the message including related actions
        //@TODO: like started process etc.
        return $message->toArray();
    }

    /**
     * @param string $id
     * @return mixed
     */
    public function get($id)
    {
        $message = $this->messageLogger->getEntryForMessageId(Uuid::fromString($id));

        if (is_null($message)) return new ApiProblemResponse(new ApiProblem(404, "Message can not be found"));

        return ["message" => $message->toArray()];
    }
}
 