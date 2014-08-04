<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a data mapper that uses cache with SQL as a backup
 */
namespace RDev\Models\ORM\DataMappers;
use RDev\Models;
use RDev\Models\Databases\SQL;
use RDev\Models\Exceptions;
use RDev\Models\ORM\Exceptions as ORMExceptions;

abstract class CachedSQLDataMapper implements ICachedSQLDataMapper
{
    /** @var ICacheDataMapper The cache mapper to use for temporary storage */
    protected $cacheDataMapper = null;
    /** @var SQLDataMapper The SQL database data mapper to use for permanent storage */
    protected $sqlDataMapper = null;
    /** @var Models\IEntity[] The list of entities scheduled for insertion */
    protected $scheduledForCacheInsertion = [];
    /** @var Models\IEntity[] The list of entities scheduled for update */
    protected $scheduledForCacheUpdate = [];
    /** @var Models\IEntity[] The list of entities scheduled for deletion */
    protected $scheduledForCacheDeletion = [];

    /**
     * @param mixed $cache The cache object used in the cache data mapper
     * @param SQL\ConnectionPool $connectionPool The connection pool used in the SQL data mapper
     */
    public function __construct($cache, SQL\ConnectionPool $connectionPool)
    {
        $this->setCacheDataMapper($cache);
        $this->setSQLDataMapper($connectionPool);
    }

    /**
     * {@inheritdoc}
     */
    public function add(Models\IEntity &$entity)
    {
        $this->sqlDataMapper->add($entity);
        $this->scheduleForCacheInsertion($entity);
    }

    /**
     * {@inheritdoc}
     */
    public function commit()
    {
        try
        {
            // Insert entities
            foreach($this->scheduledForCacheInsertion as $entity)
            {
                $this->cacheDataMapper->add($entity);
            }

            // Update entities
            foreach($this->scheduledForCacheUpdate as $entity)
            {
                $this->cacheDataMapper->update($entity);
            }

            // Delete entities
            foreach($this->scheduledForCacheDeletion as $entity)
            {
                $this->cacheDataMapper->delete($entity);
            }
        }
        catch(\Exception $ex)
        {
            throw new ORMExceptions\ORMException($ex->getMessage());
        }

        // Clear our schedules
        $this->scheduledForCacheInsertion = [];
        $this->scheduledForCacheUpdate = [];
        $this->scheduledForCacheDeletion = [];
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Models\IEntity &$entity)
    {
        $this->sqlDataMapper->delete($entity);
        $this->scheduleForCacheDeletion($entity);
    }

    /**
     * {$@inheritdoc}
     */
    public function getAll()
    {
        return $this->read("getAll");
    }

    /**
     * {$@inheritdoc}
     */
    public function getById($id)
    {
        return $this->read("getById", [$id]);
    }

    /**
     * {@inheritdoc}
     */
    public function getIdGenerator()
    {
        return $this->sqlDataMapper->getIdGenerator();
    }

    /**
     * Refreshes the data in cache with the data from the SQL data mapper
     *
     * @throws ORMExceptions\ORMException Thrown if there was an error refreshing the cache
     */
    public function refreshCache()
    {
        $this->cacheDataMapper->flush();
        $this->getAll();
    }

    /**
     * {@inheritdoc}
     */
    public function update(Models\IEntity &$entity)
    {
        $this->sqlDataMapper->update($entity);
        $this->scheduleForCacheUpdate($entity);
    }

    /**
     * Sets the cache data mapper to use in this repo
     *
     * @param mixed $cache The cache object used in the data mapper
     */
    abstract protected function setCacheDataMapper($cache);

    /**
     * Sets the SQL data mapper to use in this repo
     *
     * @param SQL\ConnectionPool $connectionPool The connection pool used in the data mapper
     */
    abstract protected function setSQLDataMapper(SQL\ConnectionPool $connectionPool);

    /**
     * Attempts to retrieve an entity(ies) from the cache data mapper before resorting to an SQL database
     *
     * @param string $funcName The name of the method we want to call on our data mappers
     * @param array $getFuncArgs The array of function arguments to pass in to our entity retrieval functions
     * @param bool $addDataToCacheOnMiss True if we want to add the entity from the database to cache in case of a cache miss
     * @param array $setFuncArgs The array of function arguments to pass into the set functions in the case of a cache miss
     * @return Models\IEntity|array|null The entity(ies) if it was found, otherwise null
     */
    protected function read($funcName, array $getFuncArgs = [], $addDataToCacheOnMiss = true, array $setFuncArgs = [])
    {
        // Always attempt to retrieve from cache first
        $data = call_user_func_array([$this->cacheDataMapper, $funcName], $getFuncArgs);

        /**
         * If an entity wasn't returned or the list of entities was empty, we have no way of knowing if they really
         * don't exist or if they're just not in cache
         * So, we must try looking in the SQL data mapper
         */
        if($data === null || $data === [])
        {
            $data = call_user_func_array([$this->sqlDataMapper, $funcName], $getFuncArgs);

            // Try to store the data back to cache
            if($data === null)
            {
                return null;
            }

            if($addDataToCacheOnMiss)
            {
                if(is_array($data))
                {
                    foreach($data as $datum)
                    {
                        call_user_func_array([$this->cacheDataMapper, "add"], array_merge([&$datum], $setFuncArgs));
                    }
                }
                else
                {
                    call_user_func_array([$this->cacheDataMapper, "add"], array_merge([&$data], $setFuncArgs));
                }
            }
        }

        return $data;
    }

    /**
     * Schedules an entity for deletion from cache
     *
     * @param Models\IEntity $entity The entity to schedule
     */
    protected function scheduleForCacheDeletion(Models\IEntity $entity)
    {
        $this->scheduledForCacheDeletion[] = $entity;
    }

    /**
     * Schedules an entity for insertion into cache
     *
     * @param Models\IEntity $entity The entity to schedule
     */
    protected function scheduleForCacheInsertion(Models\IEntity $entity)
    {
        $this->scheduledForCacheInsertion[] = $entity;
    }

    /**
     * Schedules an entity for update in cache
     *
     * @param Models\IEntity $entity The entity to schedule
     */
    protected function scheduleForCacheUpdate(Models\IEntity $entity)
    {
        $this->scheduledForCacheUpdate[] = $entity;
    }
} 