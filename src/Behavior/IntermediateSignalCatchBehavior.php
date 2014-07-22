<?php

/*
 * This file is part of KoolKode BPMN.
*
* (c) Martin Schröder <m.schroeder2007@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace KoolKode\BPMN\Behavior;

use KoolKode\BPMN\Command\CreateSignalSubscriptionCommand;
use KoolKode\Process\Behavior\SignalableBehaviorInterface;
use KoolKode\Process\Execution;

class IntermediateSignalCatchBehavior implements SignalableBehaviorInterface
{
	protected $signal;
	
	public function __construct($signal)
	{
		$this->signal = (string)$signal;
	}
	
	public function execute(Execution $execution)
	{
		$execution->waitForSignal();
		$execution->getProcessEngine()->pushCommand(new CreateSignalSubscriptionCommand($this->signal, $execution));
	}
	
	public function signal(Execution $execution, $signal, array $variables = [])
	{
		foreach($variables as $k => $v)
		{
			$execution->setVariable($k, $v);
		}
		
		return $execution->takeAll(NULL, [$execution]);
	}
}
