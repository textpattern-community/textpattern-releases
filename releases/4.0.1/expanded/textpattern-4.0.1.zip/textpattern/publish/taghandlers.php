<?php

/*
	This is Textpattern
	Copyright 2005 by Dean Allen - all rights reserved.

	Use of this software denotes acceptance of the Textpattern license agreement 

$HeadURL$
$LastChangedRevision$

*/

// -------------------------------------------------------------
	function page_title($atts) 
	{
		global $sitename,$id,$c,$q,$parentid,$pg;
		extract(lAtts(array('separator' => ': '),$atts));
		$s = $sitename;
		$sep = $separator;
		if ($id)       return $s.$sep.safe_field('Title','textpattern',"ID = $id");
		if ($c)        return $s.$sep.fetch_category_title($c);
		if ($q)        return $s.$sep.gTxt('search_results').$sep.' '.$q;
		if ($pg)       return $s.$sep.gTxt('page').' '.$pg;
		if ($parentid) return $s.$sep.gTxt('comments_on').' '.
			safe_field('Title','textpattern',"ID = '$parentid'");
		return $sitename;
	}

// -------------------------------------------------------------
	function css($atts) 	// generates the css src in <head>
	{
		global $s;
		extract(lAtts(array('n' => ''),$atts));
		if ($n) return hu.'textpattern/css.php?n='.$n;
		return hu.'textpattern/css.php?s='.$s;
	}

// -------------------------------------------------------------
	function image($atts) 
	{
		global $img_dir;
		static $cache = array();
		extract(lAtts(array(
			'id'    => '',
			'name'  => '',
			'style' => '',
			'align' => '',
			'class' => ''
		),$atts));
		
		if ($name) {
			if (isset($cache['n'][$name]))
			{
				$rs = $cache['n'][$name];
			} else {
				$name = doSlash($name);
				$rs = safe_row("*", "txp_image", "name='$name' limit 1");
				$cache['n'][$name] = $rs;
			}
		} elseif ($id) {
			if (isset($cache['i'][$id]))
			{
				$rs = $cache['i'][$id];
			} else {
				$id = intval($id);
				$rs = safe_row("*", "txp_image", "id='$id' limit 1");
				$cache['i'][$id] = $rs;
			}
		} else return;
		
		if ($rs) {
			extract($rs);
			$out = array(
				'<img',
				'src="'.hu.$img_dir.'/'.$id.$ext.'"',
				'height="'.$h.'" width="'.$w.'" alt="'.$alt.'"',				
				($style) ? 'style="'.$style.'"' : '',
				($align) ? 'align="'.$align.'"' : '',
				($class) ? 'class="'.$class.'"' : '',
				'/>'
			);
			
			return join(' ',$out);
		}
		return '<txp:notice message="malformed image tag" />';
	}

// -------------------------------------------------------------
    function thumbnail($atts) 
    {
        global $img_dir;
		extract(lAtts(array(
			'id'        => '',
			'name'      => '',
			'thumbnail' => '',
			'poplink'   => '',
			'style'     => '',
			'align'     => ''
		),$atts));
		
        if (!empty($name)) {
            $name = doSlash($name);
            $rs = safe_row("*", "txp_image", "name='$name' limit 1");
        } elseif (!empty($id)) {
            $rs = safe_row("*", "txp_image", "id='$id' limit 1");
        } else return;

        if ($rs) {
            extract($rs);
            if($thumbnail) {
                $out = array(
                    ($poplink)
                    ?   '<a href="'.hu.$img_dir.'/'.$id.$ext.
                            '" onclick="window.open(this.href, \'popupwindow\', \'width='.
                            $w.',height='.$h.',scrollbars,resizable\'); return false;">'
                    :   '',
                    '<img src="'.hu.$img_dir.'/'.$id.'t'.$ext.'"',
                    ' alt="'.$alt.'"',
                    ($style) ? 'style="'.$style.'"' : '',
                    ($align) ? 'align="'.$align.'"' : '',
                    '/>',
                    ($poplink) ? '</a>' : ''
                );
                return join(' ',$out);
            }
        }
    }

// -------------------------------------------------------------
	function output_form($atts) 
	{
		extract(lAtts(array('form' => ''),$atts));
		return ($form) ? parse(fetch('form','txp_form','name',doSlash($form))) : '';
	}

// -------------------------------------------------------------
	function feed_link($atts) // simple link to rss or atom feed
	{
		extract(lAtts(array(
			'label'    => '',
			'break'    => br,
			'wraptag'  => '',
			'category' => '',
			'section'  => '', 
			'flavor'   => 'rss'
		),$atts));
		
		$url = pagelinkurl(array('c'=>$category, 's'=>$section, $flavor=>'1'));

		$out = '<a href="'.$url.'" title="XML feed">'.$label.'</a>';
		return ($wraptag) ? tag($out,$wraptag) : $out;
	}

// -------------------------------------------------------------
	function link_feed_link($atts) // rss or atom feed of links
	{
		extract(lAtts(array(
			'label'    => '',
			'break'    => br,
			'wraptag'  => '',
			'category' => '',
			'flavor'   => 'rss'
		),$atts));
	
		$url = pagelinkurl(array('c'=>$category, $flavor=>'1', 'area'=>'link'));

		$out = '<a href="'.$url.'" title="XML feed">'.$label.'</a>';
		
		return ($wraptag) ? tag($out,$wraptag) : $out;
	}

// -------------------------------------------------------------
	function linklist($atts) 
	{
		global $thislink;
		extract(lAtts(array(
			'form'     => 'plainlinks',
			'sort'     => 'linksort',
			'label'    => '',
			'break'    => '',
			'limit'    => '',
			'wraptag'  => '',
			'category' => '',
			'class'    => __FUNCTION__,
		),$atts));
	
		$Form = fetch_form($form);
		
		$qparts = array(
			($category) ? "category='$category'" : '1',
			"order by",
			$sort,
			($limit) ? "limit $limit" : ''
		);
		
		$rs = safe_rows_start("*","txp_link",join(' ',$qparts));
	
		if ($rs) {
			if ($label)
				$outlist[] = $label;
		
			while ($a = nextRow($rs)) {
				extract($a);
				$linkname = str_replace("& ","&#38; ", $linkname);
				$link = '<a href="'.doSpecial($url).'">'.$linkname.'</a>';
				$linkdesctitle = '<a href="'.doSpecial($url).'" title="'.$description.'">'.$linkname.'</a>';
				$thislink = $a;

				$out = str_replace("<txp:link />", $link, $Form);
				$out = str_replace("<txp:linkdesctitle />", $linkdesctitle, $out);
				$out = str_replace("<txp:link_description />", $description, $out);
			
				$outlist[] = parse($out);
			}
			
			if (!empty($outlist)) {
				return doWrap($outlist, $wraptag, $break, $class);
			}
		}
		return false;
	}


