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

class       Erebot_Module_RateLimiter
extends     Erebot_Module_Base
implements  Erebot_Interface_RateLimiter
{
    protected $_queue;

    public function _reload($flags)
    {
        if ($this->_channel !== NULL)
            return;

        if ($flags & self::RELOAD_MEMBERS) {
            $this->_queue = array();
        }
    }

    protected function _unload()
    {
    }

    public function canSend()
    {
        $time   = time();
        $limit  = $this->parseInt('limit');
        $period = $this->parseInt('period');

        if ($limit < 1 || $period < 1) {
            throw new Erebot_InvalidValueException(
                'Invalid limit or time period'
            );
        }

        // Mark obsolete entries as such.
        $old = $time - $period;
        foreach ($this->_queue as &$value) {
            if ($value < $old)
                $value = NULL;
            // Entries are sorted in ascending order,
            // no need to seek further.
            else
                break;
        }
        unset($value);

        // Remove obsolete entries.
        // The call to array_values prevents indexes
        // from exhausting the key-space.
        $this->_queue = array_values(array_filter($this->_queue));

        if (count($this->_queue) < $limit) {
            $this->_queue[] = $time;
            return TRUE;
        }
        return FALSE;
    }
}

