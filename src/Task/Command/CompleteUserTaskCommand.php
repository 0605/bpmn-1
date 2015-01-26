<?php

/*
 * This file is part of KoolKode BPMN.
*
* (c) Martin Schröder <m.schroeder2007@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace KoolKode\BPMN\Task\Command;

use KoolKode\BPMN\Engine\AbstractBusinessCommand;
use KoolKode\BPMN\Engine\ProcessEngine;
use KoolKode\BPMN\Engine\SerializableBusinessCommandInterface;
use KoolKode\BPMN\Runtime\Command\SignalExecutionCommand;
use KoolKode\BPMN\Task\Event\UserTaskCompletedEvent;
use KoolKode\Util\UUID;

/**
 * Completes a user task and signals process execution to continue.
 * 
 * @author Martin Schröder
 */
class CompleteUserTaskCommand extends AbstractBusinessCommand implements SerializableBusinessCommandInterface
{
	protected $taskId;
	
	protected $variables;
	
	public function __construct(UUID $taskId, array $variables = [])
	{
		$this->taskId = $taskId;
		$this->variables = $variables;
	}
	
	public function executeCommand(ProcessEngine $engine)
	{
		$task = $engine->getTaskService()
					   ->createTaskQuery()
					   ->taskId($this->taskId)
					   ->findOne();
		
		$engine->notify(new UserTaskCompletedEvent($task, $engine));
		
		$sql = "	DELETE FROM `#__bpmn_user_task`
					WHERE `id` = :id
		";
		$stmt = $engine->prepareQuery($sql);
		$stmt->bindValue('id', $this->taskId);
		$stmt->execute();
		
		$engine->debug('Completed user task "{task}" with id {id}', [
			'task' => $task->getName(),
			'id' => (string)$task->getId()
		]);
		
		$executionId = $task->getExecutionId();
		
		if($executionId !== NULL)
		{
			$execution = $engine->findExecution($task->getExecutionId());
			
			$engine->pushCommand(new SignalExecutionCommand($execution, NULL, $this->variables));
		}
	}
}