// -------------------------------------------------------------
	function eE($txt) // convert email address into unicode entities
	{
		 for ($i=0;$i<strlen($txt);$i++) { 
			  $ent[] = "&#".ord(substr($txt,$i,1)).";"; 
		 } 
		 if (!empty($ent)) return join('',$ent); 
	}

// -------------------------------------------------------------
	function email($atts) // simple contact link
	{
		extract(lAtts(array(
			'email'    => '',
			'linktext' => gTxt('contact'),
			'title'    => ''
		),$atts));

		if($email) {
			$out  = array(
				'<a href="'.eE('mailto:'.$email).'"',
				($title) ? ' title="'.$title.'"' : '',
				'>',
				$linktext,
				'</a>'
			);
			return join('',$out);
		}
		return '<txp:notice message="malformed email tag />"';
	}
	
// -------------------------------------------------------------
	function password_protect($atts)
	{
		ob_start();

		extract(lAtts(array(
			'login' => '',
			'pass'  => ''
		),$atts));

		$au = serverSet('PHP_AUTH_USER');
		$ap = serverSet('PHP_AUTH_PW');
		if ($login && $pass) {
			if (!$au || !$ap || $au!= $login || $ap!= $pass) {
				header('WWW-Authenticate: Basic realm="Private"'); 
				header('HTTP/1.0 401 Unauthorized');  
				exit(gTxt('auth_required'));
			}
		}
	}

// -------------------------------------------------------------
	function recent_articles($atts)
	{
		extract(lAtts(array(
			'label'    => '',
			'break'    => br,
			'wraptag'  => '',
			'limit'    => 10,
			'category' => '',
			'sortby'   => 'Posted',
			'sortdir'  => 'desc',
			'class'    => __FUNCTION__
		),$atts));

		$catq = ($category) ? "and (Category1='".doSlash($category)."' 
			or Category2='".doSlash($category)."')" : '';

		$rs = safe_rows_start(
			"*, id as thisid, unix_timestamp(Posted) as posted", 
			"textpattern", 
			"Status = 4 and Posted <= now() $catq order by $sortby $sortdir limit 0,$limit"
		);
		
		if ($rs) {
			if ($label) $out[] = $label;
			while ($a = nextRow($rs)) {
				extract($a);
				$out[] = href($Title,permlinkurl($a));
			}
			if (is_array($out)) {
				return doWrap($out, $wraptag, $break, $class);
			}
		}
		return '';
	}

// -------------------------------------------------------------
	function recent_comments($atts)
	{
		extract(lAtts(array(
			'label'    => '',
			'break'    => br,
			'wraptag'  => '',
			'limit'    => 10,
			'class'    => __FUNCTION__
		),$atts));

		$rs = safe_rows_start("*",'txp_discuss',"visible=1 order by posted desc limit 0,$limit");

		if ($rs) {
			if ($label) $out[] = $label;
        	while ($a = nextRow($rs)) {
				extract($a);
				$Title = safe_field("Title",'textpattern',"ID=$parentid");
				$out[] = href($name.' ('.$Title.')', permlinkurl_id($parentid).'#c'.$discussid);
			}
			if (!empty($out)) {
				return doWrap($out, $wraptag, $break, $class);
			}
		}
		return '';
	}

// -------------------------------------------------------------
	function related_articles($atts)
	{
		extract(lAtts(array(
			'label'    => '',
			'break'    => br,
			'wraptag'  => '',
			'limit'    => 10,
			'class'    => __FUNCTION__
		),$atts));
		
		global $id,$thisid,$thisarticle;
		
		$id = ($thisid) ? $thisid : $id;

		$cats = doSlash(safe_row("Category1,Category2","textpattern", "ID='$id' limit 1"));

		if (!empty($cats['Category1']) or !empty($cats['Category2'])) {
			extract($cats);
			$cat_condition = array();
			if (!empty($Category1)) array_push($cat_condition, "(Category1='$Category1')","(Category2='$Category1')");
			if (!empty($Category2)) array_push($cat_condition, "(Category1='$Category2')","(Category2='$Category2')");
			$cat_condition = (count($cat_condition)) ? join(' or ',$cat_condition) : '';

			$q = array("select *, id as thisid, unix_timestamp(Posted) as posted from `".PFX."textpattern` where Status=4",
				($cat_condition) ? "and (". $cat_condition. ")" :'',
				"and Posted <= now() order by Posted desc limit 0,$limit");

			$rs = getRows(join(' ',$q));
	
			if ($rs) {
				if ($label) $out[] = $label;
				foreach($rs as $a) {
					extract($a);
					if ($thisid == $id) continue;
					$out[] = href($Title,permlinkurl($a));
				}
				if (is_array($out)) {
					return doWrap($out, $wraptag, $break, $class);
				}
			}
		}
		return '';
	}

// -------------------------------------------------------------
	function popup($atts)
	{
		global $pretext;
		
		$gc = $pretext['c'];
		$gs = $pretext['s'];
		
		extract(lAtts(array(
			'label'   => '',
			'wraptag' => '',
			'type'    => ''
		),$atts));

		$thetable = ($type=='s') ? 'section' : 'category';
		$out ='<select name="'.$type.'" onchange="submit(this.form)">'.n.
		t.'<option value=""></option>'.n;
		$q[] = "select name,title from `".PFX."txp_".$thetable."` where name != 'default'";
		$q[] = ($thetable=='category') ? "and type='article'" : '';
		$q[] = "order by name";

		$rs = getRows(join(' ',$q));
		if ($rs) {
			foreach ($rs as $a) {
				extract($a);
				if ($name=='root') continue;
				$sel = ($gc==$name or $gs==$name) ? 'selected="selected"' : '';
				$out .= t.t.'<option value="'.urlencode($name).'"'.$sel.'>'.
				$title.'</option>'.n;
				unset($selected);
			}
			$out.= '</select>';
			$out = ($label) ? $label.br.$out : $out;
			$out = ($wraptag) ? tag($out,$wraptag) : $out;
			$out.= '<noscript><input type="submit" value="go" /></noscript>';
			return '<form action="'.hu.'" method="get">'.n.$out.'</form>';
		}
	}

