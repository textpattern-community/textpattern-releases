<?php

/*
	This is Textpattern

	Copyright 2005 by Dean Allen
	www.textpattern.com
	All rights reserved

	Use of this software indicates acceptance of the Textpattern license agreement

$HeadURL: https://textpattern.googlecode.com/svn/releases/4.5.5/source/textpattern/include/txp_discuss.php $
$LastChangedRevision: 4089 $

*/

	if (!defined('txpinterface')) die('txpinterface is undefined.');

	if ($event == 'discuss') {
		require_privs('discuss');

		$available_steps = array(
			'discuss_save'          => true,
			'discuss_list'          => false,
			'discuss_edit'          => false,
			'ipban_add'             => true,
			'discuss_multi_edit'    => true,
			'ipban_list'            => false,
			'ipban_unban'           => true,
			'discuss_change_pageby' => true
		);

		if ($step && bouncer($step, $available_steps)){
			$step();
		} else {
			discuss_list();
		}
	}

//-------------------------------------------------------------
	function discuss_save()
	{
		$varray = array_map('assert_string', gpsa(array('email', 'name', 'web', 'message', 'ip')));
		$varray = $varray + array_map('assert_int', gpsa(array('discussid', 'visible', 'parentid')));
		extract(doSlash($varray));

		$message = $varray['message'] = preg_replace('#<(/?txp:.+?)>#', '&lt;$1&gt;', $message);

		$constraints = array(
			'status' => new ChoiceConstraint($visible, array('choices' => array(SPAM, MODERATE, VISIBLE), 'message' =>  'invalid_status'))
		);

		callback_event_ref('discuss_ui', 'validate_save', 0, $varray, $constraints);
		$validator = new Validator($constraints);

		if ($validator->validate() && safe_update("txp_discuss",
			"email   = '$email',
			 name    = '$name',
			 web     = '$web',
			 message = '$message',
			 visible = $visible",
			"discussid = $discussid"))
		{
			update_comments_count($parentid);
			update_lastmod();
			$message = gTxt('comment_updated', array('{id}' => $discussid));
		}
		else
		{
			$message = array(gTxt('comment_save_failed'), E_ERROR);
		}

		discuss_list($message);
	}

