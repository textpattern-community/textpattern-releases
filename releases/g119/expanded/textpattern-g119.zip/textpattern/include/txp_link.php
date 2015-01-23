<?php

/*
	This is Textpattern

	Copyright 2004 by Dean Allen
	www.textpattern.com
	All rights reserved

	Use of this software indicates acceptance of the Textpattern license agreement 
*/

	check_privs(1,2,3);


		$vars = array('category', 'url', 'linkname', 'linksort', 'description', 'id');
		
		switch(strtolower($step)) {
			case "":       link_edit(); break;
			case "list":   link_list(); break;
			case "edit":   link_edit(); break;
			case "post":   link_post($vars); break;
			case "save":   link_save($vars); break;
			case "delete": link_delete();
		}

// -------------------------------------------------------------
	function link_list($message="") 
	{
		global $step;
		if ($step=='list') pagetop(gTxt('edit_links'));
		
		$page = gps('page');
		$total = getCount('txp_link',"1");  
		$limit = 15;
		$numPages = ceil($total/$limit);  
		$page = (!$page) ? 1 : $page;
		$offset = ($page - 1) * $limit;

		$nav[] = ($page > 1)
		?	PrevNextLink("link",$page-1,gTxt('prev'),'prev') : '';

		$nav[] = sp.small($page. '/'.$numPages).sp;

		$nav[] = ($page != $numPages) 
		?	PrevNextLink("link",$page+1,gTxt('next'),'next') : '';

		$rs = safe_rows(
			"*", 
			"txp_link", 
			"1 order by category,linksort limit $offset,$limit"
		);

		if ($rs) {
			echo startTable('list'),

			assHead('link_name','description','link_category','');
			foreach ($rs as $a) {
				extract($a);				
				$elink = eLink('link','edit','id',$id,$linkname);
				$dlink = dLink('link','delete','id',$id);
					
				echo tr(
						td($elink).
						td($description).
						td($category).
						td($dlink)
					);
			}
			echo 
			tr(
				tdcs(			
					graf(join('',$nav))
				,4)
			);
			echo endTable();
		}
	}

// -------------------------------------------------------------
	function link_edit($message="")
	{
		global $vars,$step;
		extract(gpsa($vars));
		pagetop(gTxt('edit_links'),$message);
		$id = gps('id');
		if($id && $step=='edit') {
			extract(safe_row("*", "txp_link", "id = $id"));
		}
		
		if ($step=='save' or $step=='post'){
			foreach($vars as $var) {
				$$var = '';
			}
		}

		$textarea = '<textarea name="description" cols="40" rows="7" tabindex="4">'.
			$description.'</textarea>';
		$selects = linkcategory_popup($category);
		$editlink = ' ['.eLink('category','list','','',gTxt('edit')).']';

		$out = 
			startTable( 'edit' ) .
			
				tr( fLabelCell( 'title' ) .
					fInputCell( 'linkname', $linkname, 1, 30 )
				) .
					
				tr( fLabelCell( 'sort_value') .
					fInputCell( 'linksort', $linksort, 2, 15 )
				) .
					
				tr( fLabelCell( 'url','link_url').
					fInputCell( 'url', $url, 3, 30)
				) .
					
				tr( fLabelCell( 'link_category', 'link_category' ) .
					td( $selects . $editlink )
				) .
					
				tr( fLabelCell( 'description', 'link_description' ) .
					tda( $textarea, ' valign="top"' ) 
				) .
					
				tr( td() . 
					td( fInput( "submit", '', gTxt( 'save' ), "publish" ) ) 
				) .
				
			endTable() .
			
			eInput( 'link' ) . sInput( ( $step=='edit' ) ? 'save' : 'post' ) .
			hInput( 'id', $id );

		echo form( $out );
		echo link_list();
	}

//--------------------------------------------------------------
	function linkcategory_popup($cat="") 
	{
		$arr = array('');
		$rs = getTree("root","link");
		if ($rs) {
			return treeSelectInput("category", $rs, $cat);
		}
		return false;
	}

// -------------------------------------------------------------
	function link_post($vars)
	{
		global $txpcfg;
		$varray = gpsa($vars);

		include_once $txpcfg['txpath'].'/lib/classTextile.php';
		$textile = new Textile();
		
		$varray['linkname'] = $textile->TextileThis($varray['linkname'],'',1);
		$varray['description'] = $textile->TextileThis($varray['description'],1);
		
		extract(doSlash($varray));

		if (!$linksort) $linksort = $linkname;

		$q = safe_insert("txp_link",
		   "category    = '$category',
			date        = now(),
			url         = '".trim($url)."',
			linkname    = '$linkname',
			linksort    = '$linksort',
			description = '$description'"
		);

		if ($q) link_edit(messenger('link',$linkname,'created'));
	}

// -------------------------------------------------------------
	function link_save($vars) 
	{
		global $txpcfg;
		$varray = gpsa($vars);

		include_once $txpcfg['txpath'].'/lib/classTextile.php';
		$textile = new Textile();
		
		$varray['linkname'] = $textile->TextileThis($varray['linkname'],'',1);
		$varray['description'] = $textile->TextileThis($varray['description'],1);
		
		extract(doSlash($varray));
		
		if (!$linksort) $linksort = $linkname;

		$rs = safe_update("txp_link",
		   "category    = '$category',
			url         = '".trim($url)."',
			linkname    = '$linkname',
			linksort    = '$linksort',
			description = '$description'",
		   "id = '$id'"
		);
		if ($rs) link_edit( messenger( 'link', $linkname, 'saved' ) );
	}

// -------------------------------------------------------------
	function link_delete() 
	{
		$id = ps('id');
		$rs = safe_delete("txp_link", "id=$id");
		if ($rs) link_edit(messenger('link', '', 'deleted'));
	}
?>