// -------------------------------------------------------------
	function category_list($atts) // output href list of site categories
	{
		extract(lAtts(array(
			'label'    => '',
			'break'    => br,
			'wraptag'  => '',
			'parent'   => '',
			'type'    => 'article',
			'class'    => __FUNCTION__
		),$atts));

		if ($parent) {
			$qs = safe_row("lft,rgt",'txp_category',"name='$parent'");
			if($qs) {
				extract($qs);
				$rs = safe_rows_start(
				  "name,title", 
				  "txp_category","name != 'default' and type='$type' and (lft between $lft and $rgt) order by lft asc"
				);
			}
		} else {
			$rs = safe_rows_start(
			  "name,title", 
			  "txp_category",
			  "name != 'default' and type='$type' order by name"
			);
		}

		if ($rs) {
			if ($label) $out[] = $label;
			while ($a = nextRow($rs)) {
				extract($a);
				if ($name=='root') continue;
				if($name) $out[] = tag(str_replace("& ","&#38; ", $title),'a',' href="'.pagelinkurl(array('c'=>$name)).'"');
			}
			if (is_array($out)) {
				return doWrap($out, $wraptag, $break, $class);
			}			
		}
		return '';
	}

// -------------------------------------------------------------
	function section_list($atts) // output href list of site sections
	{
		extract(lAtts(array(
			'label'   => '',
			'break'   => br,
			'wraptag' => '',
			'class'    => __FUNCTION__
		),$atts));
		
		$rs = safe_rows_start("name,title","txp_section","name != 'default' order by name");
		
		if ($rs) {
			if ($label) $out[] = $label;
			while ($a = nextRow($rs)) {
				extract($a);
				$url = pagelinkurl(array('s'=>$name));
				$out[] = tag($title, 'a', ' href="'.$url.'"');
			}
			if (is_array($out)) {
				return doWrap($out, $wraptag, $break, $class);
			}
		}
		return '';
	}

// -------------------------------------------------------------
	function search_input($atts) // input form for search queries
	{
		global $q, $permlink_mode;
		extract(lAtts(array(
			'form'    => 'search_input',
			'wraptag' => 'p',
			'size'    => '15',
			'label'   => 'Search',
			'button'  => '',
			'section' => '',
		),$atts));	

		if ($form) {
			$rs = fetch('form','txp_form','name',$form);
			if ($rs) {
				return $rs;
			}
		}

		$sub = (!empty($button)) ? '<input type="submit" value="'.$button.'" />' : '';
		$out = fInput('text','q',$q,'','','',$size);
		$out = (!empty($label)) ? $label.br.$out.$sub : $out.$sub;
		$out = ($wraptag) ? tag($out,$wraptag) : $out;
	
		if (!$section)
			return '<form action="'.hu.'" method="get">'.$out.'</form>';

		$url = pagelinkurl(array('s'=>$section));	
		return '<form action="'.$url.'" method="get">'.$out.'</form>';
	}

// -------------------------------------------------------------
	function link_to_next($atts, $thing) // link to next article, if it exists
	{
		global $thisarticle, $id;
		global $next_id, $next_title, $next_utitle, $next_posted;
		global $prev_id, $prev_title, $prev_utitle, $prev_posted;
		extract(lAtts(array(
			'showalways'   => 0,
		),$atts));

		if(!is_numeric(@$id)) {
			extract(getNextPrev(@$thisarticle['thisid'], @strftime('%Y-%m-%d %H:%M:%S', $thisarticle['posted']), @$GLOBALS['s']));
		}

		return ($next_id) ? href(parse($thing),permlinkurl_id($next_id)) : ($showalways ? parse($thing) : '');
	}
		
// -------------------------------------------------------------
	function link_to_prev($atts, $thing) // link to next article, if it exists
	{
		global $thisarticle, $id;
		global $next_id, $next_title, $next_utitle, $next_posted;
		global $prev_id, $prev_title, $prev_utitle, $prev_posted;
		extract(lAtts(array(
			'showalways'   => 0,
		),$atts));

		if(!is_numeric(@$id)) {
			extract(getNextPrev($thisarticle['thisid'], @strftime('%Y-%m-%d %H:%M:%S', $thisarticle['posted']), @$GLOBALS['s']));
		}

		return ($prev_id) ? href(parse($thing),permlinkurl_id($prev_id)) : ($showalways ? parse($thing) : '');
	}

// -------------------------------------------------------------
	function next_title()
	{
		return $GLOBALS['next_title'];
	}

// -------------------------------------------------------------
	function prev_title()
	{
		return $GLOBALS['prev_title'];
	}

// -------------------------------------------------------------
	function site_slogan()
	{
		return $GLOBALS['site_slogan'];
	}

// -------------------------------------------------------------
	function link_to_home($atts, $thing) 
	{
		extract(lAtts(array(
			'class' => ''
		),$atts));
		$cl = ($class) ? ' class="'.$class.'"' : '';
		if (!empty($thing)) {
			return '<a href="'.hu.'"'.$cl.'>'.parse($thing).'</a>';
		}
	}

// -------------------------------------------------------------
	function newer($atts, $thing, $match='') 
	{
		global $thispage,$permlink_mode, $pretext;
		extract($pretext);
				
		if (is_array($atts)) extract($atts);
		if (is_array($thispage))
			extract($thispage);

		if ($numPages > 1 && $pg > 1) {
			$nextpg = ($pg - 1 == 1) ? 0 : ($pg - 1);
			$url = pagelinkurl(array('pg' => $nextpg, 's' => @$pretext['s'], 'c' => @$pretext['c'], 'q' => @$pretext['q'], 'a' => @$pretext['a']));
			$out = array(
				'<a href="'.$url.'"',
				(empty($title)) ? '' : ' title="'.$title.'"',
				'>',
				$thing,
				'</a>');
			return join('',$out);
		} else return;
	}

// -------------------------------------------------------------
	function older($atts, $thing, $match='') 
	{
		global $thispage,$permlink_mode, $pretext;
		extract($pretext);

		if (is_array($atts)) extract($atts);
		if (is_array($thispage))
			extract($thispage); 
		
		if ($numPages > 1 && $pg != $numPages) {
			$nextpg = $pg + 1;
			$url = pagelinkurl(array('pg' => $nextpg, 's' => @$pretext['s'], 'c' => @$pretext['c'], 'q' => @$pretext['q'], 'a' => @$pretext['a']));
			$out = array(
				'<a href="'.$url.'"',
				(empty($title)) ? '' : ' title="'.$title.'"',
				'>',
				$thing,
				'</a>');
			return join('',$out);
		} else return;
	}


// -------------------------------------------------------------
	function text($atts) 
	{
		extract(lAtts(array('item' => ''),$atts));
		return ($item) ? gTxt($item) : '';
	}

// -------------------------------------------------------------
	function article_id() 
	{
		global $thisarticle;
		return $thisarticle['thisid'];
	}

