<?php

/*
$HeadURL: http://svn.textpattern.com/releases/4.0.6/source/textpattern/lib/txplib_head.php $
$LastChangedRevision: 2783 $
*/

// -------------------------------------------------------------
	function pagetop($pagetitle,$message="")
	{
		global $css_mode,$siteurl,$sitename,$txp_user,$event;
		$area = gps('area');
		$event = (!$event) ? 'article' : $event;
		$bm = gps('bm');

		$privs = safe_field("privs", "txp_users", "name = '".doSlash($txp_user)."'");

		$GLOBALS['privs'] = $privs;

		$areas = areas();
		$area = false;

		foreach ($areas as $k => $v)
		{
			if (in_array($event, $v))
			{
				$area = $k;
				break;
			}
		}

		if (gps('logout'))
		{
			$body_id = 'page-logout';
		}

		elseif (!$txp_user)
		{
			$body_id = 'page-login';
		}

		else
		{
			$body_id = 'page-'.$event;
		}

	?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo LANG; ?>" lang="<?php echo LANG; ?>" dir="<?php echo gTxt('lang_dir'); ?>">
	<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta name="robots" content="noindex, nofollow" />
	<title>Txp &#8250; <?php echo htmlspecialchars($sitename) ?> &#8250; <?php echo escape_title($pagetitle) ?></title>
	<link href="textpattern.css" rel="Stylesheet" type="text/css" />
	<script type="text/javascript" src="textpattern.js"></script>
	<script type="text/javascript">
	<!--

		var cookieEnabled = checkCookies();

		if (!cookieEnabled)
		{
			confirm('<?php echo trim(gTxt('cookies_must_be_enabled')); ?>');
		}

<?php

	if ($event == 'list')
	{
		$sarr = array("\n", '-');
		$rarr = array('', '&#45;');

		$sections = '';

		$rs = safe_column('name', 'txp_section', "name != 'default'");

		if ($rs)
		{
			$sections = str_replace($sarr, $rarr, addslashes(selectInput('Section', $rs, '', true)));
		}

		$category1 = '';
		$category2 = '';

		$rs = getTree('root', 'article');

		if ($rs)
		{
			$category1 = str_replace($sarr, $rarr, addslashes(treeSelectInput('Category1', $rs, '')));
			$category2 = str_replace($sarr, $rarr, addslashes(treeSelectInput('Category2', $rs, '')));
		}

		$statuses = str_replace($sarr, $rarr, addslashes(selectInput('Status', array(
			1 => gTxt('draft'),
			2 => gTxt('hidden'),
			3 => gTxt('pending'),
			4 => gTxt('live'),
			5 => gTxt('sticky'),
		), '', true)));

		$comments_annotate = addslashes(onoffRadio('Annotate', safe_field('val', 'txp_prefs', "name = 'comments_on_default'")));

		$authors = '';

		$rs = safe_column('name', 'txp_users', "privs not in(0,6)");

		if ($rs)
		{
			$authors = str_replace($sarr, $rarr, addslashes(selectInput('AuthorID', $rs, '', true)));
		}

		// output JavaScript
?>
		function poweredit(elm)
		{
			var something = elm.options[elm.selectedIndex].value;

			// Add another chunk of HTML
			var pjs = document.getElementById('js');

			if (pjs == null)
			{
				var br = document.createElement('br');
				elm.parentNode.appendChild(br);

				pjs = document.createElement('P');
				pjs.setAttribute('id','js');
				elm.parentNode.appendChild(pjs);
			}

			if (pjs.style.display == 'none' || pjs.style.display == '')
			{
				pjs.style.display = 'block';
			}

			if (something != '')
			{
				switch (something)
				{
					case 'changesection':
						var sections = '<?php echo $sections; ?>';
						pjs.innerHTML = '<span><?php echo gTxt('section') ?>: '+sections+'</span>';
					break;

					case 'changecategory1':
						var categories = '<?php echo $category1; ?>';
						pjs.innerHTML = '<span><?php echo gTxt('category1') ?>: '+categories+'</span>';
					break;

					case 'changecategory2':
						var categories = '<?php echo $category2; ?>';
						pjs.innerHTML = '<span><?php echo gTxt('category2') ?>: '+categories+'</span>';
					break;

					case 'changestatus':
						var statuses = '<?php echo $statuses; ?>';
						pjs.innerHTML = '<span><?php echo gTxt('status') ?>: '+statuses+'</span>';
					break;

					case 'changecomments':
						var comments = '<?php echo $comments_annotate; ?>';
						pjs.innerHTML = '<span><?php echo gTxt('comments'); ?>: '+comments+'</span>';
					break;

					case 'changeauthor':
						var authors = '<?php echo $authors; ?>';
						pjs.innerHTML = '<span><?php echo gTxt('author'); ?>: '+authors+'</span>';
					break;

					default:
						pjs.style.display = 'none';
					break;
				}
			}

			return false;
		}

		addEvent(window, 'load', cleanSelects);
<?php
	}
?>
	-->
	</script>
	<script type="text/javascript" src="jquery.js"></script>
	</head>
	<body id="<?php echo $body_id; ?>">
  <table id="pagetop" cellpadding="0" cellspacing="0">
  <tr id="branding"><td><img src="txp_img/textpattern.gif" alt="textpattern" /></td><td id="navpop"><?php echo navPop(1); ?></td></tr>
  <tr id="nav-primary"><td align="center" class="tabs" colspan="2">
 		<?php
 		if (!$bm) {
			echo '<table cellpadding="0" cellspacing="0" align="center"><tr>
  <td valign="middle" style="width:368px">&nbsp;'.$message.'</td>',

			has_privs('tab.content')
			? areatab(gTxt('tab_content'), 'content', 'article', $area)
			: '',
			has_privs('tab.presentation')
			?	areatab(gTxt('tab_presentation'), 'presentation', 'page', $area)
			:	'',
			has_privs('tab.admin')
			?	areatab(gTxt('tab_admin'), 'admin', 'admin', $area)
			:	'',
			(has_privs('tab.extensions') and !empty($areas['extensions']))
			?	areatab(gTxt('tab_extensions'), 'extensions', array_shift($areas['extensions']), $area)
			:	'',

			'<td class="tabdown"><a href="'.hu.'" class="plain" target="_blank">'.gTxt('tab_view_site').'</a></td>',
		 '</tr></table>',

		'</td></tr><tr id="nav-secondary"><td align="center" class="tabs" colspan="2">
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
		$atts=' class="'.$tc.'"';
		$hatts=' href="?event='.$tarea.'" class="plain"';
      	return tda(tag($label,'a',$hatts),$atts);
	}

// -------------------------------------------------------------
	function tabber($label,$tabevent,$event)
	{
		$tc = ($event==$tabevent) ? 'tabup' : 'tabdown2';
		$out = '<td class="'.$tc.'"><a href="?event='.$tabevent.'" class="plain">'.$label.'</a></td>';
		return $out;
	}

// -------------------------------------------------------------

	function tabsort($area, $event)
	{
		if ($area)
		{
			$areas = areas();

			$out = array();

			foreach ($areas[$area] as $a => $b)
			{
				if (has_privs($b))
				{
					$out[] = tabber($a, $b, $event, 2);
				}
			}

			return ($out) ? join('', $out) : '';
		}

		return '';
	}

// -------------------------------------------------------------
	function areas()
	{
		global $privs, $plugin_areas;

		$areas['content'] = array(
			gTxt('tab_organise') => 'category',
			gTxt('tab_write')    => 'article',
			gTxt('tab_list')    =>  'list',
			gTxt('tab_image')    => 'image',
			gTxt('tab_file')	 => 'file',
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
			gTxt('tab_diagnostics') => 'diag',
			gTxt('tab_preferences') => 'prefs',
			gTxt('tab_site_admin')  => 'admin',
			gTxt('tab_logs')        => 'log',
			gTxt('tab_plugins')     => 'plugin',
			gTxt('tab_import')      => 'import'
		);

		$areas['extensions'] = array(
		);

		if (is_array($plugin_areas))
			$areas = array_merge_recursive($areas, $plugin_areas);

		return $areas;
	}

// -------------------------------------------------------------

	function navPop($inline = '')
	{
		$areas = areas();

		$out = array();

		foreach ($areas as $a => $b)
		{
			if (!has_privs( 'tab.'.$a))
			{
				continue;
			}

			if (count($b) > 0)
			{
				$out[] = n.t.'<optgroup label="'.gTxt('tab_'.$a).'">';

				foreach ($b as $c => $d)
				{
					if (has_privs($d))
					{
						$out[] = n.t.t.'<option value="'.$d.'">'.$c.'</option>';
					}
				}

				$out[] = n.t.'</optgroup>';
			}
		}

		if ($out)
		{
			$style = ($inline) ? ' style="display: inline;"': '';

			return '<form method="get" action="index.php" class="navpop"'.$style.'>'.
				n.'<select name="event" onchange="submit(this.form);">'.
				n.t.'<option>'.gTxt('go').'&#8230;</option>'.
				join('', $out).
				n.'</select>'.
				n.'</form>';
		}
	}

// -------------------------------------------------------------
	function button($label,$link)
	{
		return '<span style="margin-right:2em"><a href="?event='.$link.'" class="plain">'.$label.'</a></span>';
	}
?>
