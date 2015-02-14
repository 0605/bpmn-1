<?php

/*
 * This file is part of KoolKode BPMN.
*
* (c) Martin Schröder <m.schroeder2007@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace KoolKode\BPMN\Delegate\Behavior;

use KoolKode\BPMN\Delegate\DelegateExecution;
use KoolKode\BPMN\Delegate\Event\TaskExecutedEvent;
use KoolKode\BPMN\Engine\AbstractScopeBehavior;
use KoolKode\BPMN\Engine\VirtualExecution;
use KoolKode\Expression\ExpressionInterface;

/**
 * Implements service task behavior using an expression parsed from a BPMN process definition.
 * 
 * @author Martin Schröder
 */
class ExpressionTaskBehavior extends AbstractScopeBehavior
{
	protected $expression;
	
	protected $resultVariable;
	
	public function __construct(ExpressionInterface $expression)
	{
		$this->expression = $expression;
	}
	
	public function setResultVariable($var = NULL)
	{
		$this->resultVariable = ($var === NULL) ? NULL : (string)$var;
	}
	
	public function executeBehavior(VirtualExecution $execution)
	{
		$this->createScopedEventSubscriptions($execution);
		
		$engine = $execution->getEngine();
		$name = $this->getStringValue($this->name, $execution->getExpressionContext());
		
		$engine->debug('Execute expression in service task "{task}"', [
			'task' => $name
		]);
		
		$result = $this->getValue($this->expression, $execution->getExpressionContext());
		
		if($this->resultVariable !== NULL)
		{
			$execution->setVariable($this->resultVariable, $result);
		}
		
		$engine->notify(new TaskExecutedEvent($name, new DelegateExecution($execution), $engine));
		
		$execution->waitForSignal();
		$execution->signal();
	}
}
