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

function setup_db($cfg = array())
{
    global $txpcfg, $DB, $prefs, $txp_user, $txp_groups;
    global $permlink_mode, $siteurl, $adminurl, $theme_name, $public_themes, $step;
    include_once txpath.'/lib/txplib_db.php';
    include_once txpath.'/lib/admin_config.php';

    $siteurl = rtrim(@$cfg['site']['public_url'], '/');

    if (!preg_match('%^https?://%', $siteurl)) {
        $siteurl = 'http://'.$siteurl;
    }

    if (empty($cfg['site']['admin_url'])) {
        $adminurl = $siteurl.'/textpattern';
    } else {
        $adminurl = rtrim(@$cfg['site']['admin_url'], '/');

        if (!preg_match('%^https?://%', $adminurl)) {
            $adminurl = 'http://'.$adminurl;
        }
    }

    // Determine the mode of permanent links.
    ini_set('default_socket_timeout', 10);
    $s = md5(uniqid(rand(), true));
    $pretext_data = @file("{$siteurl}/{$s}/?txpcleantest=1");

    if (trim(@$pretext_data[0]) == md5("/{$s}/?txpcleantest=1")) {
        $permlink_mode = 'section_title';
    } else {
        $permlink_mode = 'messy';
    }

    // Variable set.
    @define('hu', $siteurl.'/');
    $siteurl = preg_replace('%^https?://%', '', $siteurl);
    $siteurl = str_replace(' ', '%20', $siteurl);
    $theme_name = empty($cfg['site']['admin_theme']) ? 'hive' : $cfg['site']['admin_theme'];

    $Skin = Txp::get('Textpattern\Skin\Skin');

    $setup_path = txpath.DS.'setup';
    $setup_themes_path = $setup_path.DS.'themes';
    $Skin->setDirPath($setup_themes_path);
    $setup_public_themes = $Skin->getUploaded();

    $root_themes_path = txpath.DS.'..'.DS.'themes';
    $Skin->setDirPath($root_themes_path);
    $root_public_themes = $Skin->getUploaded();

    $public_themes = array_merge($root_public_themes, $setup_public_themes);

    if (empty($cfg['site']['public_theme']) || !array_key_exists($cfg['site']['public_theme'], $public_themes)) {
        $public_theme = current(array_keys($public_themes));
    } else {
        $public_theme = $cfg['site']['public_theme'];
    }

    $is_from_setup = in_array($public_theme, array_keys($setup_public_themes));

    if (empty($cfg['site']['content_directory'])) {
        /*  Option 'txp-data' in manifest.json:
            <default>           - Import /articles and /data from setup dir
            txp-data == 'theme' - Import /articles and /data from theme dir
            txp-data == 'none'  - Nothing to import.
        */

        if (@$public_themes[$public_theme]['txp-data'] == 'theme') {
            $datadir = $is_from_setup ? $setup_themes_path.DS.$public_theme : $root_public_themes.DS.$public_theme;
        } elseif (@$public_themes[$public_theme]['txp-data'] == 'none') {
            $datadir = '';
        } else {
            $datadir = $setup_path;
        }
    } else {
        $datadir = $cfg['site']['content_directory'];
    }

    if (numRows(safe_query("SHOW TABLES LIKE '".PFX."textpattern'"))) {
        if (! empty($step)) {
            $step = 'step_printConfig';
            echo txp_setup_progress_meter(4).n.'<div class="txp-setup">';
        }

        msg(gTxt('tables_exist', array('{dbname}' => @$txpcfg['db'])), MSG_ERROR, true);
    }

    $setup = new \Textpattern\DB\Core();

    // Create tables
    $setup->createAllTables();

    // Initial mandatory data
    $setup->initData();
    msg(gTxt('creating_db_tables'));

    setup_txp_lang($cfg['site']['language_code']);

    // Create core prefs
    $setup->initPrefs();

    $prefs = get_prefs();
    $txp_user = $cfg['user']['login_name'];

    create_user($txp_user, $cfg['user']['email'], $cfg['user']['password'], $cfg['user']['full_name'], 1);

    if ($datadir) {
        /*  Load theme prefs:
                /data/core.prefs    - Allow override some core prefs. Used only in setup theme.
                /data/theme.prefs   - Theme global and private prefs.
                                        global  - Used in setup and for AutoCreate missing prefs.
                                        private - Will be created after user login
        */
        foreach (get_files_content($datadir.'/data', 'prefs') as $key => $data) {
            if ($out = @json_decode($data, true)) {
                msg("Prefs: 'data/{$key}'");

                foreach ($out as $name => $p) {
                    if (empty($p['private'])) {
                        @set_pref($name, $p['val'], $p['event'], $p['type'], $p['html'], $p['position']);
                    }
                }
            }
        }

        $prefs = get_prefs();
        $plugin = new \Textpattern\Plugin\Plugin();

        foreach (get_files_content($datadir.'/plugin', 'txt') as $key => $data) {
            $result = $plugin->install($data, 1);
            msg("Plugin: '{$key}' - ".(is_array($result) ? $result[0] : $result));
        }

        $import = new \Textpattern\Import\TxpXML();

        foreach (get_files_content($datadir.'/data', 'xml') as $key => $data) {
            $import->importXml($data);
            msg("Import: 'data/{$key}'");
        }

        foreach (get_files_content($datadir.'/articles', 'xml') as $key => $data) {
            $import->importXml($data);
            msg("Import: 'articles/{$key}'");
        }
    }

    // --- Theme setup.
    // Import theme assets.
    msg(gTxt('public_theme').": '{$public_theme}'");
    $is_from_setup ? $Skin->setDirPath($setup_themes_path) : '';
    $Skin->setNames(array($public_theme))
         ->import()
         ->setBase('default')
         ->updateSections();

    // --- Theme setup end

    // Final rebuild category trees
    rebuild_tree_full('article');
    rebuild_tree_full('link');
    rebuild_tree_full('image');
    rebuild_tree_full('file');
}

