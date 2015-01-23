<?php

$plugin['name'] = 'dca_articles_by_category';

$plugin['author'] = 'Dean Allen';

$plugin['author_uri'] = 'http://textpattern.com';

$plugin['version'] = '0.1';

$plugin['description'] = 'Displays a nested tree list of articles by category';

$plugin['help'] = '

	<p>Call with <txp:dca_articles_by_category /></p>
	
';

// please escape any single quotes

$plugin['code'] = '

	function dca_articles_by_category($atts)
	{       
		if (is_array($atts)) extract($atts);
		global $pretext;
		extract($pretext);

		$lastlevel = 1;
		$out = \'\';

		$cs = getTree(\'root\',\'article\');
				
		if ($cs) {

			foreach ($cs as $cat) {
			extract($cat);
				if ($name==\'root\') continue;
			
				if ($level < $lastlevel) {
					$out .= str_repeat(\'</ul></li>\',$lastlevel - $level);
				}
			
				$catn = addslashes($name);
				$rs = safe_rows(
					"ID, Title, Section",
					"textpattern", 
			    	"(Category1 = \'$catn\' or Category2 = \'$catn\')
                	 and Status = 4 and Posted <= now()
                	 order by Posted"
                );

				if ($rs) {
					foreach($rs as $art) {
						extract($art);
						
						if (trim($Title)) {

							$url = ($url_mode)
							 ? tag($Title,\'a\',\' href="\'.$pfr.$Section.
									\'/\'.$ID.\'/\'.stripSpace($Title).\'"\')
							 : tag($Title,\'a\',\' href="\'.$pfr.
									\'index.php?id=\'.$ID.\'"\');

							$thislist[] = tag($url,\'li\');
						}
					}
					$arts = tag(join(\'\',$thislist),\'ul\');
					unset($thislist);
				} else $arts = \'\';

				if ($children == 0) {
					$out .= tag($name.$arts,\'li\');
				} else {
					$out .= \'<li>\'.$name.$arts.\'<ul>\';
				}
				
				$lastlevel = $level;
			}
		return tag($out,\'ul\');
		}
		return \'\';
	}

';

$plugin['md5'] = md5( $plugin['code'] );

// to produce a copy of the plugin for distribution, load this file in a browser. 

echo chr(60)."?php\n\n".'$'."plugin='" . base64_encode(serialize($plugin)) . "'\n?".chr(62);

?>
