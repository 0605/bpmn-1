<?php

/*
 * This file is part of KoolKode BPMN.
 *
 * (c) Martin Schröder <m.schroeder2007@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KoolKode\BPMN\Repository;

use KoolKode\BPMN\Engine\AbstractQuery;
use KoolKode\BPMN\Engine\BinaryData;
use KoolKode\BPMN\Engine\ProcessEngine;
use KoolKode\BPMN\Runtime\EventSubscription;
use KoolKode\Database\UUIDTransformer;
use KoolKode\Util\UUID;

/**
 * Query for deployed process definitions.
 * 
 * @author Martin Schröder
 */
class ProcessDefinitionQuery extends AbstractQuery
{
    protected $processDefinitionId;

    protected $processDefinitionKey;

    protected $processDefinitionVersion;

    protected $deploymentId;

    protected $resourceId;

    protected $resourceName;

    protected $latestVersion;

    protected $messageEventSubscriptionNames;

    protected $signalEventSubscriptionNames;

    protected $engine;

    public function __construct(ProcessEngine $engine)
    {
        $this->engine = $engine;
    }

    public function processDefinitionId($processDefinitionId)
    {
        $this->populateMultiProperty($this->processDefinitionId, $processDefinitionId, function ($value) {
            return new UUID($value);
        });
        
        return $this;
    }

    public function processDefinitionKey($key)
    {
        $this->populateMultiProperty($this->processDefinitionKey, $key);
        
        return $this;
    }

    public function processDefinitionVersion($version)
    {
        $this->populateMultiProperty($this->processDefinitionVersion, $version, function ($value) {
            return (int) $value;
        });
        
        return $this;
    }

    public function deploymentId($id)
    {
        $this->populateMultiProperty($this->deploymentId, $id, function ($value) {
            return new UUID($value);
        });
        
        return $this;
    }

    public function resourceId($id)
    {
        $this->populateMultiProperty($this->resourceId, $id, function ($value) {
            return new UUID($value);
        });
        
        return $this;
    }

    public function resourceName($name)
    {
        $this->populateMultiProperty($this->resourceName, $name, function ($name) {
            return (string) $name;
        });
        
        return $this;
    }

    public function latestVersion()
    {
        $this->latestVersion = true;
        
        return $this;
    }

    public function messageEventSubscriptionName($name)
    {
        $this->messageEventSubscriptionNames[] = [];
        $this->populateMultiProperty($this->messageEventSubscriptionNames[count($this->messageEventSubscriptionNames) - 1], $name);
        
        return $this;
    }

    public function signalEventSubscriptionName($name)
    {
        $this->signalEventSubscriptionNames[] = [];
        $this->populateMultiProperty($this->signalEventSubscriptionNames[count($this->signalEventSubscriptionNames) - 1], $name);
        
        return $this;
    }

    public function orderByDeploymentId($ascending = true)
    {
        $this->orderings[] = [
            'd.`id`',
            $ascending ? 'ASC' : 'DESC'
        ];
        
        return $this;
    }

    public function orderByDeploymentName($ascending = true)
    {
        $this->orderings[] = [
            'd.`name`',
            $ascending ? 'ASC' : 'DESC'
        ];
        
        return $this;
    }

    public function orderByDeployed($ascending = true)
    {
        $this->orderings[] = [
            'd.`deployed_at`',
            $ascending ? 'ASC' : 'DESC'
        ];
        
        return $this;
    }

    public function orderByProcessName($ascending = true)
    {
        $this->orderings[] = [
            'p.`name`',
            $ascending ? 'ASC' : 'DESC'
        ];
        
        return $this;
    }

    public function orderByProcessRevision($ascending = true)
    {
        $this->orderings[] = [
            'p.`revision`',
            $ascending ? 'ASC' : 'DESC'
        ];
        
        return $this;
    }

    public function orderByProcessDefinitionId($ascending = true)
    {
        $this->orderings[] = [
            'p.`id`',
            $ascending ? 'ASC' : 'DESC'
        ];
        
        return $this;
    }

    public function orderByProcessDefinitionKey($ascending = true)
    {
        $this->orderings[] = [
            'p.`process_key`',
            $ascending ? 'ASC' : 'DESC'
        ];
        
        return $this;
    }

    public function orderByResourceId($ascending = true)
    {
        $this->orderings[] = [
            'r.`id`',
            $ascending ? 'ASC' : 'DESC'
        ];
        
        return $this;
    }

