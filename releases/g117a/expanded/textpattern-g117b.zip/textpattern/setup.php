<?php
/*
	This is Textpattern

	Copyright 2004 by Dean Allen
	www.textpattern.com
	All rights reserved

	Use of this software indicates acceptance of the Textpattern license agreement.
*/

include_once './lib/txplib_html.php';
include_once './lib/txplib_forms.php';
include_once './lib/txplib_misc.php';
print <<<eod
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
			"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>Textpattern &#8250; setup</title>
	<link rel="Stylesheet" href="./textpattern.css" type="text/css" />
	</head>
	<body style="border-top:15px solid #FC3">
	<div align="center">
eod;

	$step = isPost('step');
	switch ($step) {	
		case "": getDbInfo(); break;
		case "getTxpLogin": getTxpLogin(); break;
		case "printConfig": printConfig(); break;
		case "createTxp": createTxp();
	}
?>
</div>
</body>
</html>
<?php

// -------------------------------------------------------------
	function getDbInfo()
	{
		$temp_txpath = dirname(__file__);
		$temp_doc_root = $_SERVER['DOCUMENT_ROOT'];
	  echo '<form action="setup.php" method="post">',
	  	'<table id="setup" cellpadding="0" cellspacing="0" border="0">',
		tr(
			tda(
			  hed('Welcome to Textpattern 1.0',3). 
			  graf('Inevitably, we need a few details:',' style="margin-bottom:3em"').
			  hed('MySQL',3).
			  graf('Note that the database you specify must exist; 
			  		Textpattern won&#8217;t create it for you.')
			,' width="400" height="50" colspan="4", align="left"')
		),
		tr(
			fLabelCell('MySQL login').fInputCell('duser','',1).
			fLabelCell('MySQL password').fInputCell('dpass','',2)
		),
		tr(
			fLabelCell('MySQL server').fInputCell('dhost','localhost',3).
			fLabelCell('MySQL database').fInputCell('ddb','',4)
		),
		tr(tdcs('&nbsp;',4)),
		tr(
			tdcs(
				hed('Site Paths',3).
				graf('Please confirm the following paths to your site root and to the Textpattern directory.'),4)
		),
		tr(
			fLabelCell('Full path to server\'s web root').
				tdcs(fInput('text','doc_root',$temp_doc_root,'edit','','',40).
				popHelp('doc_root'),3)
		),
		tr(
			fLabelCell('Full path to Textpattern').
				tdcs(fInput('text','txpath',$temp_txpath,'edit','','',40).
				popHelp('full_path'),3)
		),
		tr(
			fLabelCell('Secret word (for authentication security)').
				tdcs(fInput('text','secret_word','spinach','edit','','',20),3)
		);
		echo
			tr(
				td().td(fInput('submit','Submit','Next','publish')).td().td()
			);
		echo endTable(),
		sInput('printConfig'),
		'</form>';
	}

// -------------------------------------------------------------
	function printConfig()
	{
		$carry = enumPostItems('ddb','duser','dpass','dhost','txprefix','txpath',
			'doc_root','ftphost','ftplogin','ftpass','ftpath','secret_word');

		$carry['txpath']   = preg_replace("/^(.*)\/$/","$1",$carry['txpath']);
		$carry['doc_root'] = preg_replace("/^(.*)\/$/","$1",$carry['doc_root']);
		$carry['ftpath']   = preg_replace("/^(.*)\/$/","$1",$carry['ftpath']);
		
		extract($carry);

		echo graf("Checking database connection...");
		if (!($mylink = mysql_connect($dhost,$duser,$dpass))){
			exit(graf("Can't connect to the database with the values entered."));
		}
		echo graf('Connected.');
		if (!$mydb = mysql_select_db($ddb)) {
			exit(graf("Database ".strong($ddb)." doesn't exist. Please create it or choose another."));
		}
		echo graf("Using database ".strong($ddb)),
		graf(strong('Before you proceed').', open <code>config.php</code> in the <code>/textpattern/</code> directory and replace its contents with the following:'),

		'<textarea style="width:400px;height:200px" name="config" rows="1" cols="1">',
		makeConfig($carry),
		'</textarea>',
		'<form action="setup.php" method="post">',
		fInput('submit','submit','I did it','smallbox'),
		sInput('getTxpLogin'),hInput('carry',postEncode($carry)),
		'</form>';
	}

// -------------------------------------------------------------
	function getTxpLogin() 
	{
		$carry = isPost('carry');
		extract(postDecode($carry));

		echo '<form action="setup.php" method="post">',
	  	startTable('edit'),
		tr(
			tda(
				graf('Thank you.').
				graf('You are about to create and populate database tables, 
					and register yourself with Textpattern.')
			,' width="400" colspan="2" align="center"')
		),
		tr(
			fLabelCell('Your full name').fInputCell('RealName')
		),
		tr(
			fLabelCell('Choose a login name (basic characters and spaces only please)').fInputCell('name')
		),
		tr(
			fLabelCell('Choose a password').fInputCell('pass')
		),
		tr(
			fLabelCell('Your email address').fInputCell('email')
		),
		tr(
			td().td(fInput('submit','Submit','Next','publish'))
		),
		endTable(),
		sInput('createTxp'),
		hInput('carry',$carry),
		'</form>';
	}

// -------------------------------------------------------------
	function createTxp() 
	{
		$carry = isPost('carry');
		extract(postDecode($carry));
		extract(gpsa(array('name','pass','RealName','email')));
 		include './txpsql.php';

		mysql_query("INSERT INTO txp_users VALUES 		
			(1,'$name',password(lower('$pass')),'$RealName','$email',1,now())");

 		echo fbCreate();
	}


// -------------------------------------------------------------
	function isPost($val)
	{
		if(isset($_POST[$val])) {
			return (get_magic_quotes_gpc()) 
			?	stripslashes($_POST[$val])
			:	$_POST[$val];						
		} 
		return '';
	}

// -------------------------------------------------------------
	function makeConfig($ar) 
	{
		define("nl","';\n");
		define("o",'$txpcfg[\'');
		define("m","'] = '");
		$open = chr(60).'?php';
		$close = '?'.chr(62);
		extract($ar);
		return
		$open."\n".
		o.'db'			.m.$ddb.nl
		.o.'user'		.m.$duser.nl
		.o.'pass'		.m.$dpass.nl
		.o.'host'		.m.$dhost.nl
		.o.'txpath'		.m.$txpath.nl
		.o.'doc_root'	.m.$doc_root.nl
		.o.'secret_word'.m.$secret_word.nl
		.$close;
	}

// -------------------------------------------------------------
	function fbCreate() 
	{
		return <<<text
		<div width="450" valign="top" style="margin-left:auto;margin-right:auto"> 
		<p style="margin-top:3em">That went well. Tables were created and populated.</p>
		<p>You should be able to access the <a href="index.php">main interface</a> with the login and password you chose.</p>
		<h3>This is Important</h3>
		<p>Delete this file, <code>/textpattern/setup.php</code>, from your server</p>
		<p><strong>Do it now</strong>!</p>
		<p>Thank you for your interest in Textpattern.</p>
</div>
text;
	}

// -------------------------------------------------------------
	function postEncode($thing)
	{
		return base64_encode(serialize($thing));
	}

// -------------------------------------------------------------
	function postDecode($thing)
	{
		return unserialize(base64_decode($thing));
	}

// -------------------------------------------------------------
	function enumPostItems() 
	{
		foreach(func_get_args() as $item) { $out[$item] = isPost($item); }
		return $out; 
	}

?>
