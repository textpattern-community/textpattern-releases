 <?php

	include './config.php'; 
	include $txpcfg['txpath'].'/publish.php'; 

	safe_delete("txp_category","name=''");
	safe_delete("txp_category","name=' '");

	$txpcat = getThings('describe '.PFX.'txp_category');
	
	if (!in_array('parent',$txpcat)) {
		safe_alter("txp_category", "add `parent` varchar(64) not null default ''");
	}

	if (!in_array('lft',$txpcat)) {
		safe_alter("txp_category", "add `lft` int(6) not null default '0'");
	}

	if (!in_array('rgt',$txpcat)) {
		safe_alter("txp_category", "add `rgt` int(6) not null default '0'");
	}

	if (in_array('level',$txpcat)) {
		safe_alter("txp_category", "drop `level`");
	}

	$txp = getThings('describe '.PFX.'textpattern');

	if (!in_array('Keywords',$txp)) {
		safe_alter("textpattern", "add `Keywords` varchar(255) not null");
	}
	
	if (in_array('Listing1',$txp) && !in_array('textile_body',$txp)) {
		safe_alter("textpattern",
						"change Listing1 textile_body INT(2) DEFAULT '1' NOT NULL");
	}

	if (in_array('Listing2',$txp) && !in_array('textile_excerpt',$txp)) {
		safe_alter("textpattern",
						"change Listing2 textile_excerpt INT(2) DEFAULT '1' NOT NULL");
	}

	if (!in_array('url_title',$txp)) {
		safe_alter("textpattern", "add `url_title` varchar(255) not null");
	}

	if (!in_array('Excerpt',$txp)) {
		safe_alter("textpattern", "add `Excerpt` mediumtext not null after `Body_html`");
	}


	$txpsect = getThings('describe '.PFX.'txp_section');

	if (!in_array('searchable',$txpsect)) {
		safe_alter("txp_section", "add `searchable` int(2) not null default 1");
	}

	$txp2 = getThings('describe '.PFX.'textpattern');

	if (in_array('Keywords',$txp2)) {
		echo graf("table textpattern 'Keywords' looks good");
	} else echo graf("error: missing field 'Keywords' in textpattern");

	if (in_array('Excerpt',$txp2)) {
		echo graf("table textpattern 'Excerpt' looks good");
	} else echo graf("error: missing field 'Excerpt' in textpattern");

	
	$txpcat2 = getThings('describe '.PFX.'txp_category');

	if (in_array('lft',$txpcat2)) {
		echo graf("table txp_category 'lft' looks good");
	} else echo graf("error: missing field 'lft' in txp_category");

	if (in_array('rgt',$txpcat2)) {
		echo graf("table txp_category 'rgt' looks good");
	} else echo graf("error: missing field 'rgt' in txp_category");

	if (!in_array('level',$txpcat2)) {
		echo graf("table txp_category 'level' looks good");
	} else echo graf("error: deprecated field 'level' in txp_category");

	if (in_array('parent',$txpcat2)) {
		echo graf("table txp_category 'parent' looks good");
	} else echo graf("error: missing field 'parent' in txp_category");

	$txpsect2 = getThings('describe '.PFX.'txp_section');

	if (in_array('searchable',$txpsect2)) {
		echo graf("table txp_section 'parent' looks good");
	} else echo graf("error: missing field 'searchable' in txp_section");

	
	
	safe_update("txp_category", "lft=0,rgt=0","name!='root'");

	safe_delete("txp_category", "name='root'");

	safe_update("txp_category", "parent='root'","parent = ''");

	safe_insert("txp_category", "name='root',parent='',type='article',lft=1,rgt=0");

	rebuild_tree('root',1,'article');

	safe_insert("txp_category", "name='root',parent='',type='link',lft=1,rgt=0");

	rebuild_tree('root',1,'link');

	safe_insert("txp_category", "name='root',parent='',type='image',lft=1,rgt=0");

	rebuild_tree('root',1,'image');
?>
