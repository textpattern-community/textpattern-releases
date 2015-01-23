<?php

	check_privs(1,2,3,4,5,6);

	$myprivs = fetch('privs','txp_users','name',$txp_user);

	$levels = array(
		1 => gTxt('publisher'),
		2 => gTxt('managing_editor'),
		3 => gTxt('copy_editor'),
		4 => gTxt('staff_writer'),
		5 => gTxt('freelancer'),
		6 => gTxt('designer'),
		0 => gTxt('none')
	);

	if(!$step or !function_exists($step)){
		admin();
	} else $step();

// -------------------------------------------------------------
	function admin($message='') 
	{
		global $myprivs,$txp_user;
		pagetop(gTxt('site_administration'),$message);
		$themail = fetch('email','txp_users','name',$txp_user);

		echo new_pass_form();
		echo change_email_form($themail);
		echo author_list();
		echo ($myprivs == 1) ? new_author_form() : '';
	}

// -------------------------------------------------------------
	function change_email() 
	{
		global $txp_user;
		$new_email = gps('new_email');
		$rs = safe_query("update txp_users 
			set email  = '$new_email' 
			where name = '$txp_user'");	
		if ($rs) {
			admin('email address changed to '.$new_email);
		}
	}
	
// -------------------------------------------------------------
	function author_save() 
	{
		extract(doSlash(gpsa(array('privs','user_id','RealName','email'))));
		$rs = safe_query("
			update txp_users set 
			privs = $privs, 
			RealName = '$RealName',
			email = '$email' 
			where user_id='$user_id'");
		if ($rs) admin(messenger('author',$RealName,'updated'));
	}

// -------------------------------------------------------------
	function change_pass() 
	{
		global $txp_user;
		$message = '';
		$themail = fetch('email','txp_users','name',$txp_user);
		if (!empty($_POST["new_pass"])) {
			$NewPass = $_POST["new_pass"];
			$rs = safe_query("update txp_users set pass=password(lower('$NewPass')) 
				where name='$txp_user'");
			if ($rs) {
				$message .= gTxt('password_changed');
				if ($_POST['mailpassword']==1) {
					send_new_password($NewPass,$themail);
					$message .= sp.gTxt('and_mailed_to').sp.$themail;
				}
				$message .= ".";
			} else echo comment(mysql_error());
			admin($message);
		}
	}

// -------------------------------------------------------------
	function author_save_new() 
	{
		extract(doSlash(psa(array('privs','name','email','RealName'))));
		$pw = generate_password(6);
		$rs = safe_query("
			insert into txp_users set 
			privs    = '$privs',
			name     = '$name',
			email    = '$email',
			RealName = '$RealName',
			pass     =  password(lower('$pw'))");
		
		if ($rs) {
			send_password($pw,$email);
			admin(gTxt('password_sent_to').sp.$email);
		} else {
			admin(gTxt('error_adding_new_author'));
		}
	}

// -------------------------------------------------------------
	function privs($priv='') 
	{
		global $levels;
		return selectInput("privs", $levels, $priv);
	}

// -------------------------------------------------------------
	function get_priv_level($priv) 
	{
		global $levels;
		return $levels[$priv];
	}

// -------------------------------------------------------------
	function send_password($pw,$email) {
		global $siteurl,$path_from_root,$sitename,$txp_user;
		$myName = $txp_user;
		extract(getRow("select RealName as myName, email as myEmail 
			from txp_users where name = '$myName'"));

		$message = 'Dear '.$_POST['RealName'].','."\r\n"."\r\n".
	
		'You have been registered as a contributor to the site '.$sitename."\r\n".
	
		'Your login is '.$_POST['name']."\r\n".
		'Your password is '.$pw."\r\n"."\r\n".
	
		'Log in at http://'.$siteurl.$path_from_root.'textpattern/index.php';
	
		mail($email, "Your $sitename login password", $message,
		 "From: $myName <$myEmail>\r\n"
		."Reply-To: $myEmail\r\n");
	}

// -------------------------------------------------------------
	function send_new_password($NewPass,$themail) 
	{
		global $txp_user,$sitename;

		$message = 'Dear '.$txp_user.','."\r\n".
		'Your new password for the site '.$sitename.' is:'."\r\n"."\r\n".	
		$NewPass;
	
	mail($themail, "Your new $sitename password", $message,
		 "From: $txp_user <$themail>\r\n"
		."Reply-To: $themail\r\n");
	}


// -------------------------------------------------------------
	function generate_password($length = 10) 
	{
		$okchars = "ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789";
		$ps_len = strlen($okchars);
		mt_srand((double)microtime()*1000000);
		$pass = "";
		for($i = 0; $i < $length; $i++) {
			$pass .= $okchars[mt_rand(0,$ps_len-1)];
		}

		return $pass;
	}

// -------------------------------------------------------------
	function new_pass_form() 
	{
		return '<div align="center" style="margin-top:3em">'.
		form(
			tag(gTxt('change_password'),'h3').
			graf(gTxt('new_password').' '.
				fInput('password','new_pass','','edit','','','20','1').
				checkbox('mailpassword','1',1).gTxt('mail_it').' '.
				fInput('submit','change_pass',gTxt('submit'),'smallerbox').
				eInput('admin').sInput('change_pass')
			,' style="text-align:center"')
		).'</div>';
	}
	
// -------------------------------------------------------------
	function change_email_form($themail) 
	{
		return '<div align="center" style="margin-top:3em">'.
		form(
			tag(gTxt('change_email_address'),'h3').
			graf(gTxt('new_email').' '.
				fInput('text','new_email',$themail,'edit','','','20','2').
				fInput('submit','change_email',gTxt('submit'),'smallerbox').
				eInput('admin').sInput('change_email')
			,' style="text-align:center"')
		).'</div>';
	}

// -------------------------------------------------------------
	function author_list() 
	{
		global $myprivs;
		$out[] = hed(gTxt('authors'),3,' align="center"');
		$out[] = startTable('list');
		$out[] = tr(
			hCell(gTxt('real_name'))
		.	hCell(gTxt('login_name'))
		.	hCell(ucfirst(gTxt('email')))
		.	hCell(gTxt('privileges'))
		.	td()
		.	td()
		);

		$rs = getRows("select * from txp_users where name != 'textism'");
		if ($rs) {
			foreach($rs as $a) {
				extract($a);
				$deletelink = dLink('admin','author_delete','user_id',$user_id);
				$savelink = fInput("submit",'save',gTxt('save'),'smallerbox');
				$emailhref = '<a href="mailto:'.$email.'">'.$email.'</a>';
				$RealNameInput = fInput('text','RealName',$RealName,'edit');
				$emailInput = fInput('text','email',$email,'edit');
				
				$row[] = ($myprivs == 1) 
					?	td($RealNameInput)
					:	td($RealName);
					
				$row[] = td($name);

				$row[] = ($myprivs == 1) 
					?	td($emailInput)
					:	td($emailhref);
				
				$row[] = ($myprivs == 1) 
					?	td(privs($privs).popHelp("about_privileges"))
					:	td(get_priv_level($privs).popHelp("about_privileges"));

				$row[] = ($myprivs == 1) ? td($savelink) : '';

				$row[] = ($myprivs == 1)
					?	td($deletelink,10)
					:	td();

				$row[] = ($myprivs == 1)
					?	hInput("user_id",$user_id). eInput("admin").sInput('author_save')
					:	td();

				$out[] = 
				form(tr(join('',$row)));
				unset($row);
			}
		
			$out[] = endTable();
			return join('',$out);
		}
	}

// -------------------------------------------------------------
	function author_delete() 
	{
		$user_id = gps('user_id');
		$name = fetch('Realname','txp_users','user_id',$user_id);
		if ($name) {
			$rs = safe_query("delete from txp_users where user_id = '$user_id'");
			if ($rs) admin(messenger('author',$name,'deleted'));
		}
	}

// -------------------------------------------------------------
	function new_author_form() 
	{
		$out = array(
			hed(gTxt('add_new_author' ),3,' align="center"'),
			startTable('edit'),
			tr( fLabelCell( 'real_name' ) . fInputCell('RealName') ),
			tr( fLabelCell( 'login_name' ) . fInputCell('name') ),
			tr( fLabelCell( 'email' ) . fInputCell('email') ),
			tr( fLabelCell( 'privileges' ) . td(privs().popHelp('about_privileges')) ),
			tr( td() . td( fInput( 'submit','',gTxt('save'),'publish').
				popHelp('add_new_author')) ),
			endTable(),
			eInput('admin').sInput('author_save_new'));

		return form(join('',$out));
	}
?>
