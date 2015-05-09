<?php
/*
* This file is part of prooph/link.
 * (c) prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * 
 * Date: 12.01.15 - 22:39
 */

namespace Prooph\Link\ProcessorProxy\ProcessingPlugin;

use Prooph\Common\Event\ActionEventDispatcher;
use Prooph\Common\Event\ActionEventListenerAggregate;
use Prooph\Common\Event\DetachAggregateHandlers;
use Prooph\Common\Messaging\RemoteMessage;
use Prooph\Processing\Environment\Environment;
use Prooph\Processing\Environment\Plugin;
use Prooph\Processing\Message\ProcessingMessage;
use Prooph\Processing\Message\LogMessage;
use Prooph\Processing\Message\WorkflowMessage;
use Prooph\Processing\Processor\Command\StartSubProcess;
use Prooph\Processing\Processor\Event\SubProcessFinished;
use Prooph\Link\ProcessorProxy\Model\MessageLogEntry;
use Prooph\Link\ProcessorProxy\Model\MessageLogger;
use Prooph\ServiceBus\Process\CommandDispatch;
use Prooph\ServiceBus\Process\EventDispatch;
use Prooph\ServiceBus\Process\MessageDispatch;

/**
 * Class MessageFlowLogger
 *
 * This logger listens on processing workflow engine channels to log the message flow of all processing messages
 *
 * @package ProcessorProxy\ProcessingPlugin\Factory
 * @author Alexander Miertsch <kontakt@codeliner.ws>
 */
final class MessageFlowLogger implements ActionEventListenerAggregate, Plugin
{
    use DetachAggregateHandlers;

    const PLUGIN_NAME ="prooph.link.processor_proxy.message_flow_logger";
    /**
     * @var MessageLogger
     */
    private $messageLogger;

    /**
     * @param MessageLogger $messageLogger
     */
    public function __construct(MessageLogger $messageLogger)
    {
        $this->messageLogger = $messageLogger;
    }

    /**
     * Return the name of the plugin
     *
     * @return string
     */
    public function getName()
    {
        return self::PLUGIN_NAME;
    }

    /**
     * Register the plugin on the workflow environment
     *
     * @param Environment $workflowEnv
     * @return void
     */
    public function registerOn(Environment $workflowEnv)
    {
        $workflowEnv->getWorkflowEngine()->attachPluginToAllChannels($this);
    }

    /**
     * @param ActionEventDispatcher $events
     *
     * @return void
     */
    public function attach(ActionEventDispatcher $events)
    {
        $this->trackHandler($events->attachListener(MessageDispatch::INITIALIZE, [$this, 'onInitializeMessageDispatch']));
        $this->trackHandler($events->attachListener(MessageDispatch::FINALIZE, [$this, 'onFinalizeMessageDispatch']));
    }

    public function onInitializeMessageDispatch(MessageDispatch $messageDispatch)
    {
        if ($messageDispatch instanceof CommandDispatch) {
            $this->onInitializeCommandDispatch($messageDispatch);
        } else {
            $this->onInitializeEventDispatch($messageDispatch);
        }
    }

    public function onFinalizeMessageDispatch(MessageDispatch $messageDispatch)
    {
        if ($messageDispatch instanceof CommandDispatch) {
            $this->onFinalizeCommandDispatch($messageDispatch);
        } else {
            $this->onFinalizeEventDispatch($messageDispatch);
        }
    }

    /**
     * @param CommandDispatch $commandDispatch
     */
    public function onInitializeCommandDispatch(CommandDispatch $commandDispatch)
    {
        $this->tryLogMessage($commandDispatch->getCommand());
    }

    /**
     * @param EventDispatch $eventDispatch
     */
    public function onInitializeEventDispatch(EventDispatch $eventDispatch)
    {
        $this->tryLogMessage($eventDispatch->getEvent());
    }

    /**
     * @param CommandDispatch $commandDispatch
     */
    public function onFinalizeCommandDispatch(CommandDispatch $commandDispatch)
    {
        if ($ex = $commandDispatch->getException()) {
            $successfulLogged = $this->logMessageProcessingFailed($commandDispatch->getCommand(), $ex);

            if ($successfulLogged) {
                $commandDispatch->setException(null);
            }
        } else {
            $this->logMessageProcessingSucceed($commandDispatch->getCommand());
        }
    }

    public function onFinalizeEventDispatch(EventDispatch $eventDispatch)
    {
        if ($ex = $eventDispatch->getException()) {
            $successfulLogged = $this->logMessageProcessingFailed($eventDispatch->getEvent(), $ex);

            if ($successfulLogged) {
                $eventDispatch->setException(null);
            }
        } else {
            $this->logMessageProcessingSucceed($eventDispatch->getEvent());
        }
    }

    /**
     * Message is only logged if it is has a valid type and is not logged already
     * otherwise it is ignored.
     *
     * @param $message
     */
    private function tryLogMessage($message)
    {
        $messageId = null;

        if ($message instanceof RemoteMessage) $messageId = $message->header()->uuid();
        elseif ($message instanceof ProcessingMessage) $messageId = $message->uuid();

        if (! $messageId) return;

        $entry = $this->messageLogger->getEntryForMessageId($messageId);

        if ($entry) return;

        $this->messageLogger->logIncomingMessage($message);
    }

    /**
     * @param $message
     * @param \Exception $ex
     * @return bool if logging was successful
     */
    private function logMessageProcessingFailed($message, \Exception $ex)
    {
        $entry = $this->getLogEntryForMessage($message);

        if (!$entry) return false;

        if (!$entry->status()->isPending()) return false;

        try {
            $this->messageLogger->logMessageProcessingFailed($entry->messageId(), (string)$ex);
        } catch (\Exception $newEx) {
            return false;
        }

    }

    private function logMessageProcessingSucceed($message)
    {
        $entry = $this->getLogEntryForMessage($message);

        if (!$entry) return;

        if (! $entry->status()->isPending()) return;

        $this->messageLogger->logMessageProcessingSucceed($entry->messageId());
    }

    /**
     * @param $message
     * @return null|MessageLogEntry
     */
    private function getLogEntryForMessage($message)
    {
        $messageId = null;

        if ($message instanceof RemoteMessage) $messageId = $message->header()->uuid();
        elseif ($message instanceof WorkflowMessage) $messageId = $message->uuid();
        elseif ($message instanceof LogMessage) $messageId = $message->uuid();
        elseif ($message instanceof StartSubProcess) $messageId = $message->uuid();
        elseif ($message instanceof SubProcessFinished) $messageId = $message->uuid();

        if (! $messageId) return null;

        $entry = $this->messageLogger->getEntryForMessageId($messageId);

        if (!$entry) return null;

        return $entry;
    }
}
 