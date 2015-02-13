<?php

/*
 * This file is part of KoolKode BPMN.
 *
 * (c) Martin Schröder <m.schroeder2007@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KoolKode\BPMN\Job\Executor;

use KoolKode\BPMN\Job\Handler\JobHandlerInterface;
use KoolKode\BPMN\Job\Job;

/**
 * Contract for the BPMN job scheduler.
 * 
 * @author Martin Schröder
 */
interface JobExecutorInterface
{
	/**
	 * Check if a job handler of the given type is registered.
	 * 
	 * @param string $type
	 * @return boolean
	 */
	public function hasJobHandler($type);
	
	/**
	 * Register a job handler with the executor.
	 * 
	 * @param JobHandlerInterface $handler
	 */
	public function registerJobHandler(JobHandlerInterface $handler);
	
	/**
	 * Execute the given job using the process engine.
	 * 
	 * @param Job $job
	 */
	public function executeJob(Job $job);
	
	// TODO: Split the scheduling part into another component.
	
	/**
	 * Schedule a job for execution.
	 * 
	 * @param Job $job
	 */
	public function scheduleJob(Job $job);
}