// -------------------------------------------------------------
	function posted($atts) 
	{
		global $dateformat,$archive_dateformat,
				$pg,$c,$thisarticle,$id,$txpcfg;

		$date_offset = $thisarticle['posted'];

		extract(lAtts(array(
			'format' => '',
			'lang'   => ''
		),$atts));	

		if($format) {
			if($format=='since') {
				$date_out = since($thisarticle['posted']);
			} else {
				$date_out = safe_strftime($format,$date_offset);
			}

		} else {
		
			if ($pg or $id or $c) { 	
				$dateformat = $archive_dateformat; 
			}

			if($dateformat == "since") { 
				$date_out = since($thisarticle['posted']); 
			} else { 
				$date_out = safe_strftime($dateformat,$date_offset); 
			}
		}

		if(!empty($wraptag)) $date_out = tag($date_out,$wraptag);

		return $date_out;
	}

// -------------------------------------------------------------
	function comments_count($atts) 
	{
		global $thisarticle;

		$com_count = $thisarticle['comments_count'];
		return ($com_count > 0) ? $com_count : '';
	}

// -------------------------------------------------------------
	function comments_invite($atts) 
	{
		global $thisarticle,$is_article_list;
		if (empty($thisarticle)) return;
		
		extract($thisarticle);
		global $comments_mode;

		if (($annotate or $comments_count) && $is_article_list) {

			$ccount = ($comments_count) ?  ' ['.$comments_count.']' : '';
	
			if (!$comments_mode) {
				return '<a href="'.permlinkurl($thisarticle).'#'.gTxt('comment').
					'">'.$comments_invite.'</a>'. $ccount;
			} else {
				return "<a href=\"".hu."?parentid=$thisid\" onclick=\"window.open(this.href, 'popupwindow', 'width=500,height=500,scrollbars,resizable,status'); return false;\">".$comments_invite.'</a> '.$ccount;
	
			}
		}
	}
// -------------------------------------------------------------
	function comments_form($atts)
	{
		global $thisarticle, $comment_preview, $pretext;

		extract(lAtts(array(
			'id'		   => @$pretext['id'],
			'class'		=> __FUNCTION__,
			'form'		=> 'comment_form',
			'wraptag'	=> ''
		),$atts));

		# don't display the comment form at the bottom, since it's
		# already shown at the top
		if (ps('preview') and empty($comment_preview))
			return '';

		if (is_array($thisarticle)) extract($thisarticle);

		if (@$thisid) $id = $thisid;

		if ($id) {
			if (!checkCommentsAllowed($id)) {
				$out = graf(gTxt("comments_closed"));
			} elseif (gps('commented')) {
				$out = gTxt("comment_posted");
				if (@$GLOBALS['prefs']['comments_moderate'])
					$out .= " ". gTxt("comment_moderated");
				$out = graf($out, ' id="txpCommentInputForm"');
			} else {
				$out = commentForm($id,$atts);
			}

			return (!$wraptag ? $out : doTag($out,$wraptag,$class) );
		}
	}

// -------------------------------------------------------------
	# DEPRECATED - provided only for backwards compatibility
	# this functionality will be merged into comments_invite
	# no point in having two tags for one functionality
	function comments_annotateinvite($atts,$thing=NULL)
	{
		global $thisarticle, $pretext;

		extract(lAtts(array(
			'id'		   => @$pretext['id'],
			'class'		=> __FUNCTION__,
			'wraptag'	=> 'h3',
		),$atts));

		if (is_array($thisarticle)) extract($thisarticle);

		if (@$thisid) $id = $thisid;

		if ($id) {
			extract(
				safe_row(
					"Annotate,AnnotateInvite,unix_timestamp(Posted) as uPosted",
						"textpattern", "ID='{$id}'"
				)
			);

			if (!$thing)
				$thing = $AnnotateInvite;

			return (!$Annotate) ? '' : doTag($thing,$wraptag,$class,' id="'.gTxt('comment').'"');
		}
	}

// -------------------------------------------------------------
	function comments($atts)
	{
		global $thisarticle, $prefs, $comment_preview, $pretext;
		extract($prefs);

		extract(lAtts(array(
			'id'		   => @$pretext['id'],
			'form'		=> 'comments',
			'wraptag'	=> ($comments_are_ol ? 'ol' : ''),
			'break'		=> ($comments_are_ol ? 'li' : 'div'),
			'class'		=> __FUNCTION__,
			'breakclass'=> '',
		),$atts));	

		if (is_array($thisarticle)) extract($thisarticle);

		if (@$thisid) $id = $thisid;

		$Form = fetch_form($form);

		if (!empty($comment_preview)) {
			$preview = psas(array('name','email','web','message','parentid','remember'));
			$preview['time'] = time();
			$preview['discussid'] = 0;
			$preview['message'] = markup_comment($preview['message']);
			$GLOBALS['thiscomment'] = $preview;
			$comments[] = parse($Form).n;
			unset($GLOBALS['thiscomment']);
			$out = doWrap($comments,$wraptag,$break,$class,$breakclass);
		}
		else {
			$rs = safe_rows_start("*, unix_timestamp(posted) as time", "txp_discuss",
				"parentid='$id' and visible='1' order by posted asc");
							
			$out = '';

			if ($rs) {
				$comments = array();

				while($vars = nextRow($rs)) {
					$GLOBALS['thiscomment'] = $vars;
					$comments[] = parse($Form).n;
					unset($GLOBALS['thiscomment']);
				}

				$out .= doWrap($comments,$wraptag,$break,$class,$breakclass);
			}
		}

		return $out;
	}
	
// -------------------------------------------------------------
	function comment_permlink($atts,$thing) 
	{
		global $thisarticle, $thiscomment;
		extract($thiscomment);
		extract(lAtts(array(
			'anchor' => empty($thiscomment['has_anchor_tag']),
		),$atts));

		$dlink = permlinkurl($thisarticle).'#c'.$discussid;
		
		$thing = parse($thing);

		$name = ($anchor ? ' id="c'.$discussid.'"' : '');
	
		return tag($thing,'a',' href="'.$dlink.'"'.$name);
	}

// -------------------------------------------------------------
	function comment_id($atts) 
	{
		global $thiscomment;
		return $thiscomment['discussid'];
	}

// -------------------------------------------------------------
	function comment_name($atts) 
	{
		global $thiscomment, $prefs;
		extract($prefs);
		extract($thiscomment);
		$web = str_replace("http://", "", $web);

		if ($email && !$web && !$never_display_email)
			$name = '<a href="'.eE('mailto:'.$email).'"'.(@$txpac['comment_nofollow'] ? ' rel="nofollow"' : '').'>'.$name.'</a>';

		if ($web)
			$name = '<a href="http://'.$web.'" title="'.$web.'"'.(@$txpac['comment_nofollow'] ? ' rel="nofollow"' : '').'>'.$name.'</a>';

		return $name;
	}

