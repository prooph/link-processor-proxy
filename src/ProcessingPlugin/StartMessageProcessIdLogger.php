<?php
/*
* This file is part of prooph/link.
 * (c) prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * 
 * Date: 12.01.15 - 00:19
 */

namespace Prooph\Link\ProcessorProxy\ProcessingPlugin;

use Prooph\Common\Event\ActionEvent;
use Prooph\Processing\Environment\Environment;
use Prooph\Processing\Environment\Plugin;
use Prooph\Processing\Processor\ProcessId;
use Prooph\Link\ProcessorProxy\Model\MessageLogger;
use Rhumsaa\Uuid\Uuid;

/**
 * Class StartMessageProcessIdLogger
 *
 * The StartMessageProcessIdLogger listens on Prooph\Processing\Processor\Processor events to be able to log which
 * message has started which process. The information is required by the UI to redirect the user to
 * the process monitor after sending a start message.
 *
 * @package ProcessorProxy\ProcessingPlugin
 * @author Alexander Miertsch <kontakt@codeliner.ws>
 */
final class StartMessageProcessIdLogger implements Plugin
{
    const PLUGIN_NAME = 'prooph.link.processor_proxy.start_message_process_id_logger';

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
        $workflowEnv->getWorkflowProcessor()->events()->attachListener("process_was_started_by_message", [$this, "onProcessWasStartedByMessage"]);
    }

    /**
     * @param ActionEvent $event
     */
    public function onProcessWasStartedByMessage(ActionEvent $event)
    {
        $this->messageLogger->logProcessStartedByMessage(
            ProcessId::fromString($event->getParam('process_id')),
            Uuid::fromString($event->getParam('message_id'))
        );
    }
}
 