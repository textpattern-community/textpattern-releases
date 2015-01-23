<?php

/*
	This is Textpattern

	Copyright 2004 by Dean Allen
	www.textpattern.com
	All rights reserved

	Use of this software indicates acceptance of the Textpattern license agreement 
*/

	check_privs(1,2,3,5);

	$vars = array('Form','type','name','savenew','oldname');

	if(!$step or !function_exists($step)){
		form_edit();
	} else $step();

// -------------------------------------------------------------
	function form_list($curname)
	{
		global $step;
		$out[] = hed(gTxt('all_forms'),2);
		$out[] = startTable('list');
		$out[] = assHead('form','type','');
		
		$rs = safe_rows("*", "txp_form", "1 order by name");

		if ($rs) {
			foreach($rs as $a){
				extract($a);
					$editlink = ($curname!=$name) 
					?	eLink('form','form_edit','name',$name,$name)
					:	$name;
					$deletelink = ($name!='comments' && $name!='comment_form' 
						&& $name!='default' && $name!='Links') 
					?	dLink('form','form_delete','name',$name) 
					:	sp;
				$out[] = tr(td($editlink).td(small($type)).td($deletelink));
			}
			$out[] = tr(
				tda(
					form(
						fInput('submit','',gTxt('add'),'navbox').
						eInput('form').sInput('form_create')
					),' colspan="3" class="noline"'
				)
			).endTable();
			
			return join ('',$out);
		}
	}

// -------------------------------------------------------------
	function form_create() 
	{
		form_edit();
	}

// -------------------------------------------------------------
	function form_edit($message='')
	{
		global $step;
		pagetop(gTxt('edit_forms'),$message);

		extract(gpsa(array('Form','name','type')));

		if ($step=='form_create') {
			$Form=''; $name=''; $type='';
			$inputs = fInput('submit','savenew',gTxt('save_new'),'smallbox').
				eInput("form").sInput('form_save');
		} else {
			$name = (!$name or $step=='form_delete') ? 'default' : $name;
			$rs = safe_row("*", "txp_form", "name='$name'");
			if ($rs) {
				extract($rs);
				$inputs = fInput('submit','save',gTxt('save'),'smallbox').
					eInput("form").sInput('form_save').hInput('oldname',$name);
			}
		}

		$out = 
			startTable('edit').
			tr(
				tdtl(
					hed(gTxt('useful_tags'),2).
					graf(gTxt('articles').sp.popHelp('form_place_article').br.
						popTagLinks('article')).
					graf(gTxt('links').sp.popHelp('form_place_link').br.
						popTagLinks('link')).
					graf(gTxt('displayed_comments').sp.popHelp('form_place_comment').br.
						popTagLinks('comment')).
					graf(gTxt('comment_form').sp.popHelp('form_place_input').br.
						popTagLinks('comment_form')).
					graf(gTxt('search_input_form').sp.popHelp('form_place_search_input').br.
						popTagLinks('search_input')).
					graf(gTxt('search_results_form').
						sp.popHelp('form_place_search_results').br.
						popTagLinks('search_result'))
				).
				tdtl(
					'<form action="index.php" method="post">'.
					input_textarea($Form).

					graf(gTxt('form_name').br.
						fInput('text','name',$name,'edit','','',15)).
					graf(gTxt('form_type').br.
						formtypes($type)).
					graf(gTxt('only_articles_can_be_previewed')).
					fInput('submit','preview',gTxt('preview'),'smallbox').
					graf($inputs).
					'</form>'

				).
				tdtl(
					form_list($name)
				)
			).endTable();
			
		echo $out;
	}

// -------------------------------------------------------------
	function form_save() 
	{
		global $vars;
		extract(doSlash(gpsa($vars)));
		if ($savenew) {
			safe_insert("txp_form", "Form='$Form', type='$type', name='$name'");
			form_edit(messenger('form',$name,'created'));
		} else {
			safe_update(
				"txp_form", 
				"Form='$Form',type='$type',name='$name'",
				"name='$oldname'"
			);
			form_edit(messenger('form',$name,'updated'));		
		}
	}

// -------------------------------------------------------------
	function form_delete()
	{
		$name = gps('name');
		if ($name) safe_delete("txp_form","name='$name'");
		form_edit(messenger('form',$name,'deleted'));
	}
	
// -------------------------------------------------------------
	function formTypes($type) 
	{
	 	$types = array(''=>'','article'=>'article','comment'=>'comment','link'=>'link','misc'=>'misc'); 
		return selectInput('type',$types,$type);
	}

// -------------------------------------------------------------
	function input_textarea($Form) 
	{
		return 
		'<textarea name="Form" rows="20" cols="60">'.htmlspecialchars($Form).'</textarea>';
	}
?>
