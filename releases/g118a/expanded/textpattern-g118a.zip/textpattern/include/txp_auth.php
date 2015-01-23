<?php

/*
This is Textpattern

Copyright 2004 by Dean Allen
www.textpattern.com
All rights reserved

Use of this software indicates acceptance of the Textpattern license agreement 

*/
		
	unset($txp_user);
	
	$message = doTxpValidate();
	
	if(!$txp_user) {
		doLoginForm($message);
	}

	ob_start();

// -------------------------------------------------------------
	function txp_validate($user,$password) {
    	$safe_user = strtr(addslashes($user),array('_' => '\_', '%' => '\%'));
    	$r = safe_field("name", "txp_users", "name = '$safe_user'
							and pass = password(lower('$password')) and privs > 0");
    	if ($r) {
			// update the last access time
			safe_update("txp_users", "last_access = now()", "name = '$safe_user'");
			return true;
    	}
		return false;
	}
	
// -------------------------------------------------------------
	function doLoginForm($message) 
	{
		global $txpcfg;
		include $txpcfg['txpath'].'/lib/txplib_head.php';
		pagetop('log in');
		echo
		form(
			startTable('edit').
				tr(
					td().td(graf($message))
				).
				tr(
					fLabelCell('name').fInputCell('p_userid')
				).
				tr(
					fLabelCell('password').
					td(fInput('password','p_password','','edit'))
				).
				tr(
					td().td(graf(checkbox('stay',1,1).gTxt('stay_logged_in').
					popHelp('remember_login')))
				).
				tr(
					fLabelCell('').td(fInput('submit','',gTxt('log_in_button'),'publish'))
				).
			endTable()
		);
		exit("</div></body></html>");
	} // end doLoginForm()
	
	
// -------------------------------------------------------------
	function doTxpValidate() 
	{
		global $logout,$txpcfg;
		$p_userid = ps('p_userid');
		$p_password = ps('p_password');
		$logout = gps('logout');
		$secret_word = $txpcfg['secret_word'];
		$stay = ps('stay');
		
		if ($logout) {
			setcookie('txp_login',' ',time()-3600);
		}
		if (isset($_COOKIE['txp_login']) and !$logout) {	// cookie exists
	
			list($c_userid,$cookie_hash) = split(',',$_COOKIE['txp_login']);
	
			if (md5($c_userid.$secret_word) == $cookie_hash) {  // check secret word
	
				$GLOBALS['txp_user'] = $c_userid;	// cookie is good, create $txp_user
				return '';
	
			} else {
					// something's gone wrong
				$GLOBALS['txp_user'] = '';
				setcookie('txp_login','',time()-3600);
				return gTxt('bad_cookie');
			}
			
		} elseif ($p_userid and $p_password) {	// no cookie, but incoming login vars
		
				sleep(3); // should grind dictionary attacks to a halt
	
				if (txp_validate($p_userid,$p_password)) {
				
					if ($stay) {	// persistent cookie required
	
						setcookie('txp_login',
							$p_userid.','.md5($p_userid.$secret_word),
							time()+3600*24*365);	// expires in 1 year
	
					} else {    // session-only cookie required
	
						setcookie('txp_login',$p_userid.','.md5($p_userid.$secret_word));    			
					}
				
					$GLOBALS['txp_user'] = $p_userid;	// login is good, create $txp_user
					return '';

				} else {
					$GLOBALS['txp_user'] = '';
					return gTxt('could_not_log_in');
				}
	
		} else {
			$GLOBALS['txp_user'] = '';
			return gTxt('login_to_textpattern');
		}	
	} // end doTxpValidate() 
?>
