<?php
/*
* This file is part of prooph/link.
 * (c) prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * 
 * Date: 10.01.15 - 12:35
 */

namespace ProophTest\Link\ProcessorProxy\ProophPlugin;

use Prooph\Processing\Message\WorkflowMessage;
use Prooph\Link\ProcessorProxy\Command\ForwardHttpMessage;
use Prooph\Link\ProcessorProxy\ProophPlugin\ServiceBusMessageExtractor;
use ProophTest\Link\ProcessorProxy\TestCase;
use ProophTest\Link\ProcessorProxy\DataType\TestUser;

/**
 * Class ServiceBusMessageExtractorTest
 *
 * @package ProcessorProxyTest\ProophPlugin
 * @author Alexander Miertsch <kontakt@codeliner.ws>
 */
final class ServiceBusMessageExtractorTest extends TestCase
{
    /**
     * @test
     */
    public function it_extracts_a_service_bus_message_from_a_forward_message_command()
    {
        $wfMessage = WorkflowMessage::collectDataOf(TestUser::prototype(), 'test-case', 'localhost');

        $sbMessage = $wfMessage->toServiceBusMessage();

        $forwardMessage = ForwardHttpMessage::createWith($sbMessage);

        $messageExtractor = new ServiceBusMessageExtractor();

        $this->assertTrue($messageExtractor->canTranslateToMessage($forwardMessage));

        $this->assertSame($sbMessage, $messageExtractor->translateToMessage($forwardMessage));
    }
}
 