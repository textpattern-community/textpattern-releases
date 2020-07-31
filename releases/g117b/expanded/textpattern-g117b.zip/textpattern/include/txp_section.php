<?php

/*
	This is Textpattern

	Copyright 2004 by Dean Allen
	www.textpattern.com
	All rights reserved

	Use of this software indicates acceptance ofthe Textpattern license agreement 
*/

	if(!$step or !function_exists($step)){
		section_list();
	} else $step();

// -------------------------------------------------------------
	function section_list($message='') 
	{
		pagetop(gTxt('sections'),$message);

		global $url_mode,$txpac,$wlink;
		$out[] = 
			tr(
				tdcs(strong(gTxt('section_head')).popHelp('section_category'),3)
			);
		$out[] = 
			tr(tdcs(form(
				fInput('text','name','','edit','','',10).
				fInput('submit','',gTxt('Create'),'smallerbox').
				eInput('section').
				sInput('section_create')
			),3));
	

			// load up some arrays to feed the individual section popups
		$pages = getThings("select name from txp_page");
		foreach ($pages as $a) $pageslist[$a] = $a;
		$styles = getThings("select name from txp_css");
		foreach ($styles as $a) $styleslist[$a] = $a;

		$rs = getRows("select * from txp_section where name!='' order by name");

		if($rs) {
			foreach ($rs as $a) {
				extract($a);
				if($name=='default') continue;
				if ($url_mode) {
					$wlink = (!check_sections($name)) 
					?	sp.wLink('section','missing_section_file','name',$name) 
					:	'';
				}
				$deletelink = dLink('section','section_delete','name',
					$name,'','type','section');
#				$editlink = eLink('section','section_edit','name',
#					$name,htmlspecialchars($name));

				$form = startTable('edit') .
				stackRows(
					fLabelCell(gTxt('section_name').':') . 
						fInputCell('name',$name,1,20),
		
					fLabelCell(gTxt('uses_page').':') . 
						td(selectInput('page',$pageslist,$page).
						popHelp('section_uses_page')),
		
					fLabelCell(gTxt('uses_style').':') . 
						td(selectInput('css',$styleslist,$css).
						popHelp('section_uses_css')),
		
					fLabelCell(gTxt('selected_by_default').'?') . 
						td(yesnoradio('is_default',$is_default).
						popHelp('section_is_default')),
		
					fLabelCell(gTxt('on_front_page').'?') . 
						td(yesnoradio('on_frontpage',$on_frontpage).
						popHelp('section_on_frontpage')),
		
					fLabelCell(gTxt('syndicate').'?') . 
						td(yesnoradio('in_rss',$in_rss).
						popHelp('section_syndicate')),
		
					fLabelCell(gTxt('include_in_search').'?') . 
						td(yesnoradio('searchable',$searchable).
						popHelp('section_searchable')),
						
					tdcs(fInput('submit', '', gTxt('save_button')),2)
				).
				endTable().
				eInput('section').sInput('section_save').hInput('old_name',$name);
				
				$form = form($form);

				$out[] = tr( td( $name.$wlink ) . td( $form ) . td( $deletelink ) );
			}
		}
		echo startTable('list').join('',$out).endTable();
	}

//-------------------------------------------------------------
	function section_create() 
	{
		$name = ps('name');
		$name = trim(doSlash($name));
		$chk = fetch('name','txp_section','name',$name);
		if (!$chk) {
			if ($name) {
				$rs = safe_query("
					insert into txp_section set 
					name         = '$name',
					page         = 'default',
					css          = 'default',
					is_default   = 0,
					in_rss       = 1,
					on_frontpage = 1");
				if ($rs) section_list(messenger('section',$name,'created'));
			} else section_list();
		} else section_list(gTxt('section_name_already_exists'));
	}

//-------------------------------------------------------------
	function section_save()
	{
		$in = psa(array(
			'name','page','css','is_default','on_frontpage','in_rss','searchable','old_name')
		);
		extract(doSlash($in));
		
		if ($is_default) {
			safe_query("update txp-section set is_default=0 where name!='$old_name'");
		}

		safe_query("
			update txp_section set 
			name         = '$name',
			page         = '$page',
			css          = '$css',
			is_default   = '$is_default',
			on_frontpage = '$on_frontpage',
			in_rss       = '$in_rss',
			searchable   = '$searchable'
			where name = '$old_name'");
		safe_query("update textpattern set Section='$name' where Section='$old_name'");
		section_list(messenger('section',$name,'updated'));
	}

// -------------------------------------------------------------
	function section_delete() 
	{
		$name = gps('name');
		safe_query("delete from txp_section where name='$name'");
		section_list(messenger('section',$name,'deleted'));
	}


//-------------------------------------------------------------
	function htaccess_snip($section) 
	{
		return "<Files $section>\n\tForceType application/x-httpd-php\n</Files>\n\n";
	}

//-------------------------------------------------------------
	function file_snip($s) 
	{
	return chr(60).'?php
	include "./textpattern/config.php"; 
	$s = "'.$s.'";
	include $txpcfg["txpath"]."/publish.php";
	textpattern();
?'.chr(62);

	}

// -------------------------------------------------------------
	function check_sections($section) 
	{
		global $txpcfg,$path_from_root;
		$txproot = $txpcfg['doc_root'].$path_from_root;

		if(is_dir($txproot)) {
			$thedir = opendir($txproot);
			while (false !== ($file = readdir($thedir))) {
				if (is_file($txproot.$file)) {
					$rootfiles[] = strtolower($file);
				}
			}
		}
		if (is_array($rootfiles)) {
			if (!in_array(strtolower($section),$rootfiles)) {
				return false;
			}
			return true;
		}
		return false;
	}

// -------------------------------------------------------------
	function missing_section_file() 
	{
		global $txpcfg;
		pageTop('missing section placeholder');
		$name = gps('name');
		$out = array(
		startTable("edit"),
		tr(
			tda(
				graf(gTxt('section_warning_part1').' '.strong($name).' '.gTxt('section_warning_part2').' '.$txpcfg['doc_root'].' '.gTxt('section_warning_part3').':').graf(
			'<textarea cols="50" rows="7">'.file_snip($name).'</textarea>').
			graf(gTxt('section_warning_part4').' <code>.htaccess</code> '.gTxt('section_warning_part5').' '.$txpcfg['doc_root'].' '.gTxt('section_warning_part6').':').
	graf(
			'<textarea cols="50" rows="4">'.htaccess_snip($name).'</textarea>'),' width="500px"')
		),
		endTable());
		echo join('',$out);
	}

?>
