<?php

$plugin['name'] = 'dca_random_image';

$plugin['author'] = 'Dean Allen';

$plugin['author_uri'] = 'http://textpattern.com';

$plugin['version'] = '0.1';

$plugin['description'] = 'Displays a random image selected from the directory specified';

$plugin['help'] = '

	<p>The image directory must be in the site root</p>
	
';

// please escape any single quotes

$plugin['code'] = '

	function dca_random_image() 
	{
		$dirname = \'random\';

		global $pfr,$txpcfg;
		$types = array(\'gif\',\'jpg\',\'png\');
		$path = $txpcfg[\'doc_root\'].$pfr.$dirname;
		if(is_dir($path)) {
			$thedir = opendir($path);
			while (false !== ($file = readdir($thedir))) {
				if (!preg_match(\'/^\./\',$file)) {
					$images[] = $file;
				}
			}
		}
		
		if(!empty($images)) {
			$thenum = array_rand($images);
			$theimage = $images[$thenum];
			list(,,$type,$dims) = @getimagesize($path.\'/\'.$theimage);

			if(isset($types[$type])) {
				return \'<img src="\'.$pfr.$dirname.\'/\'.$theimage.\'" \'.$dims.\' />\';
			}
		}
		return false;
	}

';

$plugin['md5'] = md5( $plugin['code'] );

// to produce a copy of the plugin for distribution, load this file in a browser. 

echo chr(60)."?php\n\n".'$'."plugin='" . base64_encode(serialize($plugin)) . "'\n?".chr(62);

?>
