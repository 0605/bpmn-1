<?php

/*
 * This file is part of KoolKode BPMN.
 *
 * (c) Martin Schröder <m.schroeder2007@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KoolKode\BPMN\Task;

use KoolKode\Util\UUID;

/**
 * Represents a user task instance.
 * 
 * @author Martin Schröder
 */
interface TaskInterface
{
    /**
     * Get the unique identifier of this user task instance.
     * 
     * @return UUID
     */
    public function getId();

    /**
     * Get the unique identifier of the execution that triggered the task instance.
     * 
     * @return UUID or null when the task is not related to an execution.
     */
    public function getExecutionId();

    /**
     * Get the process instance ID that create the task.
     * 
     * @return UUID or null hen the task is not related to a process instance.
     */
    public function getProcessInstanceId();

    /**
     * Get the business key of the process that spawned the task.
     * 
     * @return string Business key or null when no such key exists.
     */
    public function getProcessBusinessKey();

    /**
     * Get the name (as defined in a BPMN 2.0 process diagram) of the activity to be performed.
     * 
     * @return string
     */
    public function getName();

    /**
     * Get the documentation of the task (will contain text-only).
     * 
     * @return string
     */
    public function getDocumentation();

    /**
     * Get the identifier (as defined by the "id" attribute in a BPMN 2.0 diagram) of the
     * activity to be performed.
     * 
     * @return string ID or null if the task is not related to an execution.
     */
    public function getDefinitionKey();

    /**
     * Get the time of creation of this activity instance.
     * 
     * @return \DateTimeImmutable
     */
    public function getCreated();

    /**
     * Check if the task has been claimed.
     * 
     * @return boolean
     */
    public function isClaimed();

    /**
     * Get the assignment date of this task.
     * 
     * @return \DateTimeImmutable or null when the task instance has not been claimed yet.
     */
    public function getClaimDate();

    /**
     * Get the identity of the assignee of this task.
     * 
     * @return string or null when the task instance has not been claimed yet.
     */
    public function getAssignee();

    /**
     * Get the task priority, defaults to 0.
     * 
     * @return integer
     */
    public function getPriority();

    /**
     * Check if the task has a due date set.
     * 
     * @return boolean
     */
    public function hasDueDate();

    /**
     * Get the due date of the task.
     * 
     * @return \DateTimeImmutable or null when no due date is set.
     */
    public function getDueDate();
}
