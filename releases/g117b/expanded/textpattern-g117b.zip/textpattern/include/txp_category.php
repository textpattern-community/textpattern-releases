<?php

/*
	This is Textpattern

	Copyright 2004 by Dean Allen
	www.textpattern.com
	All rights reserved

	Use of this software indicates acceptance ofthe Textpattern license agreement 
*/

	if(!$step or !function_exists($step)){
		category_list();
	} else $step();

//-------------------------------------------------------------
	function category_list($message="")
	{
		global $use_sections,$use_categories;
		pagetop(gTxt('categories'),$message);
		$out = array(startTable('edit'),
		'<tr>',
			($use_categories) ? tdtl(article_list()) : '',
			tdtl(link_list()),
			tdtl(image_list()),
		'</tr>',
		endTable());
		echo join('',$out);
	}

 
//-------------------------------------------------------------
	function article_list() 
	{
		global $url_mode,$txpac;
		$headspan = ($txpac['show_article_category_count']) ? 3 : 2;
		$out[] = 
			tr(
				td(strong(gTxt('article_head')).popHelp('article_category')).
				tdcs('&nbsp;',$headspan)
			);
		$out[] = 
			tr(tdcs(form(
				fInput('text','name','','edit','','',10).
				fInput('submit','',gTxt('Create'),'smallerbox').
				eInput('category').
				sInput('article_create')
			),$headspan));

		$rs = getTree('root','article');
	
		if($rs) {
			foreach ($rs as $a) {
				extract($a);
				if ($name=='root') continue;
				if ($txpac['show_article_category_count']) {
					$sname = doSlash($name);
					$count = td(small(getThing("select count(*) from textpattern
							where ((Category1='$sname') or (Category2='$sname'))")));
				} else $count = '';

				$deletelink = dLink('category','article_delete','name',
					$name,'','type','article');
				$editlink = eLink('category','article_edit','name',
					$name,htmlspecialchars($name));

				$out[] = 
				tr( 
					td( 
						str_repeat(sp,$level-1) . $editlink 
					) . 
					$count . 
					td( 
						$deletelink 
					) 
				);

			}
		}
			return startTable('list').join('',$out).endTable();
	}

//-------------------------------------------------------------
	function article_create()
	{
		$name = gps('name');
		$name = trim(doSlash($name));

		$check = getThing("select name from txp_category 
						   where name='$name' and type='article'");

		if (!$check) {
			if($name) {
				$q = safe_query("insert into txp_category 
								 set name='$name',type='article', parent='root'");
				rebuild_tree('root', 1, 'article');
				if ($q) category_list(messenger('article_category',$name,'created'));
			} else {
				category_list();
			}
		} else {
			category_list(messenger('article_category',$name,'already_exists'));		
		}
	}

//-------------------------------------------------------------
	function article_edit()
	{
		pagetop(gTxt('categories'));

		extract(gpsa(array('name','parent')));
		$row = getRow("select * from txp_category where name='$name' and type='article'");
		if($row){
			extract($row);
			$out = stackRows(
				fLabelCell('article_category_name') . fInputCell('name', $name, 1, 20),
				fLabelCell('parent') . td(parent_pop($parent,'article')),
				tdcs(fInput('submit', '', gTxt('save_button')), 2)
			);
		}
		$out.= eInput( 'category' ) . sInput( 'article_save' ) . hInput( 'old_name',$name );
		echo form( startTable( 'edit' ) . $out . endTable() );
	}

//-------------------------------------------------------------
	function article_save()
	{
		$in = gpsa(array('name','old_name','parent'));
		extract(doSlash($in));
		safe_query("update txp_category 
			set name='$name',parent='$parent' where name='$old_name' and type='article'"); 
		rebuild_tree('root', 1, 'article');
		safe_query("update textpattern set Category1='$name' where Category1='$old_name'"); 
		safe_query("update textpattern set Category2='$name' where Category2='$old_name'"); 
		category_list(messenger('article_category',stripslashes($name),'saved'));
	}

//-------------------------------------------------------------
	function article_delete()
	{	
		$name = doSlash(gps('name'));
		$q = safe_query("delete from txp_category where name='$name' and type='article'"); 
		if ($q) category_list(messenger('article_category',$name,'deleted'));
	}


//--------------------------------------------------------------
	function parent_pop($name,$type)
	{
		$rs = getTree("root",$type);
		if ($rs) {
			return ' '.treeSelectInput('parent', $rs, $name);
		}
		return 'no categories created';
	}




// -------------------------------------------------------------
	function link_list() 
	{
		global $url_mode;
		$out[] = 
			tr(
				td(strong(gTxt('link_head')).popHelp('link_category')).
				td()
			);
		$out[] = 
			tr(td(form(
				fInput('text','name','','edit','','',10).
				fInput('submit','',gTxt('Create'),'smallerbox').
				eInput('category').
				sInput('link_create')
			)));
	
		$rs = getTree('root','link');

		if($rs) {
			foreach ($rs as $a) {
				extract($a);
				if($name=='root') continue;
				$deletelink = dLink('category','link_delete','name',
					$name,'','type','link');
				$editlink = eLink('category','link_edit','name',
					$name,htmlspecialchars($name));

				$out[] = tr( td( str_repeat(sp,$level-1).$editlink ) . td( $deletelink ) );
			}
		}
		return startTable('list').join('',$out).endTable();
	}

//-------------------------------------------------------------
	function link_create()
	{
		$name = ps('name');
		$name = trim(doSlash($name));

		$check = getThing("select name from txp_category 
								where name='$name' and type='link'");
		if (!$check) {
			if ($name) {
				safe_query("insert into txp_category 
							set name='$name',type='link',parent='root'"); 
				rebuild_tree('root', 1, 'link');
				category_list(messenger('link_category',$name,'created'));
			} else category_list();
		} else category_list(messenger('link_category',$name,'already_exists'));
	}

//-------------------------------------------------------------
	function link_edit()
	{
		pagetop(gTxt('categories'));
		$name = gps('name');
		extract(getRow("select * from txp_category where name='$name' and type='link'"));
		$out = 
		tr( fLabelCell(gTxt('link_category_name').':').fInputCell('name',$name,1,20)).
		tr( fLabelCell('parent') . td(parent_pop($parent,'link'))).
		tr( tdcs(fInput('submit','', gTxt('save_button')), 2));
		$out.= eInput('category').sInput('link_save').hInput('old_name',$name);
		echo form(startTable('edit').$out.endTable());
	}

//-------------------------------------------------------------
	function link_save()
	{
		$in = gpsa(array('name','old_name','parent'));
		extract(doSlash($in));
		safe_query("update txp_category set name='$name',parent='$parent' 
					where name='$old_name' and type='link'"); 
		rebuild_tree('root', 1, 'link');
		safe_query("update txp_link set category='$name' where category='$old_name'"); 
		category_list(messenger('link_category',$name,'saved'));
	}

//-------------------------------------------------------------
	function link_delete()
	{	
		$name = gps('name');
		safe_query("delete from txp_category where name='$name' and type='link'"); 
		rebuild_tree('root', 1, 'link');
		category_list(messenger('link_category',$name,'deleted'));
	}





// -------------------------------------------------------------
	function image_list() 
	{
		global $url_mode;
		$out[] = 
			tr(
				td(strong(gTxt('image_head')).popHelp('image_category')).
				td()
			);
		$out[] = 
			tr(td(form(
				fInput('text','name','','edit','','',10).
				fInput('submit','',gTxt('Create'),'smallerbox').
				eInput('category').
				sInput('image_create')
			)));
	
		$rs = getTree('root','image');

		if($rs) {
			foreach ($rs as $a) {
				extract($a);
				if ($name=='root') continue;
				$deleteimage = dLink('category','image_delete','name',
					$name,'','type','image');
				$editimage = eLink('category','image_edit','name',
					$name,htmlspecialchars($name));

				$out[] = tr( td( str_repeat(sp,$level-1).$editimage ) . td( $deleteimage ) );
			}
		}
		return startTable('list').join('',$out).endTable();
	}

//-------------------------------------------------------------
	function image_create()
	{
		$name = trim(doSlash(ps('name')));

		$checkdb = getThing("select name from txp_category 
			where name='$name' and type='image'");

		if (!$checkdb) {
			if ($name) {
				$q = safe_query("insert into txp_category 
								 set name='$name',type='image',parent='root'");
				rebuild_tree('root', 1, 'image');
				if ($q) category_list(messenger('image_category',$name,'created'));
			} else category_list();
		} else category_list(messenger('image_category',$name,'already_exists'));
	}

//-------------------------------------------------------------
	function image_edit()
	{
		pagetop(gTxt('categories'));
		$name = gps('name');
		extract(getRow("select * from txp_category where name='$name' and type='image'"));
		$out = 
		tr(fLabelCell(gTxt('image_category_name').':').fInputCell('name',$name,1,20)).
		tr( fLabelCell('parent') . td(parent_pop($parent,'image'))).
		tr(tdcs(fInput('submit','', gTxt('save_button')), 2));
		$out.= eInput('category').sInput('image_save').hInput('old_name',$name);
		echo form(startTable('edit').$out.endTable());
	}

//-------------------------------------------------------------
	function image_save()
	{
		extract(doSlash(gpsa(array('name','old_name','parent'))));

		safe_query("update txp_category set name='$name',parent='$parent'
			where name='$old_name' and type='image'"); 
		rebuild_tree('root', 1, 'image');
		safe_query("update txp_image set category='$name' where category='$old_name'"); 
		category_list(messenger('image_category',$name,'saved'));
	}

//-------------------------------------------------------------
	function image_delete()
	{	
		$name = doSlash(gps('name'));
		$q = safe_query("delete from txp_category where name='$name' and type='image'"); 
		rebuild_tree('root', 1, 'image');
		if ($q) category_list(messenger('image_category',$name,'deleted'));
	}
?>
