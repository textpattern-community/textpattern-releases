<?php

/*
	This is Textpattern

	Copyright 2004 by Dean Allen
	www.textpattern.com
	All rights reserved

	Use of this software indicates acceptance of the Textpattern license agreement 
*/
	require './config.php';
	$txpath = $txpcfg['txpath'];

	define('txp_version', '&#947;1.18');
	define('txp_build', '11 Apr 04');

	if (isset($_POST['preview'])) {
		include $txpath.'/publish.php';
		textpattern();
		exit;
	}

//	error_reporting(E_ALL);

	include $txpath.'/lib/txplib_db.php';
	include $txpath.'/lib/txplib_forms.php';
	include $txpath.'/lib/txplib_html.php';
	include $txpath.'/lib/txplib_misc.php';
	include $txpath.'/lib/admin_config.php';

	$microstart = getmicrotime();

#	define("LANG",$language);
	define("LANG","en-gb");

	extract(get_prefs());

	$textarray = load_lang(LANG);

	include $txpath.'/include/txp_auth.php';
	include $txpath.'/lib/txplib_head.php';

	$event = gps('event');
	$step = gps('step');

	include (!$event) 
	?	$txpath.'/include/txp_article.php'
	:	$txpath.'/include/txp_'.$event.'.php';

	$microdiff = (getmicrotime() - $microstart);
	echo "\n<!-- Runtime: ",substr($microdiff,0,6),"-->";

	end_page();
?>
