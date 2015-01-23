<?php

/*
	This is Textpattern

	Copyright 2005 by Dean Allen
	www.textpattern.com
	All rights reserved

	Use of this software indicates acceptance of the Textpattern license agreement 

$HeadURL: http://svn.textpattern.com/releases/4.0.4/source/textpattern/include/txp_discuss.php $
$LastChangedRevision: 1909 $

*/

	if (!defined('txpinterface')) die('txpinterface is undefined.');

	if ($event == 'discuss') {
		require_privs('discuss');

		if(!$step or !in_array($step, array('discuss_delete','discuss_save','discuss_list','discuss_edit','ipban_add','discuss_multi_edit','ipban_list','ipban_unban','discuss_change_pageby'))){
			discuss_list();
		} else $step();
	}

//-------------------------------------------------------------
	function discuss_save()
	{
		extract(doSlash(gpsa(array('email','name','web','message','ip'))));
		extract(array_map('assert_int',gpsa(array('discussid','visible','parentid'))));
		safe_update("txp_discuss",
			"email   = '$email',
			 name    = '$name',
			 web     = '$web',
			 message = '$message',
			 visible = $visible",
			"discussid = $discussid");
		update_comments_count($parentid);
		update_lastmod();

		$message = gTxt('comment_updated', array('{id}' => $discussid));

		discuss_list($message);
	}

//-------------------------------------------------------------

	function short_preview($message)
	{
		$message = strip_tags($message);
		$offset = min(150, strlen($message));

		if (strpos($message, ' ', $offset) !== false)
		{
			$maxpos = strpos($message,' ',$offset);
			$message = substr($message, 0, $maxpos).'&#8230;';
		}

		return $message;
	}

