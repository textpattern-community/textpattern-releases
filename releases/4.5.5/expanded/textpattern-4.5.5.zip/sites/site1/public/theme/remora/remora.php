<?php

/*
$HeadURL: https://textpattern.googlecode.com/svn/releases/4.5.5/source/textpattern/theme/remora/remora.php $
$LastChangedRevision: 3989 $
*/

if (!defined('txpinterface')) die('txpinterface is undefined.');

theme::based_on('classic');

class remora_theme extends classic_theme
{
	function html_head()
	{
		$js = <<<SF
			$(document).ready( function() {
				$("#nav li").hover( function() { $(this).addClass("sfhover"); }, function() { $(this).removeClass("sfhover"); } );
			});
SF;
		return parent::html_head().n.script_js($js).n;
	}

	function header()
	{
		global $txp_user;
		$out[] = '<div id="masthead">'.n.'<ul id="nav">';
		foreach ($this->menu as $tab)
		{
			$class = ($tab['active']) ? 'active' : 'inactive';
			$out[] = '<li class="primary '.$class.'"><a href="?event='.$tab["event"].'">'.$tab["label"].'</a>';
			if (!empty($tab['items']))
			{
				$out[] = '<ul>';
				foreach ($tab['items'] as $item)
				{
					$class = ($item['active']) ? 'active' : 'inactive';
					$out[] = '<li class="secondary '.$class.'"><a href="?event='.$item["event"].'">'.$item["label"].'</a></li>';
				}
				$out[] = '</ul>';

			}
			$out[] = '</li>';
		}
		$out[] = '<li id="view-site" class="primary tabdown inactive"><a href="'.hu.'" target="_blank">'.gTxt('tab_view_site').'</a></li>';
		if ($txp_user) $out[] = '<li id="logout" class="primary tabdown inactive"><a href="index.php?logout=1" onclick="return verify(\''.gTxt('are_you_sure').'\')">'.gTxt('logout').'</a></li>';
		$out[] = '</ul>';
		$out[] = '</div>';
		$out[] = '<div id="messagepane">'.$this->announce($this->message).'</div>'.n;
		return join(n, $out);
	}

	function footer()
	{
		return graf('<a href="http://textpattern.com/" title="'.gTxt('go_txp_com').'" rel="external">Textpattern CMS</a> &#183; '.txp_version);
	}

	function manifest()
	{
		global $prefs;
		return array(
			'author'      => 'Team Textpattern',
			'author_uri'  => 'http://textpattern.com/',
			'version'     => $prefs['version'],
			'description' => 'Textpattern Remora Theme',
			'help'        => 'http://textpattern.com/admin-theme-help',
		);
	}
}
?>