//-------------------------------------------------------------

	function short_preview($message)
	{
		$message = strip_tags($message);
		$offset = min(120, strlen($message));

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
		global $event, $comment_list_pageby;

		pagetop(gTxt('list_discussions'), $message);

		extract(gpsa(array('sort', 'dir', 'page', 'crit', 'search_method')));
		if ($sort === '') $sort = get_pref('discuss_sort_column', 'date');
		if ($dir === '') $dir = get_pref('discuss_sort_dir', 'desc');
		$dir = ($dir == 'asc') ? 'asc' : 'desc';

		switch ($sort)
		{
			case 'id':
				$sort_sql = 'discussid '.$dir;
			break;

			case 'ip':
				$sort_sql = 'ip '.$dir;
			break;

			case 'name':
				$sort_sql = 'name '.$dir;
			break;

			case 'email':
				$sort_sql = 'email '.$dir;
			break;

			case 'website':
				$sort_sql = 'web '.$dir;
			break;

			case 'message':
				$sort_sql = 'message '.$dir;
			break;

			case 'status':
				$sort_sql = 'visible '.$dir;
			break;

			case 'parent':
				$sort_sql = 'parentid '.$dir;
			break;

			default:
				$sort = 'date';
				$sort_sql = 'txp_discuss.posted '.$dir;
			break;
		}

		if ($sort != 'date') $sort_sql .= ', txp_discuss.posted asc';

		set_pref('discuss_sort_column', $sort, 'discuss', 2, '', 0, PREF_PRIVATE);
		set_pref('discuss_sort_dir', $dir, 'discuss', 2, '', 0, PREF_PRIVATE);

		$switch_dir = ($dir == 'desc') ? 'asc' : 'desc';

		$criteria = 1;

		if ($search_method and $crit != '')
		{
			$verbatim = preg_match('/^"(.*)"$/', $crit, $m);
			$crit_escaped = doSlash($verbatim ? $m[1] : str_replace(array('\\','%','_','\''), array('\\\\','\\%','\\_', '\\\''), $crit));
			$critsql = $verbatim ?
				array(
					'id'      => "discussid = '$crit_escaped'",
					'parent'  => "parentid = '$crit_escaped'".(intval($crit_escaped) ? '' : " OR title = '$crit_escaped'"),
					'name'    => "name = '$crit_escaped'",
					'message' => "message = '$crit_escaped'",
					'email'   => "email = '$crit_escaped'",
					'website' => "web = '$crit_escaped'",
					'ip'      => "ip = '$crit_escaped'",
				) : array(
					'id'      => "discussid = '$crit_escaped'",
					'parent'  => "parentid = '$crit_escaped'".(intval($crit_escaped) ? '' : " OR title like '%$crit_escaped%'"),
					'name'    => "name like '%$crit_escaped%'",
					'message' => "message like '%$crit_escaped%'",
					'email'   => "email like '%$crit_escaped%'",
					'website' => "web like '%$crit_escaped%'",
					'ip'      => "ip like '%$crit_escaped%'",
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

		$criteria .= callback_event('admin_criteria', 'discuss_list', 0, $criteria);

		$counts = getRows(
			'SELECT visible, COUNT(*) AS c'.
			' FROM '.safe_pfx_j('txp_discuss').' LEFT JOIN '.safe_pfx_j('textpattern').' ON txp_discuss.parentid = textpattern.ID'.
			' WHERE '. $criteria.' GROUP BY visible'
		);

		$count[SPAM] = $count[MODERATE] = $count[VISIBLE] = 0;

		if ($counts) foreach($counts as $c)
		{
			$count[$c['visible']] = $c['c'];
		}

		// grand total comment count
		$total = $count[SPAM] + $count[MODERATE] + $count[VISIBLE];

		echo '<h1 class="txp-heading">'.gTxt('list_discussions').'</h1>';
		echo '<div id="'.$event.'_control" class="txp-control-panel">';
		echo graf(
			sLink('discuss', 'ipban_list', gTxt('list_banned_ips'))
			, ' class="txp-buttons"');

		if ($total < 1)
		{
			if ($criteria != 1)
			{
				echo n.discuss_search_form($crit, $search_method).
					n.graf(gTxt('no_results_found'), ' class="indicator"').'</div>';
			}

			else
			{
				echo graf(gTxt('no_comments_recorded'), ' class="indicator"').'</div>';
			}

			return;
		}

		// paging through displayed comments
		$total = ((cs('toggle_show_spam')) ? $count[SPAM] : 0) + $count[MODERATE] + $count[VISIBLE];
		$limit = max($comment_list_pageby, 15);
		list($page, $offset, $numPages) = pager($total, $limit, $page);

		echo discuss_search_form($crit, $search_method).'</div>';

		$spamq = cs('toggle_show_spam') ? '1=1' : 'visible != '.intval(SPAM);

		$rs = safe_query(
			'SELECT txp_discuss.*, unix_timestamp(txp_discuss.posted) as uPosted, ID as thisid, Section as section, url_title, Title as title, Status, unix_timestamp(textpattern.Posted) as posted'.
			' FROM '.safe_pfx_j('txp_discuss').' LEFT JOIN '.safe_pfx_j('textpattern').' ON txp_discuss.parentid = textpattern.ID'.
			' WHERE '.$spamq.' AND '.$criteria.
			' ORDER BY '.$sort_sql.
			' LIMIT '.$offset.', '.$limit
		);

		if ($rs)
		{
			echo n.'<div id="'.$event.'_container" class="txp-container">';
			echo n.n.'<form name="longform" id="discuss_form" class="multi_edit_form" method="post" action="index.php">'.

				n.'<div class="txp-listtables">'.
				n.startTable('', '', 'txp-list').
				n.'<thead>'.
				n.n.tr(
					n.hCell(fInput('checkbox', 'select_all', 0, '', '', '', '', '', 'select_all'), '', ' title="'.gTxt('toggle_all_selected').'" class="multi-edit"').
					n.column_head('ID', 'id', 'discuss', true, $switch_dir, $crit, $search_method, (('id' == $sort) ? "$dir " : '').'id').
					n.column_head('date', 'date', 'discuss', true, $switch_dir, $crit, $search_method, (('date' == $sort) ? "$dir " : '').'date posted created').
					n.column_head('name', 'name', 'discuss', true, $switch_dir, $crit, $search_method, (('name' == $sort) ? "$dir " : '').'name').
					n.column_head('message', 'message', 'discuss', true, $switch_dir, $crit, $search_method, (('message' == $sort) ? "$dir " : 'message')).
					n.column_head('email', 'email', 'discuss', true, $switch_dir, $crit, $search_method, (('email' == $sort) ? "$dir " : '').'discuss_detail email').
					n.column_head('website', 'website', 'discuss', true, $switch_dir, $crit, $search_method, (('website' == $sort) ? "$dir " : '').'discuss_detail website').
					n.column_head('IP', 'ip', 'discuss', true, $switch_dir, $crit, $search_method, (('ip' == $sort) ? "$dir " : '').'discuss_detail ip').
					n.column_head('status', 'status', 'discuss', true, $switch_dir, $crit, $search_method, (('status' == $sort) ? "$dir " : '').'status').
					n.column_head('parent', 'parent', 'discuss', true, $switch_dir, $crit, $search_method, (('parent' == $sort) ? "$dir " : '').'parent')
				).
				n.'</thead>';

			include_once txpath.'/publish/taghandlers.php';

			echo '<tbody>';

			while ($a = nextRow($rs))
			{
				extract($a);
				$parentid = assert_int($parentid);

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

				if (empty($thisid))
				{
					$parent = gTxt('article_deleted').' ('.$parentid.')';
					$view = '';
				}

				else
				{
					$parent_title = empty($title) ? '<em>'.gTxt('untitled').'</em>' : escape_title($title);

					$parent = href($parent_title, '?event=article'.a.'step=edit'.a.'ID='.$parentid);

					$view = $comment_status;

					if ($visible == VISIBLE and in_array($Status, array(4,5)))
					{
						$view = n.'<a title="'.gTxt('view').'" href="'.permlinkurl($a).'#c'.$discussid.'">'.$comment_status.'</a>';
					}
				}

				echo n.n.tr(
					n.td(fInput('checkbox', 'selected[]', $discussid), '', 'multi-edit').
					td('<a title="'.gTxt('edit').'" href="'.$edit_url.'">'.$discussid.'</a>', '', 'id').
					td(gTime($uPosted), '', 'date posted created').
					td(txpspecialchars(soft_wrap($name, 15)), '', 'name').
					td(short_preview($dmessage), '', 'message').
					td(txpspecialchars(soft_wrap($email, 15)), '', 'discuss_detail email').
					td(txpspecialchars(soft_wrap($web, 15)), '', 'discuss_detail website').
					td($ip, '', 'discuss_detail ip').
					td($view, '', 'status').
					td($parent, '', 'parent')
				, ' class="'.$row_class.'"');
			}

			if (empty($message))
				echo tr(tda(gTxt('just_spam_results_found'),' colspan="10"'));

			echo '</tbody>',
				n, endTable(),
				n, '</div>',
				n, discuss_multiedit_form($page, $sort, $dir, $crit, $search_method),
				n, tInput(),
				n, '</form>',
				n, graf(
					toggle_box('discuss_detail'),
					' class="detail-toggle"'
				),
				n, cookie_box('show_spam'),
				n, '<div id="'.$event.'_navigation" class="txp-navigation">',
				n, nav_form('discuss', $page, $numPages, $sort, $dir, $crit, $search_method, $total, $limit),
				n, pageby_form('discuss', $comment_list_pageby),
				n, '</div>',
				n, '</div>';
		}
	}

//-------------------------------------------------------------

	function discuss_search_form($crit, $method)
	{
		$methods =	array(
			'id'      => gTxt('ID'),
			'parent'  => gTxt('parent'),
			'name'    => gTxt('name'),
			'message' => gTxt('message'),
			'email'   => gTxt('email'),
			'website' => gTxt('website'),
			'ip'      => gTxt('IP')
		);

		return search_form('discuss', 'list', $crit, $methods, $method, 'message');
	}

//-------------------------------------------------------------

	function discuss_edit()
	{
		global $event;

		pagetop(gTxt('edit_comment'));

		extract(gpsa(array('discussid', 'sort', 'dir', 'page', 'crit', 'search_method')));

		$discussid = assert_int($discussid);

		$rs = safe_row('*, unix_timestamp(posted) as uPosted', 'txp_discuss', "discussid = $discussid");

		if ($rs)
		{
			extract($rs);

			$message = txpspecialchars($message);

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

			$ban_link = '[<a class="action-ban" href="?event=discuss'.a.'step='.$ban_step.a.'ip='.$ip.
				a.'name='.urlencode($name).a.'discussid='.$discussid.a.'_txp_token='.form_token().'">'.$ban_text.'</a>]';

			$status_list = selectInput(
				'visible',
				array(
					VISIBLE	 => gTxt('visible'),
					SPAM		 => gTxt('spam'),
					MODERATE => gTxt('unmoderated')
				),
				$visible,
				false,
				'',
				'status');

			echo '<div id="'.$event.'_container" class="txp-container">'.
				form(
					'<div class="txp-edit">'.n.
					hed(gTxt('edit_comment'), 2).n.
					inputLabel('status', $status_list, 'status').n.
					inputLabel('name', fInput('text', 'name', $name, '', '', '', INPUT_REGULAR, '', 'name'), 'name').n.
					inputLabel('IP', $ip.n.$ban_link, '').n.
					inputLabel('email', fInput('text', 'email', $email, '', '', '', INPUT_REGULAR, '', 'email'), 'email').n.
					inputLabel('website', fInput('text', 'web', $web, '', '', '', INPUT_REGULAR, '', 'website'), 'website').n.
					inputLabel('date', safe_strftime('%d %b %Y %X', $uPosted), '').n.
					inputLabel('commentmessage', '<textarea id="commentmessage" name="message" cols="'.INPUT_LARGE.'" rows="'.INPUT_MEDIUM.'">'.$message.'</textarea>', 'message', '', '', '').n.
					graf(fInput('submit', 'step', gTxt('save'), 'publish')).

					hInput('sort', $sort).
					hInput('dir', $dir).
					hInput('page', $page).
					hInput('crit', $crit).
					hInput('search_method', $search_method).
	
					hInput('discussid', $discussid).
					hInput('parentid', $parentid).
					hInput('ip', $ip).
	
					eInput('discuss').
					sInput('discuss_save').
					'</div>'
				, '', '', 'post', 'edit-form', '', 'discuss_edit_form'),'</div>';
		}

		else
		{
			echo graf(gTxt('comment_not_found'),' class="indicator"');
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
		global $event;

		pageTop(gTxt('list_banned_ips'), $message);

		echo '<h1 class="txp-heading">'.gTxt('banned_ips').'</h1>';
		echo '<div id="'.$event.'_banned_control" class="txp-control-panel">';
		echo graf(
			sLink('discuss', 'discuss_list', gTxt('list_discussions'))
			, ' class="txp-buttons"');
		echo '</div>';

		$rs = safe_rows_start('*, unix_timestamp(date_banned) as uBanned', 'txp_discuss_ipban',
			"1 = 1 order by date_banned desc");

		if ($rs and numRows($rs) > 0)
		{
			echo '<div id="'.$event.'_ban_container" class="txp-container">'.
				startTable('', '', 'txp-list').
				n.'<thead>'.
				tr(
					hCell(gTxt('date_banned'), '', ' class="date banned"').
					hCell(gTxt('IP'), '', ' class="ip"').
					hCell(gTxt('name_used'), '', ' class="name"').
					hCell(gTxt('banned_for'), '', ' class="id"')
				).
				n.'</thead>';

			echo '<tbody>';

			while ($a = nextRow($rs))
			{
				extract($a);

				echo tr(
					td(
						gTime($uBanned)
					, '', 'date banned').

					td(
						txpspecialchars($ip).n.
						'[<a class="action-ban" href="?event=discuss'.a.'step=ipban_unban'.a.'ip='.txpspecialchars($ip).a.'_txp_token='.form_token().'">'.gTxt('unban').'</a>]'
					, '', 'ip').

					td(
						txpspecialchars($name_used)
					, '', 'name').

					td(
						'<a href="?event=discuss'.a.'step=discuss_edit'.a.'discussid='.$banned_on_message.'">'.
							$banned_on_message.'</a>'
					, '', 'id')
				);
			}

			echo '</tbody>'.
			endTable().
			'</div>';
		}

		else
		{
			echo graf(gTxt('no_ips_banned'),' class="indicator"');
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

		return multi_edit($methods, 'discuss', 'discuss_multi_edit', $page, $sort, $dir, $crit, $search_method);
	}

// -------------------------------------------------------------
	function discuss_multi_edit()
	{
		//FIXME, this method needs some refactoring

		$selected = ps('selected');
		$method = ps('edit_method');
		$done = array();

		if ($selected and is_array($selected))
		{
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
					// Delete and if successful update comment count
					if (safe_delete('txp_discuss', "discussid = $id"))
						$done[] = $id;

					callback_event('discuss_deleted', '', 0, $done);
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
					'delete'      => gTxt('comments_deleted', array('{list}' => $done)),
					'ban'         => gTxt('ips_banned', array('{list}' => $done)),
					'spam'        => gTxt('comments_marked_spam', array('{list}' => $done)),
					'unmoderated' => gTxt('comments_marked_unmoderated', array('{list}' => $done)),
					'visible'     => gTxt('comments_marked_visible', array('{list}' => $done))
				);

				update_lastmod();

				return discuss_list($messages[$method]);
			}
		}

		return discuss_list();
	}

?>