//-------------------------------------------------------------

	function discuss_list($message = '')
	{
		pagetop(gTxt('list_discussions'), $message);

		echo graf(
			'<a href="index.php?event=discuss'.a.'step=ipban_list">'.gTxt('list_banned_ips').'</a>'
		, ' style="text-align: center;"');

		extract(get_prefs());

		extract(gpsa(array('sort', 'dir', 'page', 'crit', 'search_method')));

		$dir = ($dir == 'desc') ? 'desc' : 'asc';

		switch ($sort)
		{
			case 'id':
				$sort_sql = 'discussid '.$dir;
			break;

			case 'date':
				$sort_sql = 'posted '.$dir;
			break;

			case 'ip':
				$sort_sql = 'ip '.$dir.', posted asc';
			break;

			case 'name':
				$sort_sql = 'name '.$dir.', posted asc';
			break;

			case 'email':
				$sort_sql = 'email '.$dir.', posted asc';
			break;

			case 'website':
				$sort_sql = 'web '.$dir.', posted asc';
			break;

			case 'message':
				$sort_sql = 'message '.$dir.', posted asc';
			break;

			case 'parent':
				$sort_sql = 'parentid '.$dir.', posted asc';
			break;

			default:
				$dir = 'desc';
				$sort_sql = 'posted '.$dir;
			break;
		}

		$switch_dir = ($dir == 'desc') ? 'asc' : 'desc';

		$criteria = 1;

		if ($search_method and $crit)
		{
			$crit_escaped = doSlash($crit);

			$critsql = array(
				'id'			=> "discussid = '$crit_escaped'",
				'parent'  => "parentid = '$crit_escaped'",
				'name'		=> "name like '%$crit_escaped%'",
				'message' => "message like '%$crit_escaped%'",
				'email'		=> "email like '%$crit_escaped%'",
				'website' => "web like '%$crit_escaped%'",
				'ip'			=> "ip like %$crit_escaped%",
			);

			if (array_key_exists($search_method, $critsql))
			{
				$criteria = $critsql[$search_method];
				$limit = 500;
			}

			else
			{
				$search_method = '';
				$crit = '';
			}
		}

		else
		{
			$search_method = '';
			$crit = '';
		}

		$total = safe_count('txp_discuss', "$criteria");

		if ($total < 1)
		{
			if ($criteria != 1)
			{
				echo n.discuss_search_form($crit, $search_method).
					n.graf(gTxt('no_results_found'), ' style="text-align: center;"');
			}

			else
			{
				echo graf(gTxt('no_comments_recorded'), ' style="text-align: center;"');
			}

			return;
		}

		$limit = max(@$comment_list_pageby, 15);

		list($page, $offset, $numPages) = pager($total, $limit, $page);

		echo discuss_search_form($crit, $search_method);

		$rs = safe_rows_start('*, unix_timestamp(posted) as uPosted', 'txp_discuss', 
			"$criteria order by $sort_sql limit $offset, $limit");

		if ($rs)
		{
			echo n.n.'<form name="longform" method="post" action="index.php" onsubmit="return verify(\''.gTxt('are_you_sure').'\')">'.

				n.startTable('list','','','','90%').

				n.n.tr(
					column_head('ID', 'id', 'discuss', true, $switch_dir, $crit, $search_method).
					column_head('date', 'date', 'discuss', true, $switch_dir, $crit, $search_method).
					column_head('name', 'name', 'discuss', true, $switch_dir, $crit, $search_method).
					column_head('message', 'message', 'discuss', true, $switch_dir, $crit, $search_method).
					column_multi_head( array(
						array ('value' => 'email', 'sort' => 'email', 'event' => 'discuss','is_link' => true,
							    'dir' => $switch_dir, 'crit' => $crit, 'method' => $search_method),
						array ('value' => 'website', 'sort' => 'website', 'event' => 'discuss','is_link' => true,
							    'dir' => $switch_dir, 'crit' => $crit, 'method' => $search_method),
						array ('value' => 'IP', 'sort' => 'ip', 'event' => 'discuss','is_link' => true,
							    'dir' => $switch_dir, 'crit' => $crit, 'method' => $search_method)
					), 'discuss_detail').
					column_head('status', 'status', 'discuss', true, $switch_dir, $crit, $search_method, 'discuss_detail').
					column_head('parent', 'parent', 'discuss', true, $switch_dir, $crit, $search_method).
					hCell()
				);

			include_once txpath.'/publish/taghandlers.php';

			while ($a = nextRow($rs))
			{
				extract($a);
				$parentid = assert_int($parentid);

				$tq = safe_row('*, ID as thisid, unix_timestamp(Posted) as posted', 'textpattern', "ID = $parentid");

				$edit_url = '?event=discuss'.a.'step=discuss_edit'.a.'discussid='.$discussid.a.'sort='.$sort.
					a.'dir='.$dir.a.'page='.$page.a.'search_method='.$search_method.a.'crit='.$crit;

				$dmessage = ($visible == SPAM) ? short_preview($message) : $message;

				switch ($visible)
				{
					case VISIBLE:
						$comment_status = gTxt('visible');
						$row_class = 'visible';
					break;

					case SPAM:
						$comment_status = gTxt('spam');
						$row_class = 'spam';
					break;

					case MODERATE:
						$comment_status = gTxt('unmoderated');
						$row_class = 'moderate';
					break;

					default:
					break;
				}

				if (empty($tq))
				{
					$parent = gTxt('article_deleted').' ('.$parentid.')';
					$view = '';
				}

				else
				{
					$parent_title = empty($tq['Title']) ? '<em>'.gTxt('untitled').'</em>' : $tq['Title'];

					$parent = href($parent_title, '?event=list'.a.'step=list'.a.'search_method=id'.a.'crit='.$tq['ID']);

					$view = '';

					if ($visible == VISIBLE and in_array($tq['Status'], array(4,5)))
					{
						$view = n.t.'<li><a href="'.permlinkurl($tq).'#c'.$discussid.'">'.gTxt('view').'</a></li>';
					}
				}

				echo n.n.tr(

					n.td('<a href="'.$edit_url.'">'.$discussid.'</a>'.
						n.'<ul class="discuss_detail">'.
						n.t.'<li><a href="'.$edit_url.'">'.gTxt('edit').'</a></li>'.
						$view.
						n.'</ul>'
					, 50).

					td(
						safe_strftime('%d %b %Y %I:%M %p', $uPosted), 140
					).

					td($name, 75).

					td(
						short_preview($dmessage)
					).

					td("<ul><li>".soft_wrap($email, 30)."</li><li>".soft_wrap($web, 30)."</li><li>$ip</li></ul>", 100, 'discuss_detail').
					td($comment_status, 75, 'discuss_detail').
					td($parent, 75).

					td(
						fInput('checkbox', 'selected[]', $discussid)
					, 20)

				, ' class="'.$row_class.'"');
			}

			echo tr(
				tda(
					toggle_box('discuss_detail'),
					' colspan="2" style="text-align: left; border: none;"'
				).
				tda(
					select_buttons().
					discuss_multiedit_form($page, $sort, $dir, $crit, $search_method)
				, ' colspan="9" style="text-align: right; border: none;"')
			).

			endTable().
			'</form>'.

			nav_form('discuss', $page, $numPages, $sort, $dir, $crit, $search_method).

			pageby_form('discuss', $comment_list_pageby);
		}
	}