    public function orderByResourceName($ascending = true)
    {
        $this->orderings[] = [
            'r.`name`',
            $ascending ? 'ASC' : 'DESC'
        ];
        
        return $this;
    }

    public function count()
    {
        $stmt = $this->executeSql(true);
        
        return (int) $stmt->fetchNextColumn(0);
    }

    public function findOne()
    {
        $stmt = $this->executeSql(false, 1);
        $row = $stmt->fetchNextRow();
        
        if ($row === false) {
            throw new \OutOfBoundsException(sprintf('No matching process definition found'));
        }
        
        return $this->unserializeProcessDefinition($row);
    }

    public function findAll()
    {
        $stmt = $this->executeSql(false, $this->limit, $this->offset);
        $result = [];
        
        while ($row = $stmt->fetchNextRow()) {
            $result[] = $this->unserializeProcessDefinition($row);
        }
        
        return $result;
    }

    protected function unserializeProcessDefinition(array $row)
    {
        return new ProcessDefinition($row['id'], $row['process_key'], $row['revision'], unserialize(BinaryData::decode($row['definition'])), $row['name'], new \DateTimeImmutable('@' . $row['deployed_at']), $row['deployment_id'], $row['resource_id']);
    }

    protected function getDefaultOrderBy()
    {
        return [
            'p.`id`',
            'ASC'
        ];
    }

    protected function executeSql($count = false, $limit = 0, $offset = 0)
    {
        if ($count) {
            $fields = 'COUNT(*) AS num';
        } else {
            $fields = 'p.*';
        }
        
        $sql = "
            SELECT $fields
            FROM `#__bpmn_process_definition` AS p
            LEFT JOIN `#__bpmn_deployment` AS d ON (d.`id` = p.`deployment_id`)
            LEFT JOIN `#__bpmn_resource` AS r ON (r.`id` = p.`resource_id`)
        ";
        
        $alias = 1;
        $joins = [];
        $where = [];
        $params = [];
        
        $this->buildPredicate("p.`id`", $this->processDefinitionId, $where, $params);
        $this->buildPredicate("p.`process_key`", $this->processDefinitionKey, $where, $params);
        $this->buildPredicate("p.`revision`", $this->processDefinitionVersion, $where, $params);
        $this->buildPredicate("d.`id`", $this->deploymentId, $where, $params);
        $this->buildPredicate('p.`resource_id`', $this->resourceId, $where, $params);
        $this->buildPredicate('r.`name`', $this->resourceName, $where, $params);
        
        foreach ((array) $this->messageEventSubscriptionNames as $name) {
            $joins[] = "INNER JOIN `#__bpmn_process_subscription` AS s$alias ON (s$alias.`definition_id` = p.`id`)";
            
            $p1 = 'p' . count($params);
            
            $where[] = "s$alias.`flags` = :$p1";
            $params[$p1] = EventSubscription::TYPE_MESSAGE;
            
            $this->buildPredicate("s$alias.`name`", $name, $where, $params);
            
            $alias++;
        }
        
        foreach ((array) $this->signalEventSubscriptionNames as $name) {
            $joins[] = "INNER JOIN `#__bpmn_process_subscription` AS s$alias ON (s$alias.`definition_id` = p.`id`)";
            
            $p1 = 'p' . count($params);
            
            $where[] = "s$alias.`flags` = :$p1";
            $params[$p1] = EventSubscription::TYPE_SIGNAL;
            
            $this->buildPredicate("s$alias.`name`", $name, $where, $params);
            
            $alias++;
        }
        
        if ($this->latestVersion) {
            // Using an anti-join to improve query performance (no need for aggregate functions).
            $joins[] = "
                LEFT JOIN `#__bpmn_process_definition` AS p2 ON (
                    p2.`process_key` = p.`process_key`
                    AND p2.`revision` > p.`revision`
                )
            ";
            $where[] = "p2.`revision` IS NULL";
        }
        
        foreach ($joins as $join) {
            $sql .= ' ' . $join;
        }
        
        if (!empty($where)) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        
        if (!$count) {
            $sql .= $this->buildOrderings();
        }
        
        $stmt = $this->engine->prepareQuery($sql);
        $stmt->bindAll($params);
        $stmt->transform('id', new UUIDTransformer());
        $stmt->transform('deployment_id', new UUIDTransformer());
        $stmt->transform('resource_id', new UUIDTransformer());
        $stmt->setLimit($limit);
        $stmt->setOffset($offset);
        $stmt->execute();
        
        return $stmt;
    }
}