// -------------------------------------------------------------
	function comment_email($atts) 
	{
		global $thiscomment;
		return $thiscomment['email'];
	}

// -------------------------------------------------------------
	function comment_web($atts) 
	{
		global $thiscomment;
		return $thiscomment['web'];
	}

// -------------------------------------------------------------
	function comment_time($atts) 
	{
		global $thiscomment,$comments_dateformat;
		if($comments_dateformat == "since") { 
			$comment_time = since($thiscomment['time'] + tz_offset()); 
		} else {
			$comment_time = safe_strftime($comments_dateformat,$thiscomment['time']); 
		}
		return $comment_time;
	}

// -------------------------------------------------------------
	function comment_message($atts) 
	{
		global $thiscomment;
		return $thiscomment['message'];
	}

// -------------------------------------------------------------
	function comment_anchor($atts) 
	{
		global $thiscomment;
		$thiscomment['has_anchor_tag'] = 1;
		return '<a id="c'.$thiscomment['discussid'].'"></a>';
	}

// -------------------------------------------------------------
// DEPRECATED: the old comment message body tag
	function message($atts) 
	{
		return comment_message($atts);
	}

// -------------------------------------------------------------
	function author($atts) 
	{
		global $thisarticle;		
		extract(lAtts(array('link' => ''),$atts));
		$author_name = get_author_name($thisarticle['authorid']);
		return (empty($link))
			? $author_name 
			: tag($author_name, 'a', ' href="'.pagelinkurl(array('author'=>$author_name)).'"');
	}
	
// -------------------------------------------------------------
	function body($atts) 
	{
		global $thisarticle;
		return $thisarticle['body'];
	}	
	
// -------------------------------------------------------------
	function title($atts) 
	{
		global $thisarticle;
		return $thisarticle['title'];	
	}

// -------------------------------------------------------------
	function excerpt($atts) 
	{
		global $thisarticle;
		return $thisarticle['excerpt'];	
	}

// -------------------------------------------------------------
	function category1($atts) 
	{
		global $thisarticle;
		extract(lAtts(array(
			'link' => 0,
			'title' => 0,
		),$atts));
		if ($thisarticle['category1']) {
			$cat_title = ($title ? fetch_category_title($thisarticle['category1']) : $thisarticle['category1']);
			if (!empty($link)) 
				return '<a href="'.pagelinkurl(array('c'=>$thisarticle['category1'])).'">'.
					$cat_title.'</a>';
			return $cat_title;
		}
	}
	
// -------------------------------------------------------------
	function category2($atts) 
	{
		global $thisarticle;
		extract(lAtts(array(
			'link' => 0,
			'title' => 0,
		),$atts));
		if ($thisarticle['category2']) {
			$cat_title = ($title ? fetch_category_title($thisarticle['category2']) : $thisarticle['category2']);
			if (!empty($link)) 
				return '<a href="'.pagelinkurl(array('c'=>$thisarticle['category2'])).'">'.
					$cat_title.'</a>';
			return $cat_title;
		}
	}
	
// -------------------------------------------------------------
	function section($atts) 
	{
		global $thisarticle;
		extract(lAtts(array(
			'link' => 0,
			'title' => 0,
		),$atts));
		if ($thisarticle['section']) {
			$sec_title = ($title ? fetch_section_title($thisarticle['section']) : $thisarticle['section']);
			if (!empty($link)) 
				return '<a href="'.pagelinkurl(array('s'=>$thisarticle['section'])).'">'.
					$sec_title.'</a>';
			return $sec_title;
		}
	}

// -------------------------------------------------------------
	function keywords($atts) 
	{
		global $thisarticle;
		return ($thisarticle['keywords']) ? $thisarticle['keywords'] : '';
	}

// -------------------------------------------------------------
	function article_image($atts) 
	{
		global $thisarticle,$img_dir;
		extract(lAtts(array(
			'style' => '',
			'align' => ''
		),$atts));	

		$theimage = ($thisarticle['article_image']) ? $thisarticle['article_image'] : '';
		
		if ($theimage) {
		
			if (is_numeric($theimage)) {
				$rs = safe_row("*",'txp_image',"id='$theimage'");
				if ($rs) {
					extract($rs);
					$out = array(
						'<img',
						'src="'.hu.$img_dir.'/'.$id.$ext.'"',
						'height="'.$h.'" width="'.$w.'" alt="'.$alt.'"',
						(!empty($style)) ? 'style="'.$style.'"' : '',
						(!empty($align)) ? 'align="'.$align.'"' : '',
						' />'
					);			
					return join(' ',$out);
				}
			} else {
				return '<img src="'.$theimage.'" alt="" />';
			}
		}
	}

// -------------------------------------------------------------
	function search_result_title($atts) 
	{
		return permlink($atts, '<txp:title />');
	}

// -------------------------------------------------------------
	function search_result_excerpt($atts) 
	{
		global $thisarticle, $q;
		extract(lAtts(array(
			'hilight'     => 'strong',
		),$atts));
		
		if (empty($thisarticle)) return;
		
		extract($thisarticle);
		
		$result = preg_replace("/>\s*</","> <",$body);
		preg_match_all("/\s.{1,50}".preg_quote($q).".{1,50}\s/iu",$result,$concat);

		$concat = join(" ... ",$concat[0]);

		$concat = strip_tags($concat);
		$concat = preg_replace('/^[^>]+>/U',"",$concat);
		$concat = preg_replace("/(".preg_quote($q).")/i","<$hilight>$1</$hilight>",$concat);
		return ($concat) ? "... ".$concat." ..." : '';
	}

// -------------------------------------------------------------
	function search_result_url($atts) 
	{
		global $thisarticle;
		
		$l = permlinkurl($thisarticle);
		return permlink($atts, $l);
	}

// -------------------------------------------------------------
	function search_result_date($atts) 
	{
		return posted($atts);
	}

// -------------------------------------------------------------
	function search_result_count($atts)
	{
		global $thispage;
		$t = @$thispage['total'];
		extract(lAtts(array(
			'text'     => ($t == 1 ? gTxt('article_found') : gTxt('articles_found')),
		),$atts));
		
		return $t . ($text ? ' ' . $text : '');
	}

