<?php

// -------------------------------------------------------------
	function doArray($in,$function)
	{
		return is_array($in) ? array_map($function,$in) : $function($in); 
	}
	
// -------------------------------------------------------------
	function doStrip($in)
	{ 
		return doArray($in,'stripslashes'); 
	}

// -------------------------------------------------------------
	function doSlash($in)
	{ 
		return doArray($in,'mysql_escape_string'); 
	}

// -------------------------------------------------------------
	function doSpecial($in)
	{ 
		return doArray($in,'htmlspecialchars'); 
	}

//-------------------------------------------------------------
	function gTxt($var)
	{
		global $textarray;
		if(isset($textarray[strtolower($var)])) {
			return $textarray[strtolower($var)];
		}
		return $var;
	}

// -------------------------------------------------------------
    function dmp($in) 
    {
		echo "<pre>",(is_array($in)) ? print_r($in) : $in, "</pre>";
    }

// -------------------------------------------------------------
	function load_lang($lang) 
	{
		global $txpcfg;
		$file = file($txpcfg['txpath'].'/lang/'.$lang.'.txt');
		if(is_array($file)) {
			foreach($file as $line) {
				if(strlen($line) < 4 or $line[0]=='#') continue; 
				list($name,$val) = explode('=>',$line);
				$out[trim($name)] = trim(utf8_encode($val));
			}
			return $out;
		} 
	}

// -------------------------------------------------------------
	function load_lang_dates($lang) 
	{
		global $txpcfg;
		$file = file($txpcfg['txpath'].'/lang/'.$lang.'_dates.txt','r');
		if(is_array($file)) {
			foreach($file as $line) {
				if($line[0]=='#' || strlen($line) < 2) continue;
				list($name,$val) = explode('=>',$line,2);
				$out[trim($name)] = trim($val);			
			}
			return $out;
		} 
		return false;
	}

// -------------------------------------------------------------
	function check_privs()
	{
		global $txp_user;
		$privs = getThing("select privs from txp_users where name='$txp_user'");
		$args = func_get_args();
		if(!in_array($privs,$args)) {
			exit(pageTop('Restricted').'<p style="margin-top:3em;text-align:center">'.
				gTxt('restricted_area').'</p>');
		}
	}

// -------------------------------------------------------------
	function myErrorHandler ($errno, $errstr, $errfile, $errline) 
	{ 
		echo "<pre>$errno: $errstr in $errfile at line $errline\n"; 
		echo "Backtrace:\n"; 
		$trace = debug_backtrace(); 
		foreach($trace as $ent) { 
			if(isset($ent['file'])) echo $ent['file'].':'; 
			if(isset($ent['function'])) { 
				echo $ent['function'].'('; 
				if(isset($ent['args'])) { 
					$args=''; 
					foreach($ent['args'] as $arg) { $args.=$arg.','; } 
					echo rtrim($args,','); 
				}
				echo ') '; 
			}
			if(isset($ent['line'])) echo 'at line '.$ent['line'].' '; 
			if(isset($ent['file'])) echo 'in '.$ent['file']; 
			echo "\n"; 
		}
		echo "</pre>";
	}

// -------------------------------------------------------------
	function sizeImage($name) 
	{
		$size = @getimagesize($name);
		return(is_array($size)) ? $size[3] : false;
	}

// -------------------------------------------------------------
	function gps($thing) 
	{
		if (isset($_GET[$thing])) {
			if (get_magic_quotes_gpc()) {
				return stripslashes(urldecode($_GET[$thing]));
			} else {
				return urldecode($_GET[$thing]);
			}
		} elseif (isset($_POST[$thing])) {
			if (get_magic_quotes_gpc()) {
				return stripslashes($_POST[$thing]);
			} else {
				return $_POST[$thing];
			}
		}
		return '';
	}

// -------------------------------------------------------------
	function gpsa($array)
	{
		if(is_array($array)) {
			foreach($array as $a) {
				$out[$a] = gps($a);
			}
			return $out;
		}
		return false;
	}

// -------------------------------------------------------------
	function ps($thing) 
	{
		if (isset($_POST[$thing])) {
			if (get_magic_quotes_gpc()==1) {
				return stripslashes($_POST[$thing]);
			} else {
				return $_POST[$thing];
			}
		}
		return '';
	}

// -------------------------------------------------------------
	function psa($array)
	{
		foreach($array as $a) {
			$out[$a] = ps($a);
		}
		return $out;
	}
	
// -------------------------------------------------------------
	function stripPost() 
	{
		if (isset($_POST)) {
			if (get_magic_quotes_gpc()==1) {
				return doStrip($_POST);
			} else {
				return $_POST;
			}
		}
		return '';
	
	}

// -------------------------------------------------------------
	function serverSet($thing) // Get a var from $_SERVER global array, or create it 
	{
		return (isset($_SERVER[$thing])) ? $_SERVER[$thing] : '';
	}

// -------------------------------------------------------------t(
 	function pcs($thing) //	Get a var from POST or COOKIE; if not, create it 
	{
		if (isset($_COOKIE["txp_".$thing])) {
			if (get_magic_quotes_gpc()) {
				return stripslashes($_COOKIE["txp_".$thing]);
			} else return $_COOKIE["txp_".$thing];
		} elseif (isset($_POST[$thing])) {
			if (get_magic_quotes_gpc()) {
				return stripslashes($_POST[$thing]);
			} else return $_POST[$thing];
		} 
		return '';
	}

// -------------------------------------------------------------t(
 	function cs($thing) //	Get a var from COOKIE; if not, create it 
	{
		if (isset($_COOKIE[$thing])) {
			if (get_magic_quotes_gpc()) {
				return stripslashes($_COOKIE[$thing]);
			} else return $_COOKIE[$thing];
		}
		return '';
	}

// -------------------------------------------------------------
	function yes_no($status) 
	{
		return ($status==0) ? ucfirst(gTxt('no')) : ucfirst(gTxt('yes'));
	}
	
// -------------------------------------------------------------
	function getmicrotime() { 
    	list($usec, $sec) = explode(" ",microtime()); 
    	return ((float)$usec + (float)$sec); 
    }
?>
