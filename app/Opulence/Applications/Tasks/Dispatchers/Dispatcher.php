<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Applications\Tasks\Dispatchers;

use Exception;
use RuntimeException;

/**
 * Defines the task dispatcher
 */
class Dispatcher implements IDispatcher
{
    /** @var array The list of task callbacks */
    private $tasks = [
        "preStart" => [],
        "postStart" => [],
        "preShutdown" => [],
        "postShutdown" => []
    ];

    /**
     * @inheritdoc
     */
    public function dispatch($taskType)
    {
        try {
            foreach ($this->tasks[$taskType] as $task) {
                call_user_func($task);
            }
        } catch (Exception $ex) {
            throw new RuntimeException("Failed to run tasks: {$ex->getMessage()}");
        }
    }

    /**
     * @inheritdoc
     */
    public function registerTask($taskType, callable $task)
    {
        if (!isset($this->tasks[$taskType])) {
            $this->tasks[$taskType] = [];
        }

        $this->tasks[$taskType][] = $task;
    }
}