// -------------------------------------------------------------
	function image_index($atts)
	{
		global $permlink_mode,$s,$c,$p,$txpcfg,$img_dir,$path_to_site;
		if (is_array($atts)) extract($atts);
		$c = doSlash($c);
		
		$rs = safe_rows_start("*", "txp_image","category='$c' and thumbnail=1 order by name");

		if ($rs) {
			while ($a = nextRow($rs)) {
				extract($a);
				$impath = $img_dir.'/'.$id.'t'.$ext;
				$imginfo = getimagesize($path_to_site.'/'.$impath);
				$dims = (!empty($imginfo[3])) ? ' '.$imginfo[3] : '';
				$url = pagelinkurl(array('c'=>$c, 's'=>$s, 'p'=>$id));
				$out[] = '<a href="'.$url.'">'.
               '<img src="'.hu.$impath.'"'.$dims.' alt="'.$alt.'" />'.'</a>';

			}
			return join('',$out);
		}
	}

// -------------------------------------------------------------
	function image_display($atts) 
	{
		if (is_array($atts)) extract($atts);
		global $s,$c,$p,$img_dir;
		if($p) {
			$rs = safe_row("*", "txp_image", "id='$p' limit 1");
			if ($rs) {
				extract($rs);
				$impath = hu.$img_dir.'/'.$id.$ext;
				return '<img src="'.$impath.
					'" style="height:'.$h.'px;width:'.$w.'px" alt="'.$alt.'" />';
			}
		}
	}

// -------------------------------------------------------------
	function if_comments($atts, $thing)	
	{
		global $thisarticle;
		return parse(EvalElse($thing, $thisarticle['annotate']));
	}

// -------------------------------------------------------------
	function if_comments_allowed($atts, $thing)
	{
		global $thisarticle, $pretext;

		$id = gAtt($atts,'id',gps('id'));
		if ($thisarticle['thisid']) $id = $thisarticle['thisid'];
		if (!$id && @$pretext['id']) $id = $pretext['id'];
		return (checkCommentsAllowed($id)) ? parse($thing) : '';
	}

// -------------------------------------------------------------
	function if_comments_disallowed($atts, $thing)
	{
		global $thisarticle, $pretext;

		$id = gAtt($atts,'id',gps('id'));
		if ($thisarticle['thisid']) $id = $thisarticle['thisid'];
		if (!$id && @$pretext['id']) $id = $pretext['id'];
		return (!checkCommentsAllowed($id)) ? parse($thing) : '';
	}

// -------------------------------------------------------------
	function if_individual_article($atts, $thing)	
	{
		global $is_article_list;
		return parse(EvalElse($thing, ($is_article_list == false)));
	}

// -------------------------------------------------------------
	function if_article_list($atts, $thing)	
	{
		global $is_article_list;
		return parse(EvalElse($thing, ($is_article_list == true)));
	}

// -------------------------------------------------------------
	function meta_keywords() 
	{
		global $id_keywords;
		return ($id_keywords)
		?	'<meta name="keywords" content="'.$id_keywords.'" />'
		:	'';
	}

// -------------------------------------------------------------
	function meta_author() 
	{
		global $id_author;
		return ($id_author)
		?	'<meta name="author" content="'.$id_author.'" />'
		:	'';
	}

// -------------------------------------------------------------
	function doWrap($list, $wraptag, $break, $class='', $breakclass='', $atts='')
	{
		$atts = ($class ? $atts.' class="'.$class.'"' : $atts);
		$breakatts = ($breakclass ? ' class="'.$breakclass.'"' : '');

		// non-enclosing breaks
		if (!preg_match('/^\w+$/', $break) or $break == 'br' or $break == 'hr') {
			if ($break == 'br' or $break == 'hr')
				$break = "<$break $breakatts/>";
			return ($wraptag) 
			?	tag(join($break.n,$list),$wraptag,$atts) 
			:	join($break.n,$list);
		}	

		// enclosing breaks should be specified by name only, no '<' or '>'
		if (($wraptag == 'ul' or $wraptag == 'ol') and empty($break))
			$break = 'li';
			
		return ($wraptag)
		? tag(tag(join("</$break>".n."<{$break}{$breakatts}>",$list),$break,$breakatts),$wraptag,$atts)
		: tag(join("</$break>".n."<{$break}{$breakatts}>",$list),$break,$breakatts);
	}

// -------------------------------------------------------------
	function doTag($content, $tag, $class='', $atts='')
	{
		$atts = ($class ? $atts.' class="'.$class.'"' : $atts);

		if (!$tag)
			return $content;
			
		return ($content)
		? tag($content, $tag, $atts)
		: "<$tag $atts />";
	}
	
// -------------------------------------------------------------
	function permlink($atts,$thing=NULL)
	{
		global $thisarticle;
		if (empty($thisarticle)) return;
		extract(lAtts(array(
			'style' => '',
			'class' => ''
		),$atts));
		
		$url = permlinkurl($thisarticle);

		if ($thing === NULL)
			return $url;
		
		return tag(parse($thing),'a',' href="'.$url.'" title="'.gTxt('permanent_link').'"'. 
							(($style) ? ' style="'.$style.'"' : '').
							(($class) ? ' class="'.$class.'"' : '')
 			 );
	}

// -------------------------------------------------------------
	function permlinkurl_id($ID)
	{
		$article = safe_row(
			"*,ID as thisid, unix_timestamp(Posted) as posted",
			"textpattern",
			"ID=$ID");
		
		return permlinkurl($article);
	}

// -------------------------------------------------------------
	function permlinkurl($article_array) 
	{
		global $permlink_mode, $prefs;

		if (isset($prefs['custom_url_func']) and is_callable($prefs['custom_url_func']))
			return call_user_func($prefs['custom_url_func'], $article_array);

		if (empty($article_array)) return;
		
		extract($article_array);
		
		if (!isset($title)) $title = $Title;
		if (empty($url_title)) $url_title = stripSpace($title);
		if (empty($section)) $section = $Section; // lame, huh?
		if (empty($posted)) $posted = $Posted;
		if (empty($thisid)) $thisid = $ID;
		
		switch($permlink_mode) {
			case 'section_id_title':
				if ($prefs['attach_titles_to_permalinks'])
				{
					return hu."$section/$thisid/$url_title";
				}else{
					return hu."$section/$thisid/";
				}
			case 'year_month_day_title':
				list($y,$m,$d) = explode("-",date("Y-m-d",$posted));
				return hu."$y/$m/$d/$url_title";
			case 'id_title':
				if ($prefs['attach_titles_to_permalinks'])
				{
					return hu."$thisid/$url_title";
				}else{
					return hu."$thisid/";
				}
			case 'section_title':
				return hu."$section/$url_title";
			case 'title_only':
				return hu."$url_title";	
			case 'messy':
				return hu."index.php?id=$thisid";	
		}
	}
	
// -------------------------------------------------------------	
	function lang($atts)
	{
		return LANG;
	}

// -------------------------------------------------------------
	# DEPRECATED - provided only for backwards compatibility
	function formatPermLink($ID,$Section)
	{
		return permlinkurl_id($ID);
	}

