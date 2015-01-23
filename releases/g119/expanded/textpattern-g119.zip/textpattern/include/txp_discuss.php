<?php

/*
	This is Textpattern

	Copyright 2004 by Dean Allen
	www.textpattern.com
	All rights reserved

	Use of this software indicates acceptance of the Textpattern license agreement 
*/

	check_privs(1,2,3);

	if(!$step or !function_exists($step)){
		discuss_list();
	} else $step();

//-------------------------------------------------------------
	function discuss_delete()
	{
		$discussid = ps('discussid');
		safe_delete("txp_discuss","discussid = $discussid");	
		discuss_list(messenger('message',$discussid,'deleted'));
	}

//-------------------------------------------------------------
	function discuss_save()
	{
		extract(doSlash(gpsa(array('email','name','web','message','discussid','ip','visible'))));
		safe_update("txp_discuss",
			"email   = '$email',
			 name    = '$name',
			 web     = '$web',
			 message = '$message',
			 visible = '$visible'",
			"discussid = $discussid");
		discuss_list(messenger('message',$discussid,'updated'));
	}

//-------------------------------------------------------------
	function discuss_list($message='') 
	{
		global $timeoffset;
		pagetop(gTxt('list_discussions'),$message);

		extract(doSlash(gpsa(array('page','crit'))));

		$total = safe_count('txp_discuss',"1");  
		$limit = 15;
		$numPages = ceil($total/$limit);  
		$page = (!$page) ? 1 : $page;
		$offset = ($page - 1) * $limit;

		$nav[] = ($page > 1)
		?	PrevNextLink("discuss",$page-1,gTxt('prev'),'prev') : '';

		$nav[] = sp.small($page. '/'.$numPages).sp;

		$nav[] = ($page != $numPages) 
		?	PrevNextLink("discuss",$page+1,gTxt('next'),'next') : '';

		$criteria = ($crit) ? "message like '%$crit%'" : '1'; 

		$rs = safe_rows(
			"*, unix_timestamp(posted) as uPosted", 
			"txp_discuss",
			"$criteria order by posted desc limit $offset,$limit"
		);
						
		if($rs) {	
			echo startTable('list'),
			assHead('date','name','message','parent','');
	
			foreach ($rs as $a) {
				extract($a);
				$dmessage = $message;
				$name = (!$visible) ? '<span style="color:red">'.$name.'</span>' : $name;
				$date = "".date("M d, g:ia",($uPosted + $timeoffset))."";
				$editlink = eLink('discuss','discuss_edit','discussid',$discussid,$date);
				$deletelink = dLink('discuss','discuss_delete','discussid',$discussid);
	
				$tq = fetch('Title','textpattern','ID',$parentid);
				$parent = (!$tq) ? gTxt('article_deleted') : $tq;
									
				echo assRow(array(
					$editlink   => 100,
					$name       => 100,
					$dmessage   => 250,
					$parent     => 100,
					$deletelink => 20
				));
			}
				echo tr(
						tda(
						graf('<a href="index.php?event=discuss'.a.'step=ipban_list">'.
							gTxt('list_banned_ips').'</a>'),' colspan="1" valign="middle"'
						).
						tdcs(
							form(
								fInput('text','crit','','edit').
								fInput('submit','search',gTxt('search'),'smallbox').
								eInput("discuss").
								sInput("list")
							)
						,2).
						tdcs(graf(join('',$nav)),2))
					,endTable();
		} else echo graf(gTxt('no_comments_recorded'), ' align="center"');
	}

//-------------------------------------------------------------
	function discuss_edit()
	{
		$discussid=gps('discussid');
		extract(safe_row("*", "txp_discuss", "discussid='$discussid'"));
		$ta = '<textarea name="message" cols="60" rows="15">'.
			htmlspecialchars($message).'</textarea>';

		if (fetch('ip','txp_discuss_ipban','ip',$ip)) {
			$banstep = 'ipban_unban'; $bantext = gTxt('unban');
		} else {
			$banstep = 'ipban_add'; $bantext = gTxt('ban');
		}
		
		$banlink = '[<a href="?event=discuss'.a.'step='.$banstep.a.'ip='.$ip.a.
			'name='.urlencode($name).a.'discussid='.$discussid.'">'.$bantext.'</a>]';
	
		pagetop(gTxt('edit_comment'));
		echo 
			form(
			startTable('edit').
				stackRows(
					fLabelCell('name') . fInputCell('name',$name),
					fLabelCell('email') . fInputCell('email',$email),
					fLabelCell('website') . fInputCell('web',$web),
					td() . td($ta),
					fLabelCell('visible') . td(checkbox('visible', 1,$visible)),
					fLabelCell('IP') . td($ip.sp.$banlink),
					td() . td(fInput('submit','step',gTxt('save'),'publish')),
				hInput("discussid", $discussid).hInput('ip',$ip).
				eInput('discuss').sInput('discuss_save')
			).
			endTable()
		);
	}

// -------------------------------------------------------------
	function ipban_add() 
	{
		extract(doSlash(gpsa(array('ip','name','discussid'))));
		
		if (!$ip) exit(ipban_list(gTxt("cant_ban_blank_ip")));
		
		$chk = fetch('ip','txp_discuss_ipban','ip',$ip);
		
		if (!$chk) {
			$rs = safe_insert("txp_discuss_ipban",
				"ip = '$ip',
				 name_used = '$name',
				 banned_on_message = '$discussid',
				 date_banned = now()
			");
			if ($rs) ipban_list(messenger('ip',$ip,'banned'));
		} else ipban_list(messenger('ip',$ip,'already_banned'));
		
	}

// -------------------------------------------------------------
	function ipban_unban() 
	{
		$ip = ps('ip');
		
		$rs = safe_delete("txp_discuss_ipban","ip='$ip'");
		
		if($rs) ipban_list(messenger('ip',$ip,'unbanned'));
	}

// -------------------------------------------------------------
	function ipban_list($message='')
	{
		pageTop(gTxt('list_banned_ips'),$message);
		$rs = safe_rows(
				"*", 
				"txp_discuss_ipban", 
				"1 order by date_banned desc"
			);
		if ($rs) {
			echo startTable('list'),
			tr(
				hCell('Date banned') .
				hCell('ip')          .
				hCell('Name used')   .
				hCell('Banned for')  .
				td()
			);

			foreach($rs as $a) {
				extract($a);
				
				$unbanlink = '<a href="?event=discuss'.a.'step=ipban_unban'.a.
					'ip='.$ip.'">unban</a>';
				$datebanned = date("Y-m-d",strtotime($date_banned));
				$messagelink = '<a href="?event=discuss'.a.'step=discuss_edit'.a.'discussid='.$banned_on_message.'">'.$banned_on_message.'</a>';
				echo
				tr(
					td($datebanned)  .
					td($ip)          .
					td($name_used)   .
					td($messagelink) .
					td($unbanlink)
				);
				
			}
			echo endTable();
		} else echo graf(gTxt('no_ips_banned'),' align="center"');
	}
?>
