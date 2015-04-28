<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the Predis class
 */
namespace RDev\Redis;
use RDev\Tests\Databases\NoSQL\Redis\Mocks\RDevPredis;

class RDevPredisTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests getting the server
     */
    public function testGettingServer()
    {
        $server = new Server();
        $redis = new RDevPredis($server, new TypeMapper());
        $this->assertSame($server, $redis->getServer());
    }

    /**
     * Tests getting the type mapper
     */
    public function testGettingTypeMapper()
    {
        $server = new Server();
        $typeMapper = new TypeMapper();
        $redis = new RDevPredis($server, $typeMapper);
        $this->assertSame($typeMapper, $redis->getTypeMapper());
    }

    /**
     * Tests selecting the database
     */
    public function testSelectingDatabase()
    {
        $server = new Server();
        $typeMapper = new TypeMapper();
        $redis = new RDevPredis($server, $typeMapper);
        $redis->select(724);
        $this->assertEquals(724, $redis->getServer()->getDatabaseIndex());
    }
} 