// -------------------------------------------------------------
	# DEPRECATED - provided only for backwards compatibility
	function formatCommentsInvite($AnnotateInvite,$Section,$ID)
	{
		global $comments_mode;

		$dc = safe_count('txp_discuss',"parentid='$ID' and visible=1");

		$ccount = ($dc) ?  '['.$dc.']' : '';
		if (!$comments_mode) {
			return '<a href="'.permlinkurl_id($ID).'/#'.gTxt('comment').
				'">'.$AnnotateInvite.'</a>'. $ccount;
		} else {
			return "<a href=\"".hu."?parentid=$ID\" onclick=\"window.open(this.href, 'popupwindow', 'width=500,height=500,scrollbars,resizable,status'); return false;\">".$AnnotateInvite.'</a> '.$ccount;
		}

	}
// -------------------------------------------------------------
	# DEPRECATED - provided only for backwards compatibility
   function doPermlink($text, $plink, $Title, $url_title)
	{
		global $url_mode;
		$Title = ($url_title) ? $url_title : stripSpace($Title);
		$Title = ($url_mode) ? $Title : '';
		return preg_replace("/<(txp:permlink)>(.*)<\/\\1>/sU",
			"<a href=\"".$plink.$Title."\" title=\"".gTxt('permanent_link')."\">$2</a>",$text);
	}

// -------------------------------------------------------------
	# DEPRECATED - provided only for backwards compatibility
	function doArticleHref($ID,$Title,$url_title,$Section)
	{
		$conTitle = ($url_title) ? $url_title : stripSpace($Title);	
		return ($GLOBALS['url_mode'])
		?	tag($Title,'a',' href="'.hu.$Section.'/'.$ID.'/'.$conTitle.'"')
		:	tag($Title,'a',' href="'.hu.'index.php?id='.$ID.'"');
	}

// -------------------------------------------------------------
// Testing breadcrumbs
	function breadcrumb($atts)
	{
		global $pretext,$thisarticle,$sitename;
		
		extract(lAtts(array(
			'wraptag' => 'p',
			'sep' => '&#160;&#187;&#160;',
			'link' => 'y',
			'label' => $sitename
		),$atts));
		$linked = ($link == 'y')? true: false; 		
		if ($linked) $label = '<a href="'.hu.'" class="noline">'.$sitename.'</a>';
		
		$content = array();
		
		extract($pretext);
		if(!empty($s) && $s!= 'default')
		{ 
			$content[] = ($linked)? (
					tag(htmlspecialchars($s),'a',' href="'.pagelinkurl(array('s'=>$s)).'/"')
				):$s;
		}
		
		$category = empty($c)? '': $c;
		$cattree = array();

		while($category and $category != 'root' and $parent = safe_field('parent','txp_category',"name='$category'")) {
			//Use new /category/category_name scheme here too?
				$cattree[] = ($linked)? 
					tag(str_replace("& ","&#38; ", $category),'a',' href="'.pagelinkurl(array('c'=>$category)).'"')
						:$category;
				$category = $parent;
				unset($parent);
		}		

		if (!empty($cattree))
		{
			$cattree = array_reverse($cattree);
			$content = array_merge($content, $cattree);
		}
		//Add date month permlinks?
//		$year = ''; 
//		$month = '';
//		$date = '';
		//Add the label at the end, to prevent breadcrumb for home page
		if (!empty($content)) $content = array_merge(array($label),$content);
		//Add article title without link if we're on an individual archive page?
		return doTag(join($sep, $content), $wraptag);
	}


//------------------------------------------------------------------------

	function if_excerpt($atts, $thing)
	{
	        global $thisarticle;
	        # eval condition here. example for article excerpt
	        $excerpt = trim($thisarticle['excerpt']);
	        $condition = (!empty($excerpt))? true : false;
	        return parse(EvalElse($thing, $condition));
	}

//--------------------------------------------------------------------------
// Searches use default page. This tag allows you to use different templates if searching
//--------------------------------------------------------------------------

	function if_search($atts, $thing)
	{
		$searching = gps('q');
		$condition = (!empty($searching))? true : false;
		return parse(EvalElse($thing, $condition));
	}

//--------------------------------------------------------------------------
	function if_category($atts, $thing)
	{
		global $c;

		extract(lAtts(array(
			'name' => '',
		),$atts));

		if (trim($name)) {
			return parse(EvalElse($thing, ($c == $name)));
		}

		return parse(EvalElse($thing, !empty($c)));
	}

//--------------------------------------------------------------------------
	function if_article_category($atts, $thing)
	{
		global $thisarticle;

		extract(lAtts(array(
			'name' => '',
			'number' => '',
		),$atts));

		if ($number)
			$cats = array($thisarticle['category' . $number]);
		else
			$cats = array_unique(array($thisarticle['category1'], $thisarticle['category2']));

		sort($cats);
		if ($name)
			return parse(EvalElse($thing, (in_array($name, $cats))));

		return parse(EvalElse($thing, (array_shift($cats) != '')));
	}

//--------------------------------------------------------------------------
	function if_section($atts, $thing)
	{
		global $pretext;
		extract($pretext);

		extract(lAtts(array(
			'name' => '',
		),$atts));

		$section = ($s == 'default' ? '' : $s);

		return parse(EvalElse($thing, ($section == $name)));

	}

//--------------------------------------------------------------------------
	function if_article_section($atts, $thing)
	{
		global $thisarticle;

		extract(lAtts(array(
			'name' => '',
		),$atts));

		$section = $thisarticle['section'];

		return parse(EvalElse($thing, ($section == $name)));
	}

//--------------------------------------------------------------------------
	function php($atts, $thing)
	{
		global $is_article_body, $thisarticle, $prefs;

		ob_start();
		if (empty($is_article_body)) {
			if (!empty($prefs['allow_page_php_scripting']))
				eval($thing);
		}
		else {
			if (!empty($prefs['allow_article_php_scripting'])
				and has_privs('article.php', $thisarticle['authorid']))
				eval($thing);
		}
		return ob_get_clean();
	}
	
//--------------------------------------------------------------------------
	function custom_field($atts)
	{
		global $thisarticle, $prefs;
		
		extract(lAtts(array(
			'name' => @$prefs['custom_1_set'],
		),$atts));

		if (isset($thisarticle[$name]))
			return $thisarticle[$name];
	}	
	
//--------------------------------------------------------------------------
	function if_custom_field($atts, $thing)
	{
		global $thisarticle, $prefs;
		
		extract(lAtts(array(
			'name' => @$prefs['custom_1_set'],
			'val' => NULL,
		),$atts));

		if ($val !== NULL)
			$cond = (@$thisarticle[$name] == $val);
		else
			$cond = !empty($thisarticle[$name]);

		return parse(EvalElse($thing, $cond));
	}	

