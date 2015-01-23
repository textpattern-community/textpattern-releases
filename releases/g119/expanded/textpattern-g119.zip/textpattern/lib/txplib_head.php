<?php

// -------------------------------------------------------------
	function pagetop($pagetitle,$message="")
	{
		global $css_mode,$siteurl,$path_from_root,$txp_user;
		$area = gps('area');
		$event = gps('event');
		$event = (!$event) ? 'article' : $event;
		$bm = gps('bm');

		$privs = safe_field("privs", "txp_users", "`name`='$txp_user'");
		
		$GLOBALS['privs'] = $privs;

		$ctabs = array('article','image','link','discuss','category');
		$ptabs = array('section','page','css','form');
		$atabs = array('prefs','admin','plugin','log');
	
		    if(in_array($event,$ctabs)) { $area = 'content'; }
		elseif(in_array($event,$ptabs)) { $area = 'presentation'; }
		elseif(in_array($event,$atabs)) { $area = 'admin'; }
		
	?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
			"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>Textpattern &#8250; <?php echo strtolower($pagetitle)?></title>
	<link href="textpattern.css" rel="Stylesheet" type="text/css" />
	<script language="JavaScript" type="text/javascript">
	<!--
		function verify(msg) { return confirm(msg); }

		var cookieEnabled=(navigator.cookieEnabled)? true : false
		if (typeof navigator.cookieEnabled=="undefined" && !cookieEnabled) { 
			document.cookie="testcookie"
			cookieEnabled=(document.cookie=="testcookie")? true : false
			document.cookie="" //erase dummy value
		}
		if(!cookieEnabled){
			confirm(<?php echo "'".gTxt('cookies_must_be_enabled')."'"; ?>)
		}

		function toggleDisplay(obj_id){
			if (document.getElementById){
				var obj = document.getElementById(obj_id);
				if (obj.style.display == '' || obj.style.display == 'none'){
					var state = 'block';
				} else {
					var state = 'none';
				}
				obj.style.display = state;
			}
		}


	-->
	</script>
	</head>
	<body>
  <table cellpadding="0" cellspacing="0" width="100%" style="margin-bottom:2em">
  <tr><td align="left" style="background:#FFCC33"><img src="txp_img/textpattern.gif" height="15" width="368" alt="textpattern" /></td></tr>
  <tr><td align="center" class="tabs">
 		<?php
 		if (!$bm) {
			echo '<table cellpadding="0" cellspacing="0" align="center"><tr>
  <td valign="middle" style="width:368px">&nbsp;'.$message.'</td>',
  			
			areatab(gTxt('tab_content'), 'content', 'article', $area),
			($privs == 1 or $privs==2 or $privs==3 or $privs==6)
			?	areatab(gTxt('tab_presentation'), 'presentation', 'page', $area)
			:	'',
			($privs == 1 or $privs==2)
			?	areatab(gTxt('tab_admin'), 'admin', 'prefs', $area)
			:	'',

			'<td class="tabdown"><a href="http://'.$siteurl.$path_from_root.'" class="plain" target="blank">'.gTxt('tab_view_site').'</a></td>',
		 '</tr></table>',
		
		'</td></tr><tr><td align="center" class="tabs">
			<table cellpadding="0" cellspacing="0" align="center"><tr>',
				tabsort($area,$event),
			'</tr></table>';
		}
		echo '</td></tr></table>';
	}

// -------------------------------------------------------------
	function areatab($label,$event,$tarea,$area) 
	{
		$tc = ($area == $event) ? 'tabup' : 'tabdown';
		$atts=' class="'.$tc.'" onclick="window.location.href=\'?event='.$tarea.'\'"';
		$hatts=' href="?event='.$tarea.'" class="plain"';
      	return tda(tag($label,'a',$hatts),$atts);
	}

// -------------------------------------------------------------
	function tabber($label,$tabevent,$event) 
	{		
		$tc = ($event==$tabevent) ? 'tabup' : 'tabdown2';
		$out = '<td class="'.$tc.'" onclick="window.location.href=\'?event='.$tabevent.'\'" ><a href="?event='.$tabevent.'" class="plain">'.$label.'</a></td>';
      	return $out;
	}

// -------------------------------------------------------------
	function tabsort($area,$event) 
	{
		$areas = areas();
		foreach($areas[$area] as $a=>$b) {
			$out[] = tabber($a,$b,$event,2);
		}
		return join('',$out);
	}

// -------------------------------------------------------------
	function areas() 
	{
		global $privs;
		
		$areas['content'] = array(
			gTxt('tab_organise') => 'category',
			gTxt('tab_write')    => 'article',
			gTxt('tab_image')    => 'image',
			gTxt('tab_link')     => 'link',
			gTxt('tab_comments') => 'discuss'
		);
		
		$areas['presentation'] = array(
			gTxt('tab_sections') => 'section',
			gTxt('tab_pages')    => 'page',
			gTxt('tab_forms')    => 'form',
			gTxt('tab_style')    => 'css'
		);

		$areas['admin'] = array(
			gTxt('tab_preferences') => 'prefs',
			gTxt('tab_site_admin')  => 'admin',
			gTxt('tab_logs')        => 'log',
			gTxt('tab_plugins')     => 'plugin'
		);	
		return $areas;	
	}

// -------------------------------------------------------------
	function button($label,$link) 
	{
		return '<span style="margin-right:2em"><a href="?event='.$link.'" class="plain">'.$label.'</a></span>';
	}

?>
