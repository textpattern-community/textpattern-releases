<?php

/*
	This is Textpattern

	Copyright 2005 by Dean Allen
	www.textpattern.com
	All rights reserved

	Use of this software indicates acceptance of the Textpattern license agreement

$HeadURL: https://textpattern.googlecode.com/svn/releases/4.2.0/source/textpattern/index.php $
$LastChangedRevision: 3275 $

*/
	if (@ini_get('register_globals'))
		foreach ( $_REQUEST as $name => $value )
			unset($$name);

	if (!defined('txpath'))
	{
		define("txpath", dirname(__FILE__));
	}

	define("txpinterface", "admin");

	$thisversion = '4.2.0';
	$txp_using_svn = false; // set false for releases

	ob_start(NULL, 2048);
	if (!isset($txpcfg['table_prefix']) && !@include './config.php') {
		ob_end_clean();
		header('HTTP/1.1 503 Service Unavailable');
		exit('config.php is missing or corrupt.  To install Textpattern, visit <a href="./setup/">setup</a>.');
	} else ob_end_clean();

	header("Content-type: text/html; charset=utf-8");
	if (isset($_POST['form_preview'])) {
		include txpath.'/publish.php';
		textpattern();
		exit;
	}

	error_reporting(E_ALL);
	@ini_set("display_errors","1");

	include_once txpath.'/lib/constants.php';
	include txpath.'/lib/txplib_misc.php';
	include txpath.'/lib/txplib_db.php';
	include txpath.'/lib/txplib_forms.php';
	include txpath.'/lib/txplib_html.php';
	include txpath.'/lib/txplib_theme.php';
	include txpath.'/lib/admin_config.php';

	$microstart = getmicrotime();

	 if ($connected && safe_query("describe `".PFX."textpattern`")) {

		$dbversion = safe_field('val','txp_prefs',"name = 'version'");

		// global site prefs
		$prefs = get_prefs();
		extract($prefs);

		if (empty($siteurl))
			$siteurl = $_SERVER['HTTP_HOST'] . rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/');
		if (empty($path_to_site))
			updateSitePath(dirname(dirname(__FILE__)));

		define("LANG",$language);
		//i18n: define("LANG","en-gb");
		define('txp_version', $thisversion);

		if (!defined('PROTOCOL')) {
			switch (serverSet('HTTPS')) {
				case '':
				case 'off': // ISAPI with IIS
					define('PROTOCOL', 'http://');
				break;

				default:
					define('PROTOCOL', 'https://');
				break;
			}
		}

		define("hu",PROTOCOL.$siteurl.'/');
		// v1.0 experimental relative url global
		define("rhu",preg_replace("/https?:\/\/.+(\/.*)\/?$/U","$1",hu));

		if (!empty($locale)) setlocale(LC_ALL, $locale);
		$textarray = load_lang(LANG);

		// init global theme
		$theme = theme::init();

		include txpath.'/include/txp_auth.php';
		doAuth();

		// once more for global plus private prefs
		$prefs = get_prefs();
		extract($prefs);

		$event = (gps('event') ? gps('event') : (!empty($default_event) && has_privs($default_event) ? $default_event : 'article'));
		$step = gps('step');
		$app_mode = gps('app_mode');

		if (!$dbversion or ($dbversion != $thisversion) or $txp_using_svn)
		{
			define('TXP_UPDATE', 1);
			include txpath.'/update/_update.php';
		}

		janitor();

		if (!empty($admin_side_plugins) and gps('event') != 'plugin')
			load_plugins(1);

		// plugins may have altered privilege settings
		if (!gps('event') && !empty($default_event) && has_privs($default_event))
		{
			 $event = $default_event;
		}

		// init private theme
		$theme = theme::init();

		include txpath.'/lib/txplib_head.php';

		// ugly hack, for the people that don't update their admin_config.php
		// Get rid of this when we completely remove admin_config and move privs to db
		if ($event == 'list')
			require_privs('article');
		else
			require_privs($event);

		callback_event($event, $step, 1);

		$inc = txpath . '/include/txp_'.$event.'.php';
		if (is_readable($inc))
			include($inc);

		callback_event($event, $step, 0);

		$microdiff = (getmicrotime() - $microstart);
		echo n.comment(gTxt('runtime').': '.substr($microdiff,0,6));

		end_page();

	} else {
		txp_die('DB-Connect was succesful, but the textpattern-table was not found.',
				'503 Service Unavailable');
	}
?>
