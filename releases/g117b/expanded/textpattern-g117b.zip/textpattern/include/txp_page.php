<?php
/*
	This is Textpattern

	Copyright 2004 by Dean Allen
	www.textpattern.com
	All rights reserved

	Use of this software indicates acceptance of the Textpattern license agreement 
*/
	check_privs(1,2,3,5);

	if(!$step or !function_exists($step)){
		page_edit();
	} else $step();
	
//-------------------------------------------------------------
	function page_edit($message='')
	{
		global $step;
		pagetop(gTxt('edit_pages'),$message);
		extract(gpsa(array('name','div')));
		$name = (!$name or $step=='page_delete') ? 'default' : $name;

		$divline = ($step=="div_edit")
		?	graf(gTxt('you_are_editing_div') . sp . strong($div))
		:	'';
		
		echo 
		form(
			startTable('edit').
			tr(
				td().
				td(
					graf(gTxt('you_are_editing_page') . sp . strong($name)).$divline
				).
				td()
			).
			tr(
				tda(
					hed(gTxt('useful_tags'),2).
					graf(gTxt('page_article_hed').br.small(taglinks('page_article')),' class="column"').
					graf(gTxt('page_article_nav_hed').br.small(taglinks('page_article_nav')),' class="column"').
					graf(gTxt('page_nav_hed').br.small(taglinks('page_nav')),' class="column"').
					graf(gTxt('page_xml_hed').br.small(taglinks('page_xml')),' class="column"').
					graf(gTxt('page_misc_hed').br.small(taglinks('page_misc')),' class="column"')
				).
				tda(
					page_edit_form($name),' class="column"'
				).
				tda(
					hed(gTxt('all_pages'),2).
					page_list($name),' class="column"'
				)
			).
			endTable()
		);
	}

// -------------------------------------------------------------
	function div_edit() 
	{
		return page_edit();
	}

//-------------------------------------------------------------
	function page_edit_form($name) 
	{
		global $step;
		if ($step=='div_edit') {
			list($html_array,$html,$start_pos,$stop_pos) = extract_div();
			$html_array = serialize($html_array);
			$outstep = 'div_save';
		} else {
			$html = fetch('user_html','txp_page','name',$name);
			$outstep = 'page_save';
		}
		
		$out[] = textarea('400','500',$html,'html').
				graf(
					fInput('submit','save',gTxt('save'),'publish').
					eInput('page').
					sInput($outstep).
					hInput('name',$name)
				);
				
			if($step=='div_edit') {
				$out[] = 
					hInput('html_array',$html_array).
				  	hInput('start_pos',$start_pos).
				  	hInput('stop_pos',$stop_pos).
				  	hInput('name',$name);
			} else {
				$out[] = 
					graf(
						gTxt('copy_page_as').
						fInput('text','newname','').
						fInput('submit','copy',gTxt('copy'),'smallerbox')
					); 
			}
		return join('',$out);
	}

//-------------------------------------------------------------
	function page_list($current)
	{
		$rs = getRows("select name from txp_page where name != '' order by name");			
		foreach ($rs as $a) {
			extract($a);
			$dlink = ($name!='default') ? sp.sp.dLink('page','page_delete','name',$name) :'';
			$link =  '<a href="?event=page'.a.'name='.$name.'">'.$name.'</a>';
			$out[] = ($current == $name) 
			?	tr(td($name).td($dlink))
			:	tr(td($link).td($dlink));
		}
		return startTable('list').join(n,$out).endTable();
	}
	
//-------------------------------------------------------------
	function page_delete()
	{
		$name = gps('name');
		safe_query("delete from txp_page where name='$name'");
		page_edit(messenger('page',$name,'deleted'));
	}

// -------------------------------------------------------------
	function page_save() {
		extract(doSlash(gpsa(array('name','html','newname','copy'))));
		if($newname && $copy) {
			safe_query("insert into txp_page set name='$newname',user_html='$html'");
			page_edit(messenger('page',$newname,'created'));
		} else {
			safe_query("update txp_page set user_html='$html' where name='$name'");
			page_edit(messenger('page',$name,'updated'));
		}
	}
	
//-------------------------------------------------------------
	function textarea($h,$w,$content,$name)
	{
		return '<textarea name="'.$name.'" style="height:'.$h.'px;width:'.$w.'px;font-family:monaco,courier,courier new;font-size:10px;margin-top:6px" rows="1" cols="1">'.htmlspecialchars($content).'</textarea>';
	}

//-------------------------------------------------------------
	function taglinks($type) 
	{
		return popTagLinks($type);
	}


// -------------------------------------------------------------
	function extract_div() 
	{
		extract(gpsa(array('name','div')));
		$name = (!$name) ? 'default' : $name;
		$html = fetch('user_html','txp_page','name',$name);
		
		$goodlevel = (preg_match("/<div id=\"container\"/i",$html)) ? 2 : 1;
		
		if ($div) {
		
			$html_array = preg_split("/(<.*>)/U",$html,-1,PREG_SPLIT_DELIM_CAPTURE);
						
			$level = 0;
			$count = -1;
			$indiv = false;
			foreach($html_array as $a) {
				$count++;
				if(preg_match("/^<div/i",$a)) $level++;
				if(preg_match("/^<div id=\"$div\"/i",$a)) {
					$indiv = true;
					$start_pos = $count;
				}

				if ($indiv) $thediv[] = $a;

#				print n.$count.': '.$level.': '.$a;

				if($level==$goodlevel && preg_match("/^<\/div/i",$a) && $indiv) {
					$indiv = false;
					$stop_pos = $count;
				}
				if(preg_match("/^<\/div/i",$a)) $level--;
			}
			return array($html_array,join('',$thediv),$start_pos,$stop_pos);
		}
	}
	
// -------------------------------------------------------------
	function div_save() 
	{
		extract(gpsa(array('html_array','html','start_pos','stop_pos','name')));
		
		$html_array = unserialize($html_array);
		
		$repl_array = preg_split("/(<.*>)/U",$html,-1,PREG_SPLIT_DELIM_CAPTURE);
		
		array_splice($html_array,$start_pos,($stop_pos - $start_pos)+1,$repl_array);
		
		$html = addslashes(join('',$html_array));
		
		safe_query("update txp_page set user_html='$html' where name='$name'");

		page_edit(messenger('page',$name,'updated'));

#		print_r($html_array);
	}
?>
