<?php
/*
$HeadURL: http://textpattern.googlecode.com/svn/development/4.x/textpattern/update/_to_4.0.3.php $
$LastChangedRevision: 3047 $
*/
	if (!defined('TXP_UPDATE'))
		exit("Nothing here. You can't access this file directly.");

	safe_update('txp_form',"Form = CONCAT('<txp:comments_error wraptag=\"ul\" break=\"li\" />\n\n',Form)", "name LIKE 'comment_form'");
?>