/**
 * Installing Language Files
 *
 * @param  string $langs The desired language code or comma separated string
 */

function setup_txp_lang($langs)
{
    global $language;

    Txp::getContainer()->remove('\Textpattern\L10n\Lang');

    $langs = array_flip(do_list_unique($langs));
    unset($langs[$language]);

    if (!Txp::get('\Textpattern\L10n\Lang')->installFile($language)) {
        // If cannot install from lang file, setup the Default lang. `language` pref changed too.
        $language = TEXTPATTERN_DEFAULT_LANG;
        Txp::get('\Textpattern\L10n\Lang')->installFile($language);
        unset($langs[$language]);
    }

    msg("Lang: '{$language}'");

    foreach (array_flip($langs) as $lang) {
        Txp::get('\Textpattern\L10n\Lang')->installFile($lang);
        msg("Lang: '{$lang}'");
    }
}

/**
 * Merge the desired lang strings with fallbacks.
 *
 * The fallback language is guaranteed to exist, so any unknown strings
 * will be used from that pack to fill in any gaps.
 *
 * @param  string $langs The desired language code or comma separated string
 * @return array         The language-specific name-value pairs
 */

function setup_load_lang($langs)
{
    global $language;

    $langs = do_list($langs);
    $lang = $langs[0];
    $lang_path = txpath.DS.'setup'.DS.'lang'.DS;
    $group = 'common, setup';
    $strings = array();

    // Load the desired lang strings and default strings as fallbacks.
    $default_textpack = Txp::get('\Textpattern\L10n\Lang', $lang_path)->getPack(TEXTPATTERN_DEFAULT_LANG, $group);
    $lang_textpack = Txp::get('\Textpattern\L10n\Lang', $lang_path)->getPack($lang, $group);

    $language = empty($lang_textpack) ? TEXTPATTERN_DEFAULT_LANG : $lang;
    @define('LANG', $language);

    $allStrings = array_merge($default_textpack, $lang_textpack);

    // Merge the arrays, using the default language to fill in the blanks.
    foreach ($allStrings as $meta) {
        if (empty($strings[$meta['name']])) {
            $strings[$meta['name']] = $meta['data'];
        }
    }

    Txp::get('\Textpattern\L10n\Lang')->setPack($strings)
        ->setPack(array('help' => gTxt('setup_help')), true);

    return $strings;
}

/**
 * Generate a config.php file from the known info.
 */

