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
 * Tests against existing section names.
 *
 * @since   4.6.0
 * @package Validator
 */

namespace Textpattern\Validator;

class SectionConstraint extends ChoiceConstraint
{
    /**
     * Constructor.
     *
     * @param mixed $value
     * @param array $options
     */

    public function __construct($value, $options = array())
    {
        static $choices = null;

        if (null === $choices) {
            $choices = safe_column('name', 'txp_section', '1=1');
        }

        $options['choices'] = $choices;
        $options['message'] = 'unknown_section';
        parent::__construct($value, $options);
    }
}
