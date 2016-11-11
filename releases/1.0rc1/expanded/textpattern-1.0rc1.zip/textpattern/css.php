<?php
/*
$HeadURL: http://svn.textpattern.com/current/textpattern/css.php $
$LastChangedRevision: 628 $
*/

header('Content-type: text/css');
include './config.php';
$nolog = 1;
include $txpcfg['txpath'].'/publish.php';
$s = gps('s');
$n = gps('n');
output_css($s,$n);
?>