function setup_makeConfig($cfg, $doSpecial = false)
{
    define("nl", "';\n");
    define("o", '$txpcfg[\'');
    define("m", "'] = '");

    // Escape single quotes and backslashes in literal PHP strings.
    foreach ($cfg['database'] as $k => $v) {
        $cfg['database'][$k] = addcslashes($cfg['database'][$k], "'\\");
    }

    if ($doSpecial) {
        $cfg['database'] = doSpecial($cfg['database']);
    }

    $config_details =
    "<"."?php\n"
    .o.'db'.m.$cfg['database']['db_name'].nl
    .o.'user'.m.$cfg['database']['user'].nl
    .o.'pass'.m.$cfg['database']['password'].nl
    .o.'host'.m.$cfg['database']['host'].nl
    .(empty($cfg['database']['client_flags']) ? '' : o.'client_flags'."'] = ".$cfg['database']['client_flags'].";\n")
    .o.'table_prefix'.m.$cfg['database']['table_prefix'].nl
    .o.'txpath'.m.txpath.nl
    .o.'dbcharset'.m.$cfg['database']['charset'].nl;

    if (defined('is_multisite')) {
        $config_details .=
             o.'multisite_root_path'.m.multisite_root_path.nl
            .o.'admin_url'.m.$cfg['site']['admin_url'].nl
            .o.'cookie_domain'.m.$cfg['site']['cookie_domain'].nl
            .'if (!defined(\'txpath\')) { define(\'txpath\', $txpcfg[\'txpath\']); }'."\n";
    }

    $config_details .=
    "?".">";

    return $config_details;
}

/**
 * Try to connect to the database.
 *
 * Check the database and the table prefix.
 */

function setup_connect()
{
    global $cfg;

    if (strpos($cfg['database']['host'], ':') === false) {
        $dhost = $cfg['database']['host'];
        $dport = ini_get("mysqli.default_port");
    } else {
        list($dhost, $dport) = explode(':', $cfg['database']['host'], 2);
        $dport = intval($dport);
    }

    $dsocket = ini_get("mysqli.default_socket");

    $mylink = mysqli_init();

    if (@mysqli_real_connect($mylink, $dhost, $cfg['database']['user'], $cfg['database']['password'], '', $dport, $dsocket)) {
        $cfg['database']['client_flags'] = 0;
    } elseif (@mysqli_real_connect($mylink, $dhost, $cfg['database']['user'], $cfg['database']['password'], '', $dport, $dsocket, MYSQLI_CLIENT_SSL)) {
        $cfg['database']['client_flags'] = 'MYSQLI_CLIENT_SSL';
    } else {
        msg(gTxt('db_cant_connect'), MSG_ERROR, true);
    }

    echo msg(gTxt('db_connected'));

    if (!($cfg['database']['table_prefix'] == '' || preg_match('#^[a-zA-Z_][a-zA-Z0-9_]*$#', $cfg['database']['table_prefix']))) {
        msg(gTxt('prefix_bad_characters',
            array('{dbprefix}' => strong(txpspecialchars($cfg['database']['table_prefix']))), 'raw'),
            MSG_ERROR, true
        );
    }

    if (!$mydb = mysqli_select_db($mylink, $cfg['database']['db_name'])) {
        msg(gTxt('db_doesnt_exist',
            array('{dbname}' => strong(txpspecialchars($cfg['database']['db_name']))), 'raw'),
            MSG_ERROR, true
        );
    }

    $tables_exist = mysqli_query($mylink, "DESCRIBE `".$cfg['database']['table_prefix']."textpattern`");
    if ($tables_exist) {
        msg(gTxt('tables_exist',
            array('{dbname}' => strong(txpspecialchars($cfg['database']['db_name']))), 'raw'),
            MSG_ERROR, true
        );
    }

    // On MySQL 5.5.3+ use real UTF-8 tables, if the client supports it.
    $cfg['database']['charset'] = "utf8mb4";

    // Lower versions only support UTF-8 limited to 3 bytes per character
    if (mysqli_get_server_version($mylink) < 50503) {
        $cfg['database']['charset'] = "utf8";
    } else {
        if (false !== strpos(mysqli_get_client_info($mylink), 'mysqlnd')) {
            // mysqlnd 5.0.9+ required
            if (mysqli_get_client_version($mylink) < 50009) {
                $cfg['database']['charset'] = "utf8";
            }
        } else {
            // libmysqlclient 5.5.3+ required
            if (mysqli_get_client_version($mylink) < 50503) {
                $cfg['database']['charset'] = "utf8";
            }
        }
    }
    @mysqli_close($mylink);
    echo msg(gTxt('using_db', array(
        '{dbname}' => strong(txpspecialchars($cfg['database']['db_name'])), ), 'raw').' <bdi dir="ltr">('.$cfg['database']['charset'].')</bdi>');

    return true;
}
