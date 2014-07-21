<?php

/*
 * This file is part of KoolKode BPMN.
*
* (c) Martin Schröder <m.schroeder2007@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace KoolKode\BPMN\Command;

use KoolKode\BPMN\CommandContext;
use KoolKode\BPMN\Event\MessageThrownEvent;
use KoolKode\Util\Uuid;

class ThrowMessageCommand extends AbstractCommand
{
	protected $executionId;
	
	protected $activityId;
	
	protected $payload;
	
	public function __construct(UUID $executionId, $activityId, array $payload = NULL)
	{
		$this->executionId = $executionId;
		$this->activityId = (string)$activityId;
		$this->payload = $payload;
	}
	
	public function getPriority()
	{
		return self::PRIORITY_DEFAULT - 500;
	}
	
	public function execute(CommandContext $context)
	{
		$execution = $context->getProcessEngine()
							 ->getRuntimeService()
							 ->createExecutionQuery()
							 ->executionId($this->executionId)
							 ->findOne();
		
		$context->notify(new MessageThrownEvent(
			$execution,
			$this->activityId,
			$this->payload,
			$context->getProcessEngine()
		));
	}
}
