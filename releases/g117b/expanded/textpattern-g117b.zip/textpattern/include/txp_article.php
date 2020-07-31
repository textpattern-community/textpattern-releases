<?php
/*
	This is Textpattern
	Copyright 2004 by Dean Allen 
 	All rights reserved.

	Use of this software indicates acceptance of the Textpattern license agreement 
*/
#	dmp($_POST);

	$vars = array(
		'ID','Title','Title_html','Body','Body_html','Excerpt','textile_excerpt',
		'textile_body', 'Keywords','Status','Posted','Section','Category1','Category2',
		'Annotate','AnnotateInvite','publish_now','reset_time','AuthorID','sPosted',
		'LastModID','sLastMod','override_form','from_view','year','month',
		'day','hour','minute'
	);

	$save = gps('save');
	if ($save) $step = 'save';

	$publish = gps('publish');
	if ($publish) $step = 'publish';

	$statuses = array(
			1 => gTxt('draft'),
			2 => gTxt('hidden'),
			3 => gTxt('pending'),
			4 => strong(gTxt('live'))
		);
		
	switch(strtolower($step)) {
		case "":         article_edit();    break;
		case "list":     article_list();    break;
		case "create":   article_edit();    break;
		case "publish":  article_post();    break;
		case "edit":     article_edit();    break;
		case "save":     article_save();    break;
		case "delete":   article_delete();
	}

