 <?php

	include './config.php'; 
	include $txpcfg['txpath'].'/publish.php'; 

	mysql_query("delete from txp_category where name=''");
	mysql_query("delete from txp_category where name=' '");

	$txpcat = getThings('describe txp_category');
	
	if (!in_array('parent',$txpcat)) {
		mysql_query("ALTER TABLE `txp_category` ADD `parent` 
						varchar(64) NOT NULL DEFAULT ''");
	}

	if (!in_array('lft',$txpcat)) {
		mysql_query("alter table txp_category add `lft` int(6) not null default '0'");
	}

	if (!in_array('rgt',$txpcat)) {
		mysql_query("alter table txp_category add `rgt` int(6) not null default '0'");
	}

	if (in_array('level',$txpcat)) {
		mysql_query("alter table txp_category drop `level`");
	}

	mysql_query("delete from txp_category where name='root'");

	mysql_query("update txp_category set 
		parent='root' where name!='root'");

	$txp = getThings('describe textpattern');

	if (!in_array('Keywords',$txp)) {
		mysql_query("alter table `textpattern` add `Keywords` varchar(255) not null");
	}

	if (!in_array('Excerpt',$txp)) {
		mysql_query("alter table `textpattern` 
						add `Excerpt` mediumtext not null after `Body_html`");
	}


	$txpsect = getThings('describe txp_section');

	if (!in_array('searchable',$txpsect)) {
		mysql_query("alter table `txp_section` add `searchable` int(2) not null default 1");
	}

	$txp2 = getThings('describe textpattern');

	if (in_array('Keywords',$txp2)) {
		echo graf("table textpattern 'Keywords' looks good");
	} else echo graf("error: missing field 'Keywords' in textpattern");

	if (in_array('Excerpt',$txp2)) {
		echo graf("table textpattern 'Excerpt' looks good");
	} else echo graf("error: missing field 'Excerpt' in textpattern");

	
	$txpcat2 = getThings('describe txp_category');

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

	$txpsect2 = getThings('describe txp_section');

	if (in_array('searchable',$txpsect2)) {
		echo graf("table txp_section 'parent' looks good");
	} else echo graf("error: missing field 'searchable' in txp_section");

	
	mysql_query("update txp_category set lft=0,rgt=0 where name!='root'");


	mysql_query("insert into txp_category set 
					name='root',parent='',type='article',lft=1,rgt=0");
	rebuild_tree('root',1,'article');

	mysql_query("insert into txp_category set 
					name='root',parent='',type='link',lft=1,rgt=0");
	rebuild_tree('root',1,'link');

	mysql_query("insert into txp_category set 
					name='root',parent='',type='image',lft=1,rgt=0");
	rebuild_tree('root',1,'image');


?>
