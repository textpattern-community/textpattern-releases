<?php

/*
	This is Textpattern

	Copyright 2004 by Dean Allen
	www.textpattern.com
	All rights reserved

	Use of this software indicates acceptance of the Textpattern license agreement 
*/

	check_privs(1,2,3,4,6);

	$extensions = array(0,'.gif','.jpg','.png','.swf');

	if(!$step or !function_exists($step)){
		image_list();
	} else $step();

// -------------------------------------------------------------
	function image_list($message='') 
	{
		global $txpcfg,$extensions,$path_from_root,$img_dir;
		$pfr = $path_from_root;
		extract($txpcfg);

		pagetop(gTxt('image'),$message);

		echo startTable('list'),
		tr(
			tda(
				upload_form(gTxt('upload_file'),gTxt('upload'),'image_insert'),
					' colspan="4" style="border:0"'
			)
		),
		tr(
			hCell(ucfirst(gTxt('name'))) . 
			hCell(gTxt('image_category')) . 
			hCell(gTxt('tags')) . 
			hCell(gTxt('author')) . 
			hCell(gTxt('thumbnail')) . 
			hCell()
		);

		$page = gps('page');

		$total = getCount('txp_image',"1");  
		$limit = 15;
		$numPages = ceil($total/$limit);  
		$page = (!$page) ? 1 : $page;
		$offset = ($page - 1) * $limit;

		$nav[] = ($page > 1)
		?	PrevNextLink("image",$page-1,gTxt('prev'),'prev') : '';

		$nav[] = sp.small($page. '/'.$numPages).sp;

		$nav[] = ($page != $numPages) 
		?	PrevNextLink("image",$page+1,gTxt('next'),'next') : '';
		
		$rs = safe_rows("*", "txp_image", "1 order by category,name limit $offset,$limit");
		
		if($rs) {
			foreach($rs as $a) {
			
				extract($a);
				
				$thumbnail = ($thumbnail) 
				?	'<img src="'.$pfr.$img_dir.'/'.$id.'t'.$ext.'" />' 
				:	gTxt('no');
				
				$elink = eLink('image','image_edit','id',$id,$name);
	
				$txtilelink = '<a target="_blank" href="?event=tag'.a.'name=image'.a.'id='.$id.a.'ext='.$ext.a.'alt='.$alt.a.'h='.$h.a.'w='.$w.a.'type=textile" onclick="window.open(this.href, \'popupwindow\', \'width=400,height=400,scrollbars,resizable\'); return false;">Textile</a>';
				$txplink = '<a target="_blank" href="?event=tag'.a.'name=image'.a.'id='.$id.a.'type=textpattern" onclick="window.open(this.href, \'popupwindow\', \'width=400,height=400,scrollbars,resizable\'); return false;">Textpattern</a>';
				$xhtmlink = '<a target="_blank" href="?event=tag'.a.'name=image'.a.'id='.$id.a.'ext='.$ext.a.'h='.$h.a.'w='.$w.a.'type=xhtml" onclick="window.open(this.href, \'popupwindow\', \'width=400,height=400,scrollbars,resizable\'); return false;">XHTML</a>';
				
				$dlink = dLink('image','image_delete','id',$id);
	
				echo
				tr(
					td($elink).td($category).td($txtilelink.' / '.$txplink.' / '.$xhtmlink). 
					td($author).
					td($thumbnail).
					td($dlink,10)
				);
			}

			echo 
				tr(
					tdcs(
						graf(join('',$nav))
					,4)
				);
		}
		echo endTable();

		$imgdir = $doc_root.$path_from_root.$img_dir;
		if (!is_dir($imgdir) or !is_writeable($imgdir)) {
		
			echo graf(str_replace("{imgdir}",$imgdir,gTxt('img_dir_not_writeable')),' style="text-align:center;color:red"');

		}
	}

// -------------------------------------------------------------
	function image_edit($message='',$id='') 
	{
		if (!$id) $id = gps('id');
		global $path_from_root,$txpcfg,$img_dir;

		pagetop('image',$message);

		$categories = getTree("root", "image");
		
		$rs = safe_row("*", "txp_image", "id='$id'");
		
		if ($rs) {
			extract($rs);
			echo startTable('list'),
			tr(
				td(
					'<img src="'.$path_from_root.$img_dir.
						'/'.$id.$ext.'" height="'.$h.'" width="'.$w.'" alt="" />'.
						br.upload_form(gTxt('replace_image'),'replace_image_form',
							'image_replace',$id)
				)
			),
			tr(
				td(
					join('',
						array(
							($thumbnail)
							?	'<img src="'.$path_from_root.$img_dir.
								'/'.$id.'t'.$ext.'" alt="" />'.br
							:	'',
							upload_form(gTxt('upload_thumbnail'),'upload_thumbnail',
								'thumbnail_insert',$id)
						)
					)
				)
			),
			tr(
				td(
					form(
						graf(gTxt('image_name').br.fInput('text','name',$name)) .
						 graf(gTxt('image_category').br.treeSelectInput('category',
						 		$categories,$category)) .
						graf(gTxt('alt_text').br.fInput('text','alt',$alt,'','','',50)) .
						graf(gTxt('caption').br.text_area('caption','100','400',$caption)) .
						graf(fInput('submit','',gTxt('save'))) .
						hInput('id',$id) .
						eInput('image') .
						sInput('image_save')
					)
				)
			),
			endTable();
		}
	}