//--------------------------------------------------------------
	function article_list($message="",$post='')
	{		
		extract(get_prefs());
		$lvars = array("page","order","sort","crit");
		extract(gpsa($lvars));
		global $statuses,$step;
		
		if ($post or $step=='list') pagetop("Textpattern",$message);

		$total = getCount('textpattern',"1");  
		$limit = 15;  
		$numPages = ceil($total/$limit);  
		$page = (!$page) ? 1 : $page;

		$offset = ($page - 1) * $limit;

		if (!$order) $order = "Posted";
		if (!$sort) $sort = "asc";
		if ($sort == "desc") { $sort = "asc"; } else { $sort = "desc"; }

		$critlink = ($crit) ? a."crit=" . $crit : ''; 
		$criteria = ($crit) ? "where Title rlike '$crit' or Body rlike '$crit'" : '';

		$rs = getRows("select *, unix_timestamp(Posted) as uPosted from textpattern 
			$criteria order by $order $sort limit $offset,$limit");
		
		if ($rs) {	
			echo startTable('list'),
			'<tr>',
				column_head(gTxt('posted'),'Posted','article','yes',$sort),
				column_head(gTxt('title'),'Title','article','link',$sort),
				($use_sections==1)
				?	column_head(gTxt('section'), 'Section', 'article', 'yes', $sort)
				:	'',
				($use_categories==1)
				?	column_head(gTxt('category1'),'Category1','article','yes',$sort).
					column_head(gTxt('category2'),'Category2','article','yes',$sort)
				:	'',
				hCell(gTxt('Author')),
				column_head(gTxt('status'), 'Status', 'article', 'yes', $sort),
				td(),
			'</tr>';
	
			foreach ($rs as $a) {
				extract($a);
				
				if($use_categories==1) { $cat1 = $Category1; $cat2 = $Category2; }
		
				$stat = $statuses[$Status];
				if($use_sections==1) $sect = $Section;
				$adate = date("d M y",$uPosted+$timeoffset);
		
				$alink = eLink('article','edit','ID',$ID,$adate);
				$tlink = eLink('article','edit','ID',$ID,$Title);
				$dlink = dLink('article','delete','dID',$ID);
				
				echo "<tr>".n,
					td($alink),
					td($tlink,200),
					($use_sections==1) ? td($sect,75) : '',
					($use_categories==1) ? td($cat1,'75').td($cat2,75) : '',
					td($AuthorID),
					td($stat,45),
					td($dlink,10),
				'</tr>'.n;
			}
			
			echo 
			'<tr>'.n,
				td(),
				tdcs(
					form(
						fInput('text','crit',$crit,'edit').
						eInput("article").sInput('list').
						fInput("submit","search",gTxt('search'),"smallbox")
					)
				,2),
			'<td colspan="4" style="text-align:right;vertical-align:middle">';

			$nav[] = ($page > 1) 
			?	PrevNextLink("article",$page-1,gTxt('prev'),'prev')
			:	'';

			$nav[] = sp.small($page. '/'.$numPages).sp;

			$nav[] = ($page != $numPages) 
			?	PrevNextLink("article",$page+1,gTxt('next'),'next')
			:	'';
			
			echo graf(join('',$nav));
			
			echo "</td></tr></table>";
			unset($order);
			}
		}

//--------------------------------------------------------------
	function article_post()
	{
		global $txp_user,$vars,$txpcfg;		
		extract(get_prefs());
		$incoming = gpsa($vars);

		include_once $txpcfg['txpath'].'/lib/classTextile.php';
		$textile = new Textile();
		
		if ($use_textile==0) {
			$incoming['Body_html'] = trim($incoming['Body']);
		} else if ($use_textile==1) {
			$incoming['Body_html'] = nl2br(trim($incoming['Body']));
		} else if ($use_textile==2 && $incoming['textile_body']) {
			$incoming['Body_html'] = $textile->TextileThis($incoming['Body']);
			$incoming['Title'] = $textile->TextileThis($incoming['Title'],'',1);
		}
		
		if ($incoming['textile_excerpt']) {
			$incoming['Excerpt'] = $textile->TextileThis($incoming['Excerpt'],1);
		}

			extract(doSlash($incoming));

			if ($publish_now==1) {
				$when = 'now()';
			} else {
				$when = strtotime($year.'-'.$month.'-'.$day.' '.$hour.':'.$minute.":00")-$timeoffset;
				$when = "from_unixtime($when)";
			}
		
			
			$myprivs = fetch('privs','txp_users','name',$txp_user);
			if ($myprivs==5 && $Status==4) $Status = 3;

			$query="
				insert into textpattern set 
				Title          = '$Title',
				Body           = '$Body',
				Body_html      = '$Body_html',
				Excerpt        = '$Excerpt',
				Keywords       = '$Keywords',
				Status         = '$Status',
				Posted         = $when,
				LastMod        = now(),
				AuthorID       = '$txp_user',
				Section        = '$Section',
				Category1      = '$Category1',
				Category2      = '$Category2',
				Annotate       = '$Annotate',
				override_form  = '$override_form',
				AnnotateInvite = '$AnnotateInvite'";
			
			safe_query($query);

		if ($Status==4) {

			safe_query("update txp_prefs set val=now() where `name`='lastmod'");
			$message = gTxt('article_posted');

			include_once $txpcfg['txpath'].'/lib/IXRClass.php';

			$tx_client = new IXR_Client('http://textpattern.com/xmlrpc/');
			$tx_client->query('ping.Textpattern', $sitename, $siteurl);

			if ($ping_weblogsdotcom==1) {
				$wl_client = new IXR_Client('http://rpc.weblogs.com/RPC2');
				$wl_client->query('weblogUpdates.ping', $sitename, 'http://'.$siteurl);
			}		
		
		} else { 	
			     if ($Status==3) { $message = gTxt("article_saved_pending"); } 
			else if ($Status==2) { $message = gTxt("article_saved_hidden");  } 
			else if ($Status==1) { $message = gTxt("article_saved_draft");   }
		}
			article_list($message,1);
	}

//--------------------------------------------------------------
	function article_save()
	{
		global $txp_user,$vars,$txpcfg;
		extract(get_prefs());
		$incoming = gpsa($vars);
		
		$oldstatus = fetch('Status','textpattern','ID',$incoming['ID']);
		
		if ($use_textile==0) {

			$incoming['Body_html'] = trim($incoming['Body']);

		} else if ($use_textile==1) {

			$incoming['Body_html'] = nl2br(trim($incoming['Body']));

		} else if ($use_textile==2 && $incoming['textile_body']) {

			include_once $txpcfg['txpath'].'/lib/classTextile.php';
			$textile = new Textile();
			$incoming['Body_html'] = $textile->TextileThis($incoming['Body']);
			$incoming['Title'] = $textile->TextileThis($incoming['Title'],'',1);
		}
		
		if ($incoming['textile_excerpt']) {
			$incoming['Excerpt'] = $textile->TextileThis($incoming['Excerpt'],1);
		}

		$myprivs = fetch('privs','txp_users','name',$txp_user);
		if ($myprivs==5 && $Status==4) $Status = 3;
							
		extract(doSlash($incoming));

		$q = "update textpattern set 
			Title          = '$Title',
			Body           = '$Body',
			Body_html      = '$Body_html',
			Excerpt        = '$Excerpt',
			Keywords       = '$Keywords',
			Status         = '$Status',
			LastMod        = now(),
			LastModID      = '$txp_user',
			Section        = '$Section',
			Category1      = '$Category1',
			Category2      = '$Category2',
			Annotate       = '$Annotate',
			override_form  = '$override_form',
			AnnotateInvite = '$AnnotateInvite'";
			
		if($reset_time) {
			$q .= ", Posted=now()"; 
		} else {
			$when = strtotime($year.'-'.$month.'-'.$day.' '.$hour.':'.$minute.":00")-$timeoffset;
			$when = "from_unixtime($when)";
			$q .= ", Posted=$when";
		}
	
		$q .= " where ID='$ID'";
	
		safe_query($q);
		
		if($Status == 4) {
			if ($oldstatus < 4) {
				include_once $txpcfg['txpath'].'/lib/IXRClass.php';
				$tx_client = new IXR_Client('http://textpattern.com/xmlrpc/');
				$tx_client->query('ping.Textpattern', $sitename, $siteurl);

				if ($ping_weblogsdotcom==1) {
					$wl_client = new IXR_Client('http://rpc.weblogs.com/RPC2');
					$wl_client->query('weblogUpdates.ping', $sitename, 'http://'.$siteurl);
				}		
			}
			safe_query("update txp_prefs set val=now() where name='lastmod'");
			$message = gTxt("article_saved");
		} else { 
			     if ($Status==3) { $message = gTxt("article_saved_pending"); } 
			else if ($Status==2) { $message = gTxt("article_saved_hidden");	 } 
			else if ($Status==1) { $message = gTxt("article_saved_draft");	 }	
		}
		article_edit($message);
	}

//--------------------------------------------------------------
	function article_edit($message="")
	{
		pagetop("Textpattern",$message);
		global $txpcfg,$txpac,$txp_user,$vars;

		$myprivs = fetch('privs','txp_users','name',$txp_user);
		
		extract(get_prefs());
		extract(gpsa(array('ID','view','from_view','step')));

		include_once $txpcfg['txpath'].'/lib/classTextile.php';
		$textile = new Textile();

		if (!$view) $view = "text";
		if (!$step) $step = "create";
								
		if ($step == "edit" 
			&& $view=="text" 
			&& !empty($ID) 
			&& $from_view != "preview" 
			&& $from_view != 'html') {
			
			$pull = true;          //-- it's an existing article - off we go to the db
	
			$rs = getRow("select *,
			unix_timestamp(Posted) as sPosted,
			unix_timestamp(LastMod) as sLastMod
			from textpattern where ID=$ID");

			extract($rs);
						
			if ($AnnotateInvite!= $comments_default_invite) {
				$AnnotateInvite = $AnnotateInvite;
			} else {
				$AnnotateInvite = $comments_default_invite;
			}
		} else {
		
			$pull = false;         //-- assume they came from post
		
			if (!$from_view or $from_view=='text') {
				extract(gpsa($vars));
			} elseif($from_view=='preview' or $from_view=='html') {
					// coming from either html or preview
				if (isset($_POST['store'])) {
					$store = unserialize(base64_decode($_POST['store']));
					
#					dmp($store);
					
					extract($store);
				}
			}
			
			foreach($vars as $var){
				if(isset($$var)){
					$store_out[$var] = $$var;		
				}
			}
#			dmp($store_out);
		}

		$GLOBALS['Keywords'] = $Keywords;
		$GLOBALS['step'] = $step;

		if ($step!='create') {

			// Previous record?				
			$prev_id = checkIfNeighbour('prev',$sPosted);
			
			// Next record?
			$next_id = checkIfNeighbour('next',$sPosted);
		}

		echo '<form action="index.php" method="post" name="article">';

		if (!empty($store_out)) echo hInput('store',base64_encode(serialize($store_out)));		
		echo
		hInput('ID',$ID),
		eInput('article'),
		sInput($step);

		echo
		'<input type="hidden" name="view" />',
		startTable('edit');

		echo '<tr><td>&nbsp;</td><td colspan="3">',

	//-- title input -------------- 

				($view=='preview')
				?	hed(ucfirst(gTxt('preview')),2).graf($Title)
				:	'',
				($view=='html')
				?	hed('XHTML',2).graf($Title)
				:	'',
				($view=='text')
				?	br.'<input type="text" name="Title" value="'.
						cleanfInput($Title).
						'" class="edit" size="40" tabindex="1" />'
				:	'',
		'</td></tr>';

	//-- article input --------------

  		echo '<tr>
  		<td valign="top">',

	//-- textile help --------------

		($view=='text' && $use_textile==2) ?
		graf(
		'<a href="#" onclick="toggleDisplay(\'textile_help\');">'.gTxt('textile_help').'</a>
		<div id="textile_help" style="display:none;">'.sidehelp().'</div>') : '&nbsp;',

		($view=='text') ?
		
		graf(
		'<a href="#" onclick="toggleDisplay(\'advanced\');">'.gTxt('advanced_options').'</a>
		<div id="advanced" style="display:none;">'.advanced().'</div>') : '&nbsp;',
		
  		'</td>
    	<td valign="top" style="width:400px">';

    	if ($view=="preview") { 

			if ($use_textile==2) {
				echo $textile->TextileThis($Body);
			} else if ($use_textile==1) {
				echo nl2br($Body);
			} else if ($use_textile==0) {
				echo $Body;
			}

    	} elseif($view=="html") {

			if ($use_textile==2) {
				$bod = $textile->TextileThis($Body);
			} else if ($use_textile==1) {
				$bod = nl2br($Body);
			} else if ($use_textile==0) {
				$bod = $Body;
			}

			echo tag(str_replace(array(n,t),
					array(br,sp.sp.sp.sp),htmlspecialchars($bod)),'code');
		} else {

			echo '<textarea style="width:400px;height:360px" rows="1" cols="1" name="Body" tabindex="2">',htmlspecialchars($Body),'</textarea>';

		}

	//-- excerpt --------------------

		if ($txpac['articles_use_excerpts']) {

			if ($view=='text') {
			
				echo graf(gTxt('excerpt').popHelp('excerpt').br.
				'<textarea style="width:400px;height:70px" rows="1" cols="1" name="Excerpt" tabindex="3">'.$Excerpt.'</textarea>');
		
			} else {
	
				echo '<hr width="50%" />';
				
				echo ($textile_excerpt)
				?	($view=='preview')
					?	graf($textile->textileThis($Excerpt),1)
					:	tag(str_replace(array(n,t),
							array(br,sp.sp.sp.sp),htmlspecialchars(
								$textile->TextileThis($Excerpt),1)),'code')
				:	graf($Excerpt);
			}
		}


	//-- author --------------
	
		if ($view=="text" && $step != "create") {
			echo "<p><small>".gTxt('posted_by')." $AuthorID: ",date("H:i, d M y",$sPosted + $timeoffset);
			if($sPosted != $sLastMod) {
				echo br.gTxt('modified_by')." $LastModID: ", date("H:i, d M y",$sLastMod + $timeoffset);
			}
				echo '</small></p>';
			}

	echo hInput('from_view',$view),
	'</td>';
	echo '<td valign="top" align="left" width="20">';

  	//-- layer tabs -------------------

		echo ($use_textile==2)
		?	tab('text',$view).tab('html',$view).tab('preview',$view)
		:	'&#160;';
	echo '</td>';
?>	
<td width="200" valign="top" style="padding-left:10px" align="left" id="articleside">
<?php 
	//-- prev/next article links -- 

		if ($view=='text') {
			if ($step!='create' and ($prev_id or $next_id)) {
				echo '<p>',
				($prev_id)
				?	prevnext_link('&#8249;'.gTxt('prev'),'article','edit',
						$prev_id,gTxt('prev'))
				:	'',
				($next_id)
				?	prevnext_link(gTxt('next').'&#8250;','article','edit',
						$next_id,gTxt('next'))
				:	'',
				'</p>';
			}
		}
			
	//-- status radios --------------
	 	
			echo ($view == 'text') ? n.graf(status_radio($Status)).n : '';


	//-- category selects -----------

			echo ($use_categories && $view=='text')
			?	graf(gTxt('categorize').
				' ['.eLink('category','','','',gTxt('edit')).']'.br.
				category_popup('Category1',$Category1).
				category_popup('Category2',$Category2))
			:	'';

	//-- section select --------------

			if(!$from_view && !$pull) $Section = getDefaultSection();
			echo ($use_sections && $view=='text')
			?	graf(gTxt('section').' ['.eLink('section','','','',gTxt('edit')).']'.br.
				section_popup($Section))
			:	'';

	//-- comments stuff --------------

			if($step=="create") {
				$AnnotateInvite = $comments_default_invite;
				if ($comments_on_default==1) { $Annotate = 1; }
			}
			echo ($use_comments==1 && $view=='text')
			?	graf(gTxt('comments').onoffRadio("Annotate",$Annotate).'<br />'.
				gTxt('comment_invitation').'<br />'.
				fInput('text','AnnotateInvite',$AnnotateInvite,'edit'))
			:	'';
			

	//-- timestamp ------------------- 

		if ($step == "create") {
			if ($view == 'text') {
			echo
			graf(checkbox('publish_now','1').gTxt('set_to_now')),
			'<p>',gTxt('or_publish_at'),popHelp("timestamp"),br,
				tsi('year','Y',time()),
				tsi('month','m',time()),
				tsi('day','d',time()), sp,
				tsi('hour','H',time()), ':',
				tsi('minute','i',time()),
			'</p>';
			}

	//-- publish button --------------

			if ($view == 'text') {
				echo
				($myprivs == 1 or $myprivs==2 or $myprivs==3) ?
				fInput('submit','publish',gTxt('publish'),"publish") :
				fInput('submit','save',gTxt('save'),"publish");
			}


		} else {

			
			if ($view == 'text') {
				echo
				'<p>',gTxt('published_at'),popHelp("timestamp"),br,
					tsi('year','Y',$sPosted),
					tsi('month','m',$sPosted),
					tsi('day','d',$sPosted), sp,
					tsi('hour','H',$sPosted), ':',
					tsi('minute','i',$sPosted),
				'</p>',
					hInput('sPosted',$sPosted),
					hInput('sLastMod',$sLastMod),
					hInput('AuthorID',$AuthorID),
					hInput('LastModID',$LastModID),
					graf(checkbox('reset_time','1',0).gTxt('reset_time'));
			}

	//-- save button --------------

			if ($view == 'text') {
				echo
				($myprivs == 1 or $myprivs==2 or $myprivs==3 or $AuthorID==$txp_user) 
				?   fInput('submit','save',gTxt('save'),"publish")
				:	graf('insufficient privileges to edit this article');
			}
		}

    	echo '</td></tr></table></form>';
    	
		if ($view=='text') article_list();
	
	}


// -------------------------------------------------------------
	function checkIfNeighbour($whichway,$sPosted)
	{
		$dir = ($whichway == 'prev') ? '<' : '>'; 
		$ord = ($whichway == 'prev') ? 'desc' : 'asc'; 

		return getThing("select ID from textpattern 
			where Posted $dir from_unixtime($sPosted) order by Posted $ord limit 1");
	}

//--------------------------------------------------------------
	function tsi($name,$datevar,$time)
	{
		global $timeoffset;
		$size = ($name=='year') ? 4 : 2;

		return '<input type="text" name="'.$name.'" value="'.
			date($datevar,$time+$timeoffset)
		.'" size="'.$size.'" maxlength="'.$size.'" class="edit" />'."\n";
	}

//--------------------------------------------------------------
	function article_delete()
	{
		$dID = gps('dID');
		$rs = safe_query("delete from textpattern where ID=$dID");
		if ($rs) article_list(messenger('article',$dID,'deleted'),1);
	}

// -------------------------------------------------------------
	function advanced() 
	{
		global $txpac,$override_form,$view,$Keywords;
		return join('',array(
			graf(
				gTxt('use_textile').br.
				checkbox('textile_body',1).strtolower(gTxt('article')).br.
				checkbox('textile_excerpt',1).strtolower(gTxt('excerpt'))
			),
			($txpac['allow_form_override'])
			?	graf(gTxt('override_default_form').br.
					form_pop($override_form).popHelp('override_form'))
			:	'',
			graf(gTxt('keywords').popHelp('keywords').br.
				'<textarea name="Keywords" style="width:100px;height:100px">'.
				$Keywords.'</textarea>'
			)
		));
	}

//--------------------------------------------------------------
	function sidehelp()
	{
		global $use_textile;
		$out='<p><small>';

		if ($use_textile==2) {
			$out .=
			gTxt('header').': <strong>h<em>n</em>.</strong>'.
				popHelpSubtle('header',400,400).br.
			gTxt('blockquote').': <strong>bq.</strong>'.
				popHelpSubtle('blockquote',400,400).sp.br.
			gTxt('numeric_list').': <strong>#</strong>'.
				popHelpSubtle('numeric',400,400).sp.br.
			gTxt('bulleted_list').': <strong>*</strong>'.
				popHelpSubtle('bulleted',400,400).
		
			'</small></p><p><small>'.
	
			'_<em>'.gTxt('emphasis').'</em>_'.
				popHelpSubtle('italic',400,400).sp.br.
			'*<strong>'.gTxt('strong').'</strong>*'.
				popHelpSubtle('bold',400,400).sp.br.
			'??<cite>'.gTxt('citation').'</cite>??'.
				popHelpSubtle('cite',500,300).sp.br.
			'-'.gTxt('deleted_text').'-'.
				popHelpSubtle('delete',400,300).sp.br.
			'+'.gTxt('inserted_text').'+'.
				popHelpSubtle('insert',400,300).sp.br.
			'^'.gTxt('superscript').'^'.
				popHelpSubtle('super',400,300).sp.br.
			'~'.gTxt('subscript').'~'.
				popHelpSubtle('subscript',400,400).
	
			'</small></p><p><small>'.
				'"'.gTxt('linktext').'":url'.
					popHelpSubtle('link',400,500).sp.br.
			'</small></p><p><small>'.
				'!'.gTxt('imageurl').'!'.
					popHelpSubtle('image',500,500).		
			'</small></p>'.
			'<a href="http://textism.com/tools/textile/" target="_blank">'.gTxt('More').'</a>';
		}			
	
	   return $out;
	}

//--------------------------------------------------------------
	function status_radio($Status) 
	{
		global $statuses;
		$Status = (!$Status) ? 4 : $Status;
		foreach($statuses as $a=>$b) {
			$out[] = radio('Status',$a,($Status==$a)?1:0).$b;	
		}
		return join('<br />'.n,$out);
	}

//--------------------------------------------------------------
	function category_popup($name,$val) 
	{
		$rs = getTree("root",'article');
		if ($rs) {
			return treeSelectInput($name,$rs,$val);
		}
		return false;
	}

//--------------------------------------------------------------
	function section_popup($Section) 
	{
		$rs = getRows("select name from txp_section where name!='default'");
		if ($rs) {	
			foreach ($rs as $a) $arr[$a[0]] = $a[0];
			return selectInput("Section", $arr, $Section);
		}
		return false;
	}

//--------------------------------------------------------------
	function tab($tabevent,$view) 
	{
		$state = ($view==$tabevent) ? 'up' : 'down';
		$img = 'txp_img/'.$tabevent.$state.'.gif';
		$out = '<img src="'.$img.'"';
		$out.=($tabevent!=$view) ? ' onclick="document.article.view.value=\''.$tabevent.'\'; document.article.submit(); return false;"' : "";
		$out.= ' height="100" width="19" alt="" />';
      	return $out;
	}

//--------------------------------------------------------------
	function getDefaultSection() 
	{
		return getThing("select name from txp_section where is_default=1");
	}

// -------------------------------------------------------------
	function form_pop($form)
	{
		$arr = array(' ');
		$rs = getThings("select name from txp_form 
			where type='article' and name!='default'");
		if($rs) {
			foreach($rs as $a) {
				$arr[$a] = $a;
			}
			return selectInput('override_form',$arr,$form);
		}
	}
?>
