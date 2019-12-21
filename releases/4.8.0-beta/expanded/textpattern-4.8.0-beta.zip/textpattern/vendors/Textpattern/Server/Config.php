<?php

/*
 * Textpattern Content Management System
 * https://textpattern.com/
 *
 * Copyright (C) 2019 The Textpattern Development Team
 *
 * This file is part of Textpattern.
 *
 * Textpattern is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation, version 2.
 *
 * Textpattern is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Textpattern. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * Access server configuration variables.
 *
 * <code>
 * Txp::get('\Textpattern\Server\Config')->getVariable('REQUEST_URI');
 * </code>
 *
 * @since   4.6.0
 * @package Server
 */

namespace Textpattern\Server;

class Config
{
    /**
     * Gets a server configuration variable.
     *
     * @param  string $name The variable
     * @return mixed The variable
     */

    public function getVariable($name)
    {
        if (isset($_SERVER[$name])) {
            return $_SERVER[$name];
        }

        return false;
    }
}