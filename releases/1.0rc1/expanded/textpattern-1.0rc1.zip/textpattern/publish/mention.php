<?php

/*
	This is Textpattern
	Copyright 2004 by Dean Allen - all rights reserved.

	Use of this software denotes acceptance of the Textpattern license agreement 

$HeadURL: http://svn.textpattern.com/current/textpattern/publish/mention.php $
$LastChangedRevision: 628 $

*/


// -------------------------------------------------------------
	function show_mentions()
	{
		global $id;		
		if ($id) {
			$rs = safe_rows("*", "txp_log_mention", "article_id='$id'");
			if ($rs) {
				foreach($rs as $a) {
					extract($a);
					$out[] = '<a href="http://'.$refpage.'" title="'.$excerpt
						.'">'.$reftitle.'</a>';
				}	
				return hed(gTxt('mentions'),3) . graf(join(br, $out));
			}
		}
	}	
?>
