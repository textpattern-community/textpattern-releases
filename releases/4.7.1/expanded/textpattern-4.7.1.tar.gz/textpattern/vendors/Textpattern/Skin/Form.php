<?php

/*
 * Textpattern Content Management System
 * https://textpattern.com/
 *
 * Copyright (C) 2018 The Textpattern Development Team
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
 * Form
 *
 * Manages Form.
 *
 * @since   4.7.0
 * @package Skin
 */

namespace Textpattern\Skin {

    class Form extends AssetBase implements FormInterface
    {
        /**
         * {@inheritdoc}
         */

        protected static $dir = 'forms';

        /**
         * {@inheritdoc}
         */

        protected static $subdirField = 'type';

        /**
         * The expected subdirs for this asset type.
         *
         * Note the order of the values is the order the blocks appear in the
         * Presentation->Forms panel.
         *
         * @var array
         */

        protected static $subdirValues = array('article', 'misc', 'category', 'comment', 'file', 'link', 'section');

        /**
         * {@inheritdoc}
         */

        protected static $defaultSubdir = 'misc';

        /**
         * {@inheritdoc}
         */

        protected static $fileContentsField = 'Form';

        /**
         * {@inheritdoc}
         */

        protected static $essential = array(
            array(
                'name' => 'comments',
                'type' => 'comment',
                'Form' => '<!-- Default contents of the comments tag goes here. See https://docs.textpattern.io/tags/comments. -->',
            ),
            array(
                'name' => 'comments_display',
                'type' => 'comment',
                'Form' => '<!-- Default contents of the popup_comments tag goes here. See https://docs.textpattern.io/tags/popup_comments. -->',
            ),
            array(
                'name' => 'comment_form',
                'type' => 'comment',
                'Form' => '<!-- Default contents of the comments_form tag goes here. See https://docs.textpattern.io/tags/comments_form. -->',
            ),
            array(
                'name' => 'default',
                'type' => 'article',
                'Form' => '<!-- Default contents of the article tag goes here. See https://docs.textpattern.io/tags/article. -->',
            ),
            array(
                'name' => 'plainlinks',
                'type' => 'link',
                'Form' => '<!-- Default contents of the linklist tag goes here. See https://docs.textpattern.io/tags/linklist. -->',
            ),
            array(
                'name' => 'files',
                'type' => 'file',
                'Form' => '<!-- Default contents of the file_download tag goes here. See https://docs.textpattern.io/tags/file_download. -->',
            ),
        );

        /**
         * {@inheritdoc}
         */

        public function setInfos(
            $name,
            $type = null,
            $Form = null
        ) {
            $name = $this->setName($name)->getName();

            $this->infos = compact('name', 'type', 'Form');

            return $this;
        }

        /**
         * $defaultSubdir property getter.
         */

        public static function getTypes()
        {
            return static::$subdirValues;
        }
    }
}
