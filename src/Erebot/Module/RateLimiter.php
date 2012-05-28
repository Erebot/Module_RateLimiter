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

/**
 * \brief
 *      A module that can limit the rate of messages
 *      sent to the IRC server.
 *
 * This module implements a simple rate-limiting strategy,
 * allowing up to N messages to be sent in M seconds, where
 * N (limit) and M (period) can both be configured.
 */
class       Erebot_Module_RateLimiter
extends     Erebot_Module_Base
implements  Erebot_Interface_RateLimiter
{
    /**
     * \brief
     *      A list of timestamp of messages recently sent to the IRC server.
     *
     * Each time this module allows a message to be sent,
     * it keeps track of the time it did so.
     * Up to "limit" (see configuration) timestamps are kept.
     * Next time the bot tries to send a message, this module
     * will look at how much messages have already been sent
     * since the last "period" seconds and act accordingly.
     */
    protected $_queue;

    /**
     * This method is called whenever the module is (re)loaded.
     *
     * \param int $flags
     *      A bitwise OR of the Erebot_Module_Base::RELOAD_*
     *      constants. Your method should take proper actions
     *      depending on the value of those flags.
     *
     * \note
     *      See the documentation on individual RELOAD_*
     *      constants for a list of possible values.
     */
    public function _reload($flags)
    {
        if ($flags & self::RELOAD_HANDLERS) {
            $cls = $this->getFactory('!Callable');
            $this->registerHelpMethod(new $cls(array($this, 'getHelp')));
        }

        if ($this->_channel !== NULL)
            return;

        if ($flags & self::RELOAD_MEMBERS) {
            $this->_queue = array();
        }
    }

    /// \copydoc Erebot_Module_Base::_unload()
    protected function _unload()
    {
    }

    /**
     * Provides help about this module.
     *
     * \param Erebot_Interface_Event_Base_TextMessage $event
     *      Some help request.
     *
     * \param Erebot_Interface_TextWrapper $words
     *      Parameters passed with the request. This is the same
     *      as this module's name when help is requested on the
     *      module itself (in opposition with help on a specific
     *      command provided by the module).
     */
    public function getHelp(
        Erebot_Interface_Event_Base_TextMessage $event,
        Erebot_Interface_TextWrapper            $words
    )
    {
        if ($event instanceof Erebot_Interface_Event_Base_Private) {
            $target = $event->getSource();
            $chan   = NULL;
        }
        else
            $target = $chan = $event->getChan();

        $fmt        = $this->getFormatter($chan);
        $moduleName = strtolower(get_class());
        $nbArgs     = count($words);

        if ($nbArgs == 1 && $words[0] == $moduleName) {
            $msg = $fmt->_(
                "This module does not provide any command but is used ".
                "by the bot to control the rate at which it sends messages ".
                "to IRC servers (bandwidth throttling)."
            );
            $this->sendMessage($target, $msg);
            return TRUE;
        }
    }

    /// \copydoc Erebot_Interface_RateLimiter::canSend()
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

