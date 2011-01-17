<?php
/*
    This file is part of Erebot.

    Erebot is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Erebot is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Erebot.  If not, see <http://www.gnu.org/licenses/>.
*/

require_once(
    dirname(__FILE__) .
    DIRECTORY_SEPARATOR . 'testenv' .
    DIRECTORY_SEPARATOR . 'bootstrap.php'
);

class   RateLimiterTest
extends ErebotModuleTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->_module = new Erebot_Module_RateLimiter(
            $this->_connection,
            NULL
        );
        $this->_module->reload( Erebot_Module_Base::RELOAD_ALL |
                                Erebot_Module_Base::RELOAD_INIT);
    }

    public function tearDown()
    {
        unset($this->_module);
        parent::tearDown();
    }

    public function testLimit()
    {
        // No more than 2 messages every 2 seconds.
        $this->_serverConfig
            ->expects($this->any())
            ->method('parseInt')
            ->will($this->returnValue(2));

        $this->assertTrue($this->_module->canSend());
        $this->assertTrue($this->_module->canSend());

        // We already sent 2 messages in less than 2 seconds,
        // thus reaching the rate limit.
        // The 3rd message must be rejected.
        $this->assertFalse($this->_module->canSend());

        sleep(2);

        // After we wait for long enough, new messages can be sent.
        $this->assertTrue($this->_module->canSend());
        $this->assertTrue($this->_module->canSend());

        // Again with the 2 messages every 2 seconds limit.
        $this->assertFalse($this->_module->canSend());
    }

    /**
     * @expectedException Erebot_InvalidValueException
     */
    public function testInvalidLimit()
    {
        // Set an invalid limit.
        $this->_serverConfig
            ->expects($this->any())
            ->method('parseInt')
            ->will($this->onConsecutiveCalls(0, 1));
        $this->_module->canSend();
    }

    /**
     * @expectedException Erebot_InvalidValueException
     */
    public function testInvalidPeriod()
    {
        // Set an invalid period.
        $this->_serverConfig
            ->expects($this->any())
            ->method('parseInt')
            ->will($this->onConsecutiveCalls(1, 0));
        $this->_module->canSend();
    }
}