// -------------------------------------------------------------
	function site_url($atts) 
	{
		return hu;
	}

//--------------------------------------------------------------------------
//File tags functions. 
//--------------------------------------------------------------------------

	function file_download_list($atts)
	{
		global $thisfile;
		
		extract(lAtts(array(
			'form'     => 'files',
			'sort'     => 'filename',
			'label'    => '',
			'break'    => br,
			'limit'    => '10',
			'wraptag'  => '',
			'category' => '',
			'class'    => __FUNCTION__
		),$atts));	
		
		$qparts = array(
			($category) ? "category='$category'" : '1',
			"order by",
			$sort,
			($limit) ? "limit $limit" : ''
		);
		
		$rs = safe_rows_start("*","txp_file",join(' ',$qparts));
	
		if ($rs) {
			if ($label) $outlist[] = $label;
		
			while ($a = nextRow($rs)) {				
				$thisfile = fileDownloadFetchInfo("id='$a[id]'");
				$outlist[] = file_download(
					array('id'=>$a['id'],'filename'=>$a['filename'],'form'=>$form)
				);
			}
			
			if (!empty($outlist)) {
				if ($wraptag == 'ul' or $wraptag == 'ol') {
					return doWrap($outlist, $wraptag, $break, $class);
				}	
				
				return ($wraptag) ? tag(join($break,$outlist),$wraptag) : join(n,$outlist);
			}
		}				
		return '';
	}

//--------------------------------------------------------------------------
	function file_download($atts)
	{
		global $thisfile;
		
		extract(lAtts(array(
			'form'=>'files',
			'id'=>'',
			'filename'=>'',
		),$atts));
		
		$where = (!empty($id) && $id != 0)? "id='$id'" : ((!empty($filename))? "filename='$filename'" : '');
		
		if (empty($thisfile)) {
			$thisfile = fileDownloadFetchInfo($where);
		}				
		
		$thing = fetch_form($form);

		return parse($thing);		
	}
	
//--------------------------------------------------------------------------
	function file_download_link($atts,$thing)
	{
		global $permlink_mode, $thisfile;
		extract(lAtts(array(
			'id'=>'',
			'filename'=>'',
		),$atts));
		
		$where = (!empty($id) && $id != 0)? "id='$id'" : ((!empty($filename))? "filename='$filename'" : '');
		
		$thisfile = fileDownloadFetchInfo($where);
		
		$out = ($permlink_mode == 'messy') ?
					'<a href="'.hu.'index.php?s=file_download&id='.$thisfile['id'].'">'.parse($thing).'</a>':
					'<a href="'.hu.gTxt('file_download').'/'.$thisfile['id'].'">'.parse($thing).'</a>';								
		return $out;
	}	
//--------------------------------------------------------------------------
	function fileDownloadFetchInfo($where)
	{
		global $file_base_path;		

		$result = array(
				'id' => 0,
				'filename' => '',
				'category' => '',
				'description' => '',
				'downloads' => 0,
				'size' => 0,
				'created' => 0,
				'modified' => 0
			);

		$rs = safe_row('*','txp_file',$where);

		if ($rs) {
			extract($rs);

			$result['id'] = $id;
			$result['filename'] = $filename;
			$result['category'] = $category;
			$result['description'] = $description;
			$result['downloads'] = $downloads;

			// get filesystem info
			$filepath = build_file_path($file_base_path , $filename);

			if (file_exists($filepath)) {
				$filesize = filesize($filepath);
				if ($filesize !== false)
					$result['size'] = $filesize;

				$created = filectime($filepath);
				if ($created !== false)
					$result['created'] = $created;

				$modified = filemtime($filepath);
				if ($modified !== false)
					$result['modified'] = $modified;
			}
		}

		return $result;
	}	
//--------------------------------------------------------------------------
	function file_download_size($atts)
	{
		global $thisfile;		
		
		extract(lAtts(array(
			'decimals' => 2,
			'format' => ''			
		), $atts));
		
		if (empty($decimals) || $decimals < 0) $decimals = 2;
		if (is_numeric($decimals)) {
			$decimals = intval($decimals);			
		} else {
			$decimals = 2;
		}
		$t = $thisfile['size'];
		if (!empty($thisfile['size']) && !empty($format)) {
			switch(strtoupper(trim($format))) {
				default:
					$divs = 0;
					while ($t > 1024) {
						$t /= 1024;
						$divs++;
					}
					if ($divs==0) $format = ' b';
					elseif ($divs==1) $format = 'kb';
					elseif ($divs==2) $format = 'mb';
					elseif ($divs==3) $format = 'gb';
					elseif ($divs==4) $format = 'pb';
					break;
				case 'B':
					// do nothing
					break;
				case 'KB':
					$t /= 1024;
					break;
				case 'MB':
					$t /= (1024*1024);
					break;
				case 'GB':
					$t /= (1024*1024*1024);
					break;
				case 'PB':
					$t /= (1024*1024*1024);
				break;
			}
			return number_format($t,$decimals) . $format;
		}
		
		return (!empty($thisfile['size']))? $thisfile['size'] : '';
	}

//--------------------------------------------------------------------------
	function file_download_created($atts)
	{
		global $thisfile;		
		extract(lAtts(array('format'=>''),$atts));		
		return fileDownloadFormatTime(array('ftime'=>$thisfile['created'], 'format' => $format));
	}
//--------------------------------------------------------------------------
	function file_download_modified($atts)
	{
		global $thisfile;		
		extract(lAtts(array('format'=>''),$atts));		
		return fileDownloadFormatTime(array('ftime'=>$thisfile['modified'], 'format' => $format));
	}
//-------------------------------------------------------------------------
	//All the time related file_download tags in one
	//One Rule to rule them all ... now using safe formats
	function fileDownloadFormatTime($params)
	{
		global $prefs;
		extract($params);
		if (!empty($ftime)) {
			return  (!empty($format))? safe_strftime($format,$ftime) : safe_strftime($prefs['archive_dateformat'],$ftime);
		}
		return '';
	}

	function file_download_id($atts)
	{
		global $thisfile;		
		return $thisfile['id'];
	}
	function file_download_name($atts)
	{
		global $thisfile;		
		return $thisfile['filename'];
	} 
	function file_download_category($atts)
	{
		global $thisfile;		
		return $thisfile['category'];
	}
	function file_download_downloads($atts)
	{
		global $thisfile;		
		return $thisfile['downloads'];
	}
	function file_download_description($atts)
	{
		global $thisfile;		
		return $thisfile['description'];
	}	
	
?>
