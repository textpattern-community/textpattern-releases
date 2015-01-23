 <?php

	include './config.php'; 
	include $txpcfg['txpath'].'/publish.php'; 

	safe_delete("txp_category","name=''");
	safe_delete("txp_category","name=' '");

	$txpcat = getThings('describe '.PFX.'txp_category');

	if (!in_array('id',$txpcat)) 
	{
		safe_alter('txp_category', 
			'add `id` int(6) NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST');
	}

	if (!in_array('parent',$txpcat)) 
	{
		safe_alter("txp_category", "add `parent` varchar(64) not null default ''");
	}

	if (!in_array('lft',$txpcat)) 
	{
		safe_alter("txp_category", "add `lft` int(6) not null default '0'");
	}

	if (!in_array('rgt',$txpcat)) 
	{
		safe_alter("txp_category", "add `rgt` int(6) not null default '0'");
	}

	if (in_array('level',$txpcat)) 
	{
		safe_alter("txp_category", "drop `level`");
	}


	$txp = getThings('describe '.PFX.'textpattern');

	if (!in_array('Keywords',$txp)) 
	{
		safe_alter("textpattern", "add `Keywords` varchar(255) not null");
	}
	
	if (in_array('Listing1',$txp) && !in_array('textile_body',$txp)) 
	{
		safe_alter("textpattern",
						"change Listing1 textile_body INT(2) DEFAULT '1' NOT NULL");
	}

	if (in_array('Listing2',$txp) && !in_array('textile_excerpt',$txp)) 
	{
		safe_alter("textpattern",
						"change Listing2 textile_excerpt INT(2) DEFAULT '1' NOT NULL");
	}

	if (!in_array('url_title',$txp)) 
	{
		safe_alter("textpattern", "add `url_title` varchar(255) not null");
	}

	if (!in_array('Excerpt',$txp)) 
	{
		safe_alter("textpattern", "add `Excerpt` mediumtext not null after `Body_html`");
	}


	// custom fields added for g1.19

	if (!in_array('custom_1',$txp)) {
		safe_alter("textpattern", "add `custom_1` varchar(255) not null");
	}

	if (!in_array('custom_2',$txp)) {
		safe_alter("textpattern", "add `custom_2` varchar(255) not null");
	}

	if (!in_array('custom_3',$txp)) {
		safe_alter("textpattern", "add `custom_3` varchar(255) not null");
	}

	if (!in_array('custom_4',$txp)) {
		safe_alter("textpattern", "add `custom_4` varchar(255) not null");
	}

	if (!in_array('custom_5',$txp)) {
		safe_alter("textpattern", "add `custom_5` varchar(255) not null");
	}

	if (!in_array('custom_6',$txp)) {
		safe_alter("textpattern", "add `custom_6` varchar(255) not null");
	}

	if (!in_array('custom_7',$txp)) {
		safe_alter("textpattern", "add `custom_7` varchar(255) not null");
	}

	if (!in_array('custom_8',$txp)) {
		safe_alter("textpattern", "add `custom_8` varchar(255) not null");
	}

	if (!in_array('custom_9',$txp)) {
		safe_alter("textpattern", "add `custom_9` varchar(255) not null");
	}

	if (!in_array('custom_10',$txp)) {
		safe_alter("textpattern", "add `custom_10` varchar(255) not null");
	}



	$txpsect = getThings('describe '.PFX.'txp_section');

	if (!in_array('searchable',$txpsect)) 
	{
		safe_alter("txp_section", "add `searchable` int(2) not null default 1");
	}

	$txp2 = getThings('describe '.PFX.'textpattern');

	if (in_array('Keywords',$txp2)) 
	{
		echo graf("table textpattern 'Keywords' looks good");
	} else echo graf("error: missing field 'Keywords' in textpattern");

	if (in_array('Excerpt',$txp2)) 
	{
		echo graf("table textpattern 'Excerpt' looks good");
	} else echo graf("error: missing field 'Excerpt' in textpattern");

	
	$txpcat2 = getThings('describe '.PFX.'txp_category');

	if (in_array('lft',$txpcat2)) 
	{
		echo graf("table txp_category 'lft' looks good");
	} else echo graf("error: missing field 'lft' in txp_category");

	if (in_array('rgt',$txpcat2)) 
	{
		echo graf("table txp_category 'rgt' looks good");
	} else echo graf("error: missing field 'rgt' in txp_category");

	if (!in_array('level',$txpcat2)) 
	{
		echo graf("table txp_category 'level' looks good");
	} else echo graf("error: deprecated field 'level' in txp_category");

	if (in_array('parent',$txpcat2)) 
	{
		echo graf("table txp_category 'parent' looks good");
	} else echo graf("error: missing field 'parent' in txp_category");

	$txpsect2 = getThings('describe '.PFX.'txp_section');

	if (in_array('searchable',$txpsect2)) 
	{
		echo graf("table txp_section 'parent' looks good");
	} else echo graf("error: missing field 'searchable' in txp_section");


	$popcom = fetch("*",'txp_form','name',"popup_comments");
	
	if (!$popcom) {
	
		$popform = <<<eod
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<link rel="Stylesheet" href="<txp:css />" type="text/css" />
	<title><txp:page_title /></title>
</head>
<body>
<div style="text-align: left; padding: 1em; width:300px">

	<txp:popup_comments />

	</div>
</body>
</html>
eod;
		$popform = addslashes($popform);
		safe_insert("txp_form","name='popup_comments',type='comment',Form='$popform'");
	}
	
	safe_update("txp_category", "lft=0,rgt=0","name!='root'");

	safe_delete("txp_category", "name='root'");

	safe_update("txp_category", "parent='root'","parent = ''");

	safe_insert("txp_category", "name='root',parent='',type='article',lft=1,rgt=0");

	rebuild_tree('root',1,'article');

	safe_insert("txp_category", "name='root',parent='',type='link',lft=1,rgt=0");

	rebuild_tree('root',1,'link');

	safe_insert("txp_category", "name='root',parent='',type='image',lft=1,rgt=0");

	rebuild_tree('root',1,'image');
	
	safe_insert('txp_prefs', "prefs_id=1, name='version',val='g1.19'");

?>
