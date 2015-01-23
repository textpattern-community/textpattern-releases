<?php

/*
$HeadURL: https://textpattern.googlecode.com/svn/releases/4.5.5/source/textpattern/theme/classic/classic.php $
$LastChangedRevision: 4055 $
*/

if (!defined('txpinterface')) die('txpinterface is undefined.');

class classic_theme extends theme
{
	function html_head()
	{
		return '<link type="text/css" href="'.$this->url.'textpattern.css" rel="stylesheet" />'.n;
	}

	function header()
	{
		$out[] = '<div id="masthead">'.
			n.'<div id="navpop">'.n.navPop(1).n.'</div>'.
			n.'<h1 id="branding">Textpattern</h1>'.
			n.'</div>';

		if (!$this->is_popup)
		{
			$out[] = '<div id="nav-primary" class="nav-tabs">'.
				n.'<ul>';

			$secondary = '';
			foreach ($this->menu as $tab)
			{
				$tc = ($tab['active']) ? 'tabup' : 'tabdown';
				$out[] = '<li><a class="'.$tc.'" href="?event='.$tab["event"].'">'.$tab["label"].'</a></li>';

				if ($tab['active'] && !empty($tab['items']))
				{
					$secondary = '<div id="nav-secondary" class="nav-tabs">'.
						n.'<ul>';
					foreach ($tab['items'] as $item)
					{
						$tc = ($item['active']) ? 'tabup' : 'tabdown';
						$secondary .= n.'<li><a class="'.$tc.'" href="?event='.$item['event'].'">'.$item['label'].'</a></li>';
					}
					$secondary .= n.'</ul>'.
						n.'</div>';
				}
			}
			$out[] = '<li id="view-site"><a class="tabdown" href="'.hu.'" target="_blank">'.gTxt('tab_view_site').'</a></li>';
			$out[] = '</ul>';
			$out[] = '</div>';
			$out[] = $secondary;
		}
		$out[] = '<div id="messagepane">'.$this->announce($this->message).'</div>'.n;
		return join(n, $out);
	}

	function footer()
	{
		global $txp_user;

		$out[] = '<a id="mothership" href="http://textpattern.com/" title="'.gTxt('go_txp_com').'" rel="external"><img src="'.$this->url.'carver.png" width="40" height="40" alt="Textpattern" /></a>'.n.
			graf('Textpattern CMS &#183; '.txp_version);

		if ($txp_user)
		{
			$out[] = graf(gTxt('logged_in_as').' '.span(txpspecialchars($txp_user)).br.
				'<a href="index.php?logout=1">'.gTxt('logout').'</a>', ' id="moniker"');
		}

		return join(n, $out);;
	}

	function announce($thing=array('', 0), $modal = false)
	{
		return $this->_announce($thing, false, $modal);
	}

	function announce_async($thing=array('', 0), $modal = false)
	{
		return $this->_announce($thing, true, $modal);
	}

	private function _announce($thing, $async, $modal)
	{
		// $thing[0]: message text
		// $thing[1]: message type, defaults to "success" unless empty or a different flag is set

		if (!is_array($thing) || !isset($thing[1]))	{
			$thing = array($thing, 0);
		}

		// still nothing to say?
		if (trim($thing[0]) === '') return '';

		switch ($thing[1]) {
			case E_ERROR:
				$class = 'error';
				break;
			case E_WARNING:
				$class = 'warning';
				break;
			default:
				$class = 'success';
				break;
		}

		if ($modal) {
			$html = ''; // TODO: Say what?
			$js = 'window.alert("'.escape_js(strip_tags($thing[0])).'")';
		} else {
			$html = '<span id="message" class="'.$class.'">'.gTxt($thing[0]).' <a href="#close" class="close">&times;</a></span>';
			// Try to inject $html into the message pane no matter when _announce()'s output is printed
			$js = escape_js($html);
			$js = <<< EOS
				$(document).ready(function() {
					$("#messagepane").html("{$js}");
					$('#message.success, #message.warning, #message.error').fadeOut('fast').fadeIn('fast');
				});
EOS;
		}
		if ($async) {
			return $js;
		} else {
			return script_js(str_replace('</', '<\/', $js), $html);
		}
	}

	function manifest()
	{
		global $prefs;
		return array(
			'author'      => 'Team Textpattern',
			'author_uri'  => 'http://textpattern.com/',
			'version'     => $prefs['version'],
			'description' => 'Textpattern Classic Theme',
			'help'        => 'http://textpattern.com/admin-theme-help',
		);
	}
}
?>
