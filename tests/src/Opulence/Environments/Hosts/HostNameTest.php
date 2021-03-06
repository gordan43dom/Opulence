<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Environments\Hosts;

/**
 * Tests the host name
 */
class HostNameTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests getting the value
     */
    public function testGettingValue()
    {
        $host = new HostName("localhost");
        $this->assertEquals("localhost", $host->getValue());
    }
}