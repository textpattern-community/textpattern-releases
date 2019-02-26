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
 * Template partials tags.
 *
 * @since  4.6.0
 */

namespace Textpattern\Tag\Syntax;

class Partial
{
    /**
     * Returns the inner content of the enclosing &lt;txp:output_form /&gt; tag.
     *
     * @param  array  $atts
     * @param  string $thing
     * @return string
     */

    public static function renderYield($atts, $thing = null)
    {
        global $yield, $txp_yield, $txp_atts;

        extract(lAtts(array(
            'name'    => '',
            'default' => null,
        ), $atts));

        if ($name === '') {
            $inner = end($yield);
        } elseif (!empty($txp_yield[$name])) {
            list($inner) = end($txp_yield[$name]);
            $txp_yield[$name][key($txp_yield[$name])][1] = true;
        }

        if (!isset($inner)) {
            $escape = isset($txp_atts['escape']) ? $txp_atts['escape'] : null;
            $inner = isset($default) ?
                ($default === true ? page_url(array('type' => $name, 'escape' => $escape)) : $default) :
                ($thing ? parse($thing) : $thing);
        }

        return $inner;
    }

    /**
     * Conditional for yield.
     *
     * @param  array  $atts
     * @param  string $thing
     * @return string
     */

    public static function renderIfYield($atts, $thing = null)
    {
        global $yield, $txp_yield;

        extract(lAtts(array(
            'name'  => '',
            'value' => null,
        ), $atts));

        if ($name === '') {
            $inner = empty($yield) ? null : end($yield);
        } elseif (empty($txp_yield[$name])) {
            $inner = null;
        } else {
            list($inner) = end($txp_yield[$name]);
        }

        return parse($thing, $inner !== null && ($value === null || (string)$inner === (string)$value || $inner && $value === true));
    }
}