// -------------------------------------------------------------
	function image_insert() 
	{	
		global $txpcfg,$extensions,$txp_user,$img_dir,$path_from_root;
		extract($txpcfg);
		$category = doSlash(gps('category'));
		
		$file = $_FILES['thefile']['tmp_name'];
		$name = $_FILES['thefile']['name'];

		list($w,$h,$extension) = getimagesize($file);

		if ($extensions[$extension]) {
			$ext = $extensions[$extension];
			$name = substr($name,0,strrpos($name,'.'));
			$name .= $ext;
			$name2db = doSlash($name);

			$rs = safe_insert("txp_image",
				"w        = '$w',
				 h        = '$h',
				 category = '$category',
				 ext      = '$ext',
				 `name`   = '$name2db',
				 `date`   = now(),
				 author   = '$txp_user'
			");
			
			$id = mysql_insert_id();
			
			if(!$rs){

				image_list('there was a problem saving image data');

			} else {

				$newpath = $doc_root.$path_from_root.$img_dir.'/'.$id.$ext;
				$newpath = str_replace('//','/',$newpath);

				if(move_uploaded_file($file, $newpath) == false) {
					safe_delete("txp_image","id='$id'");
					safe_alter("txp_image", "auto_increment=$id");
					image_list($newpath.sp.gTxt('upload_dir_perms'));
				} else {
					chmod($newpath,0755);
					image_edit(messenger('image',$name,'uploaded'),$id);
				}
			}
		} else {
			image_list(gTxt('only_graphic_files_allowed'));
		}
	}

// -------------------------------------------------------------
	function image_replace() 
	{	
		global $txpcfg,$extensions,$txp_user,$img_dir,$path_from_root;
		extract($txpcfg);
		$id = gps('id');
		
		$file = $_FILES['thefile']['tmp_name'];
		$name = $_FILES['thefile']['name'];

		list($w,$h,$extension) = getimagesize($file);

		if ($extensions[$extension]) {
			$ext = $extensions[$extension];
			$name = substr($name,0,strrpos($name,'.'));
			$name .= $ext;
			$name2db = doSlash($name);

			$rs = safe_update("txp_image",
				"w        = '$w',
				 h        = '$h',
				 ext      = '$ext',
				 `name`   = '$name2db',
				 `date`   = now(),
				 author   = '$txp_user'",
				 "id = $id
			");
			
			if(!$rs){

				image_list('there was a problem saving image data');

			} else {

				$newpath = $doc_root.$path_from_root.$img_dir.'/'.$id.$ext;
				$newpath = str_replace('//','/',$newpath);

				if(move_uploaded_file($file, $newpath) == false) {
					safe_delete("txp_image","id='$id'");
					safe_alter("txp_image", "auto_increment=$id");
					image_list($newpath.sp.gTxt('upload_dir_perms'));
				} else {
					chmod($newpath,0755);
					image_edit(messenger('image',$name,'uploaded'),$id);
				}
			}
		} else {
			image_list(gTxt('only_graphic_files_allowed'));
		}
	}

// -------------------------------------------------------------
	function thumbnail_insert() 
	{
		global $txpcfg,$extensions,$txp_user,$img_dir,$path_from_root;
		extract($txpcfg);
		$id = gps('id');
		
		$file = $_FILES['thefile']['tmp_name'];
		$name = $_FILES['thefile']['name'];

		list(,,$extension) = getimagesize($file);
	
		if ($extensions[$extension]) {
			$ext = $extensions[$extension];

			$newpath = $doc_root.$path_from_root.$img_dir.'/'.$id.'t'.$ext;
			
			if(move_uploaded_file($file, $newpath) == false) {
				image_list($newpath.sp.gTxt('upload_dir_perms'));
			} else {
				chmod($newpath,0755);
				safe_update("txp_image", "thumbnail='1'", "id='$id'");
				image_edit(messenger('image',$name,'uploaded'),$id);
			}
		} else image_list(gTxt('only_graphic_files_allowed')); 	
	}


// -------------------------------------------------------------
	function image_save() 
	{
		extract(doSlash(gpsa(array('id','name','category','caption','alt'))));
		
		safe_update(
			"txp_image",
			"name     = '$name',
			category = '$category',
			alt      = '$alt',
			caption  = '$caption'",
			"id = '$id'"
		);
		image_list(messenger("image",$name,"updated"));
	}

// -------------------------------------------------------------
	function image_delete() 
	{
		global $txpcfg,$path_from_root,$img_dir;
		extract($txpcfg);
		$id = ps('id');
		
		$rs = safe_row("*", "txp_image", "id='$id'");
		if ($rs) {
			extract($rs);
			$rsd = safe_delete("txp_image","id='$id'");
			$ul = unlink($doc_root.$path_from_root.$img_dir.'/'.$id.$ext);
			if(is_file($doc_root.$path_from_root.$img_dir.'/'.$id.'t'.$ext)){
				$ult = unlink($doc_root.$path_from_root.$img_dir.'/'.$id.'t'.$ext);
			}

			if ($rsd && $ul) image_list(messenger("image",$name,"deleted"));
		} else image_list();
	}

// -------------------------------------------------------------
	function upload_form($label,$pophelp,$step,$id='')
	{
		return
			'<form enctype="multipart/form-data" action="index.php" method="post">'.
			hInput('MAX_FILE_SIZE','1000000').
			graf($label.': '.
			fInput('file','thefile','','edit').
			popHelp($pophelp).
			fInput('submit','',gTxt('upload'),'smallerbox')).
			eInput('image').
			sInput($step).
			hInput('id',$id).
			'</form>';
	}
?>
