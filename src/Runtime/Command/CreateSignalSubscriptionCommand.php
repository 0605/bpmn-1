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

use KoolKode\BPMN\Engine\ProcessEngine;

/**
 * Creates a signal event subscription.
 * 
 * @author Martin Schröder
 */
class CreateSignalSubscriptionCommand extends AbstractCreateSubscriptionCommand
{
	/**
	 * {@inheritdoc}
	 */
	protected function getSubscriptionName()
	{
		return 'signal';
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected function getSubscriptionFlag()
	{
		return ProcessEngine::SUB_FLAG_SIGNAL;
	}
}