//-------------------------------------------------------------

	function discuss_search_form($crit, $method)
	{
		$methods =	array(
			'id'			=> gTxt('ID'),
			'parent'  => gTxt('parent'),
			'name'		=> gTxt('name'),
			'message' => gTxt('message'),
			'email'		=> gTxt('email'),
			'website' => gTxt('website'),
			'ip'			=> gTxt('IP')
		);

		return search_form('discuss', 'list', $crit, $methods, $method, 'message');
	}

//-------------------------------------------------------------

	function discuss_edit()
	{
		pagetop(gTxt('edit_comment'));

		extract(gpsa(array('discussid', 'sort', 'dir', 'page', 'crit', 'search_method')));

		$discussid = assert_int($discussid);

		$rs = safe_row('*, unix_timestamp(posted) as uPosted', 'txp_discuss', "discussid = $discussid");

		if ($rs)
		{
			extract($rs);

			$message = preg_replace(
				array('/</', '/>/'), 
				array('&lt;', '&gt;')
			, $message);

			if (fetch('ip', 'txp_discuss_ipban', 'ip', $ip))
			{
				$ban_step = 'ipban_unban';
				$ban_text = gTxt('unban');
			}

			else
			{
				$ban_step = 'ipban_add';
				$ban_text = gTxt('ban');
			}

			$ban_link = '[<a href="?event=discuss'.a.'step='.$ban_step.a.'ip='.$ip.
				a.'name='.urlencode($name).a.'discussid='.$discussid.'">'.$ban_text.'</a>]';

			echo form(
				startTable('edit').
				stackRows(

					fLabelCell('name').
					fInputCell('name', $name),

					fLabelCell('IP').
					td("$ip $ban_link"),

					fLabelCell('email').
					fInputCell('email', $email),

					fLabelCell('website').
					fInputCell('web', $web),

					fLabelCell('date').
					td(
						safe_strftime('%d %b %Y %I:%M:%S %p', $uPosted)
					),

					tda(gTxt('message')).
					td(
						'<textarea name="message" cols="60" rows="15">'.$message.'</textarea>'
					),

					fLabelCell('status').
					td(
						selectInput('visible', array(
							VISIBLE	 => gTxt('visible'),
							SPAM		 => gTxt('spam'),
							MODERATE => gTxt('unmoderated')
						), $visible, false)
					),

					td().td(fInput('submit', 'step', gTxt('save'), 'publish')),

					hInput('sort', $sort).
					hInput('dir', $dir).
					hInput('page', $page).
					hInput('crit', $crit).
					hInput('search_method', $search_method).

					hInput('discussid', $discussid).
					hInput('parentid', $parentid).
					hInput('ip', $ip).

					eInput('discuss').
					sInput('discuss_save')
				).

				endTable()
			);
		}

		else
		{
			echo graf(gTxt('comment_not_found'),' style="text-align: center;"');
		}
	}

