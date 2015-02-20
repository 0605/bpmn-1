<?php

/*
 * This file is part of KoolKode BPMN.
*
* (c) Martin Schröder <m.schroeder2007@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace KoolKode\BPMN\Runtime\Command;

use KoolKode\BPMN\Engine\AbstractBusinessCommand;
use KoolKode\BPMN\Engine\ProcessEngine;
use KoolKode\Util\UUID;

/**
 * Populates a local variable in a target execution.
 * 
 * @author Martin Schröder
 */
class SetExecutionVariableCommand extends AbstractBusinessCommand
{
	protected $executionId;
	protected $variableName;
	protected $variableValue;
	
	public function __construct(UUID $executionId, $variableName, $variableValue)
	{
		$this->executionId = $executionId;
		$this->variableName = (string)$variableName;
		$this->variableValue = serialize($variableValue);
	}
	
	/**
	 * {@inheritdoc}
	 *
	 * @codeCoverageIgnore
	 */
	public function isSerializable()
	{
		return true;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function executeCommand(ProcessEngine $engine)
	{
		$execution = $engine->findExecution($this->executionId);
		$execution->setVariable($this->variableName, unserialize($this->variableValue));
	}
}