// -------------------------------------------------------------

	function ipban_add() 
	{
		extract(gpsa(array('ip', 'name', 'discussid')));
		$discussid = assert_int($discussid);

		if (!$ip)
		{
			return ipban_list(gTxt('cant_ban_blank_ip'));
		}

		$ban_exists = fetch('ip', 'txp_discuss_ipban', 'ip', $ip);

		if ($ban_exists)
		{
			$message = gTxt('ip_already_banned', array('{ip}' => $ip));

			return ipban_list($message);
		}

		$rs = safe_insert('txp_discuss_ipban', "
			ip = '".doSlash($ip)."', 
			name_used = '".doSlash($name)."', 
			banned_on_message = $discussid, 
			date_banned = now()
		");

		// hide all messages from that IP also
		if ($rs)
		{
			safe_update('txp_discuss', "visible = ".SPAM, "ip = '".doSlash($ip)."'");

			$message = gTxt('ip_banned', array('{ip}' => $ip));

			return ipban_list($message);
		}

		ipban_list();
	}

// -------------------------------------------------------------

	function ipban_unban()
	{
		$ip = doSlash(gps('ip'));

		$rs = safe_delete('txp_discuss_ipban', "ip = '$ip'");

		if ($rs)
		{
			$message = gTxt('ip_ban_removed', array('{ip}' => $ip));

			ipban_list($message);
		}
	}

// -------------------------------------------------------------

	function ipban_list($message = '')
	{
		pageTop(gTxt('list_banned_ips'), $message);

		$rs = safe_rows_start('*, unix_timestamp(date_banned) as uBanned', 'txp_discuss_ipban', 
			"1 = 1 order by date_banned desc");

		if ($rs and numRows($rs) > 0)
		{
			echo startTable('list').
				tr(
					hCell(gTxt('date_banned')).
					hCell(gTxt('IP')).
					hCell(gTxt('name_used')).
					hCell(gTxt('banned_for')).
					td()
				);

			while ($a = nextRow($rs))
			{
				extract($a);

				echo tr(
					td(
						safe_strftime('%d %b %Y %I:%M %p', $uBanned)
					, 100).

					td(
						$ip
					, 100).

					td(
						$name_used
					, 100).

					td(
						'<a href="?event=discuss'.a.'step=discuss_edit'.a.'discussid='.$banned_on_message.'">'.
							$banned_on_message.'</a>'
					, 100).

					td(
						'<a href="?event=discuss'.a.'step=ipban_unban'.a.'ip='.$ip.'">'.gTxt('unban').'</a>'
					)
				);
			}

			echo endTable();
		}

		else
		{
			echo graf(gTxt('no_ips_banned'),' style="text-align: center;"');
		}
	}

// -------------------------------------------------------------
	function discuss_change_pageby() 
	{
		event_change_pageby('comment');
		discuss_list();
	}

// -------------------------------------------------------------

	function discuss_multiedit_form($page, $sort, $dir, $crit, $search_method) 
	{
		$methods = array(
			'visible'     => gTxt('show'),
			'unmoderated' => gTxt('hide_unmoderated'),
			'spam'        => gTxt('hide_spam'),
			'ban'         => gTxt('ban_author'),
			'delete'      => gTxt('delete'),
		);

		return event_multiedit_form('discuss', $methods, $page, $sort, $dir, $crit, $search_method);
	}

// -------------------------------------------------------------
	function discuss_multi_edit() 
	{
		//FIXME, this method needs some refactoring
		
		$selected = ps('selected');
		$method = ps('edit_method');
		$done = array();
		if ($selected) {
			// Get all articles for which we have to update the count
			foreach($selected as $id)
				$ids[] = assert_int($id);
			$parentids = safe_column("DISTINCT parentid","txp_discuss","discussid IN (".implode(',',$ids).")");

			$rs = safe_rows_start('*', 'txp_discuss', "discussid IN (".implode(',',$ids).")");
			while ($row = nextRow($rs)) {
				extract($row);
				$id = assert_int($discussid);
				$parentids[] = $parentid;

				if ($method == 'delete') {
					// Delete and if succesful update commnet count 
					if (safe_delete('txp_discuss', "discussid = $id"))
						$done[] = $id;
				}
				elseif ($method == 'ban') {
					// Ban the IP and hide all messages by that IP
					if (!safe_field('ip', 'txp_discuss_ipban', "ip='".doSlash($ip)."'")) {
						safe_insert("txp_discuss_ipban",
							"ip = '".doSlash($ip)."',
							name_used = '".doSlash($name)."',
							banned_on_message = $id,
							date_banned = now()
						");
						safe_update('txp_discuss',
							"visible = ".SPAM,
							"ip='".doSlash($ip)."'"
						);
					}
					$done[] = $id;
				}
				elseif ($method == 'spam') {
						if (safe_update('txp_discuss',
							"visible = ".SPAM,
							"discussid = $id"
						))
							$done[] = $id;
				}
				elseif ($method == 'unmoderated') {
						if (safe_update('txp_discuss',
							"visible = ".MODERATE,
							"discussid = $id"
						))
							$done[] = $id;
				}
				elseif ($method == 'visible') {
						if (safe_update('txp_discuss',
							"visible = ".VISIBLE,
							"discussid = $id"
						))
							$done[] = $id;
				}
				
			}

			$done = join(', ', $done);

			if ($done)
			{
				// might as well clean up all comment counts while we're here.
				clean_comment_counts($parentids);

				$messages = array(
					'delete'			=> gTxt('comments_deleted', array('{list}' => $done)),
					'ban'					=> gTxt('ips_banned', array('{list}' => $done)),
					'spam'				=> gTxt('comments_marked_spam', array('{list}' => $done)),
					'unmoderated' => gTxt('comments_marked_unmoderated', array('{list}' => $done)),
					'visible'			=> gTxt('comments_marked_visible', array('{list}' => $done))
				);

				update_lastmod();

				return discuss_list($messages[$method]);
			}
		}

		return discuss_list();
	}

?>
