<?php

/*
	This is Textpattern
	Copyright 2004 by Dean Allen - all rights reserved.

	Use of this software denotes acceptance of the Textpattern license agreement 

*/


// -------------------------------------------------------------
	function page_title($atts) 
	{
		global $sitename,$id,$c,$q,$parentid;
		if (is_array($atts)) extract($atts);
		$sep = (!empty($separator)) ? $separator : ': ';
		$s = $sitename;
		if ($id)       return $s.$sep.fetch('Title','textpattern','ID',$id);
		if ($c)        return $s.$sep.$c;
		if ($q)        return $s.$sep.gTxt('search_results').$sep.' '.$q;
		if ($parentid) return $s.$sep.'comments on '.
			fetch('Title','textpattern','ID',$parentid);
		return $sitename;
	}

// -------------------------------------------------------------
	function css($atts) 	// generates the css src in <head>
	{
		global $pfr,$s;
		if (is_array($atts)) extract($atts);
		if (!empty($n)) return $pfr.'textpattern/css.php?n='.$n;
		return $pfr.'textpattern/css.php?s='.$s;
	}

// -------------------------------------------------------------
	function image($atts) 
	{
		global $pfr,$img_dir;
		if (is_array($atts)) extract($atts);
		
		$rs = safe_row("*", "txp_image", "id='$id' limit 1");
		if ($rs) {
			extract($rs);
			$out = array(
				'<img',
				'src="'.$pfr.$img_dir.'/'.$id.$ext.'"',
				'height="'.$h.'" width="'.$w.'" alt="'.$alt.'"',
				(!empty($style)) ? 'style="'.$style.'"' : '',
				(!empty($align)) ? 'align="'.$align.'"' : '',
				'/>'
			);
			
			return join(' ',$out);
		}
		return '<txp:notice message="malformed image tag" />';
	}

// -------------------------------------------------------------
	function output_form($atts) 
	{
		if (is_array($atts)) extract($atts);
		if (empty($form)) return false;
		return parse(fetch('form','txp_form','name',$form));
	}

// -------------------------------------------------------------
	function feed_link($atts) // simple link to rss or atom feed
	{
		if (is_array($atts)) extract($atts);
		$wraptag = (empty($wraptag)) ? '' : $wraptag;
		$label = (empty($label)) ? 'XML' : $label;
		$flavor = (empty($flavor)) ? 'rss' : $flavor;
		$category = (empty($category)) ? '' : a.'category='.urlencode($category);
		$section = (empty($section)) ? '' : a.'section='.urlencode($section);
		global $pfr;
		$out = '<a href="'.$pfr.'?'.$flavor.'=1'.
			$category.$section.'" title="XML feed">'.$label.'</a>';
		return ($wraptag) ? tag($out,$wraptag) : $out;
	}

// -------------------------------------------------------------
	function link_feed_link($atts) // rss or atom feed of links
	{
		if (is_array($atts)) extract($atts);
		$label = (empty($label)) ? 'XML' : $label;
		$flavor = (empty($flavor)) ? 'rss' : $flavor;
		$category = (empty($category)) ? '' : a.'category='.urlencode($category);
		global $pfr;		
		return '<a href="'.$pfr.'?'.$flavor.'=1'.a.'area=link'.$category.
			'" title="XML feed">'.$label.'</a>';
	}


// -------------------------------------------------------------
	function linklist($atts) // possible atts: form, sort, category, limit, label
	{
		if(is_array($atts)) extract($atts);
		if(!isset($form)) $form = 'plainlinks';
		if(!isset($sort)) $sort = 'linksort';
		$Form = fetch('Form','txp_form','name',$form);
		$wraptag = (empty($wraptag)) ? '' : $wraptag;
		
		$qparts = array(
			(!empty($category)) ? "category='$category'" : '1',
			"order by",
			$sort,
			(!empty($limit)) ? "limit $limit" : ''
		);
		
		$rs = safe_rows("*","txp_link",join(' ',$qparts));
	
		if ($rs) {
			$outlist = (!empty($label)) ? $label : '';
		
			foreach ($rs as $a) {
				extract($a);
				$linkname = str_replace("& ","&#38; ", $linkname);

				$url=str_replace("http://","",$url);
				$url=preg_replace("/^([^\/].+)$/","http://$1",trim($url));
				$link = '<a href="'.$url.'">'.$linkname.'</a>';
				$linkdesctitle = '<a href="'.$url.
				    '" title="'.$description.'">'.$linkname.'</a>';

				$out = str_replace("<txp:link />", $link, $Form);
				$out = str_replace("<txp:linkdesctitle />", $linkdesctitle, $out);
				$out = str_replace("<txp:link_description />", $description, $out);
			
				$outlist .= $out;
			}
		return ($wraptag) ? tag($outlist,$wraptag) : $outlist;
		}
		return false;
	}

// -------------------------------------------------------------
	function stripSpace($text) 
	{
		global $txpac;
		if ($txpac['attach_titles_to_permalinks']) {
		
			$text = preg_replace("/(^| &\S+;)|(<[^>]*>)/U","",$text);		

			if ($txpac['permalink_title_format']) {
				return 
				strtolower(
					preg_replace("/[^[:alnum:]\-]/","",
						str_replace(" ","-",
							$text
						)
					)
				);			
			} else {
				return preg_replace("/[^[:alnum:]]/","",$text);
			}
		}
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
		if (is_array($atts)) extract($atts);
		if(!empty($email)) {
			$out  = array(
				'<a href="'.eE('mailto:'.$email).'"',
				(!empty($title)) ? ' title="'.$title.'"' : '',
				'>',
				(empty($linktext)) ? gTxt('contact') : $linktext,
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
		if (is_array($atts)) extract($atts);
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
		if (is_array($atts)) extract($atts);
		global $pretext;
		extract($pretext);

		$label    = (empty($label))    ? '' : $label;
		$break    = (empty($break))    ? br : $break;
		$wraptag  = (empty($wraptag))  ? '' : $wraptag;

		$limit    = (empty($limit))    ? 10 : $limit;
		$category = (empty($category)) ? '' : doSlash($category);
		$sortby   = (empty($sortby))   ? '' : doSlash($sortby);
		$sortdir  = (empty($sortdir))  ? '' : doSlash($sortdir);

		$catq = ($category) ? "and (Category1='$category' or Category2='$category')" :'';
		$sortq = ($sortby) ? "$sortby" : 'Posted';
		$dirq = ($sortdir) ? "$sortdir" : 'desc';

		$rs = safe_rows(
			"*", 
			"textpattern", 
			"Status = 4 and Posted <= now() $catq order by $sortq $dirq limit 0,$limit"
		);
		
		if ($rs) {
			if ($label) $out[] = $label;
			foreach ($rs as $a) {
				extract($a);
				$conTitle = stripSpace($Title);
				$out[] = ($url_mode)
				?	tag($Title,'a',' href="'.$pfr.$Section.'/'.$ID.'/'.$conTitle.'"')
				:	tag($Title,'a',' href="'.$pfr.'index.php?id='.$ID.'"');
			}
			if (is_array($out)) {
				if($break == 'li') {
					return ($wraptag) 
					?	tag("<li>".join("</li>\n<li>",$out)."</li>",$wraptag) 
					: 	"<li>".join("</li>\n<li>",$out)."</li>";
				}
				return ($wraptag) 
				?	tag(join($break.n,$out),$wraptag) 
				: 	join($break.n,$out);
			}
		}
		return '';
	}



// -------------------------------------------------------------
	function recent_comments($atts)
	{
		if (is_array($atts)) extract($atts);
		global $pretext;
		extract($pretext);

		$label = (empty($label)) ? '' : $label;
		$limit = (empty($limit)) ? "10" : $limit;
		$break = (empty($break)) ? br : $break;

		$q = "select ".PFX."txp_discuss.*,".PFX."textpattern.* from ".PFX."txp_discuss
            left join ".PFX."textpattern on ".PFX."textpattern.ID = ".PFX."txp_discuss.parentid
			order by ".PFX."txp_discuss.posted desc limit 0,$limit";

		$rs = getRows($q);

		if ($rs) {
			if ($label) $out[] = $label;
        	foreach($rs as $a) {
				extract($a);
				$out[] = ($url_mode)
				?	'<a href="'.$pfr.$Section.'/'.$ID.'/#c'.$discussid.'">'
						.$name.' ('.$Title.')</a>'
				:	'<a href="'.$pfr.'index.php?id='.$ID.'#c'.$discussid.'">'
						.$name.' ('.$Title.')</a>';
			}
			return (is_array($out)) ? join($break.n,$out) : '';
		}
		return '';
	}

// -------------------------------------------------------------
	function related_articles($atts)
	{
		if (is_array($atts)) extract($atts);
		global $pretext,$thisid;
		extract($pretext);

		$label   = (empty($label))   ? "" : $label;
		$limit   = (empty($limit))   ? 10 : $limit;
		$break   = (empty($break))   ? br : $break;
		$wraptag = (empty($wraptag)) ? "" : $wraptag;
		
		if($thisid) $id = $thisid;

		$cats = safe_row("Category1,Category2","textpattern", "ID='$id' limit 1");  

		$q = array("select * from ".PFX."textpattern where Status = 4 and ID!='$id'",
			(!empty($cats[0])) ? "and ((Category1='$cats[0]') or (Category2='$cats[0]'))" :'',
			(!empty($cats[1])) ? "or ((Category1='$cats[1]') or (Category2='$cats[1]'))" :'',
			"and Status=4 and Posted <= now() order by Posted desc limit 0,$limit");
		$rs = getRows(join(' ',$q));

		if ($rs) {
			if ($label) $out[] = $label;
	       	foreach($rs as $a) {
				extract($a);
				$conTitle = stripSpace($Title);
				$out[] = ($url_mode)
				?	tag($Title,'a',' href="'.$pfr.$Section.'/'.$ID.'/'.$conTitle.'"')
				:	tag($Title,'a',' href="'.$pfr.'index.php?id='.$ID.'"');
			}
			if (is_array($out)) {
				if($break == 'li') {
					return ($wraptag) 
					?	tag("<li>".join("</li>\n<li>",$out)."</li>",$wraptag) 
					: 	"<li>".join("</li>\n<li>",$out)."</li>";
				}
				return ($wraptag) 
				?	tag(join($break.n,$out),$wraptag) 
				:	join($break.n,$out);
			}
		}
		return '';
		unset($GLOBALS['thisid']);
	}

// -------------------------------------------------------------
	function popup($atts) // popup navigation. possible atts: type (c or s), label
	{
		global $pretext,$pfr;
		if (is_array($atts)) extract($atts);
		
		$gc = $pretext['c'];
		$gs = $pretext['s'];
		
		$wraptag = (empty($wraptag)) ? "" : $wraptag;
		$label = (empty($label)) ? "" : $label;
		
		$thetable = ($type=='s') ? 'section' : 'category';
		$out ='<select name="'.$type.'" onchange="submit(this.form)">'.n.
		t.'<option value=""></option>'.n;
		$q[] = "select name from ".PFX."txp_".$thetable." where name != 'default'";
		$q[] = ($thetable=='category') ? "and type='article'" : '';
		$q[] = "order by name";

		$rs = getRows(join(' ',$q));
		if ($rs) {
			foreach ($rs as $a) {
				extract($a);
				if ($name=='root') continue;
				$sel = ($gc==$name or $gs==$name) ? 'selected="selected"' : '';
				$out .= t.t.'<option value="'.urlencode($name).'"'.$sel.'>'.
				htmlspecialchars($name).'</option>'.n;
				unset($selected);
			}
			$out.= '</select>';
			$out = ($label) ? $label.br.$out : $out;
			$out = ($wraptag) ? tag($out,$wraptag) : $out;
			$out.= '<noscript><input type="submit" value="go" /></noscript>';
			return '<form action="'.$pfr.'" method="get">'.n.$out.'</form>';
		}
	}

// -------------------------------------------------------------
	function category_list($atts) // output href list of site categories
	{
		global $pfr;
		if (is_array($atts)) extract($atts);
		$wraptag = (empty($wraptag)) ? "" : $wraptag;
		$label = (empty($label)) ? "" : $label;
		$break = (empty($break)) ? br : $break;

		$rs = safe_column(
			"name", 
			"txp_category",
			"name != 'default' and type='article' order by name"
		);

		if ($rs) {
			if ($label) $out[] = $label;
			foreach ($rs as $a) {
				if ($a=='root') continue;
				if($a) $out[] = tag(str_replace("& ","&#38; ", $a),'a',' href="'.$pfr.'?c='.urlencode($a).'"');
			}
			if (is_array($out)) {
				if($break == 'li') {
					return ($wraptag) 
					?	tag("<li>".join("</li>\n<li>",$out)."</li>",$wraptag) 
					: 	"<li>".join("</li>\n<li>",$out)."</li>";
				}
				return ($wraptag) 
				?	tag(join($break.n,$out),$wraptag) 
				:	join($break.n,$out);
			}
		}
		return '';
	}

// -------------------------------------------------------------
	function section_list($atts) // output href list of site categories
	{
		global $pfr,$url_mode;
		if (is_array($atts)) extract($atts);
		$wraptag = (empty($wraptag)) ? "" : $wraptag;
		$label = (empty($label)) ? "" : $label;
		$break = (empty($break)) ? br : $break;

		$rs = safe_column("name","txp_section","name != 'default' order by name");
		if ($rs) {
			if ($label) $out[] = $label;
			foreach ($rs as $a) {
				if($a) {
					if($url_mode) {
						$out[] = tag(htmlspecialchars($a),'a',' href="'.$pfr.urlencode($a).'/"');
					} else {
						$out[] = tag(htmlspecialchars($a),'a',' href="'.$pfr.'?s='.urlencode($a).'"');
					}
				}
			}
			if (is_array($out)) {
				if($break == 'li') {
					return ($wraptag) 
					?	tag("<li>".join("</li>\n<li>",$out)."</li>",$wraptag) 
					: 	"<li>".join("</li>\n<li>",$out)."</li>";
				}
				return ($wraptag) 
				?	tag(join($break.n,$out),$wraptag) 
				:	join($break.n,$out);
			}
		}
		return '';
	}


// -------------------------------------------------------------
	function search_input($atts) // input form for search queries
	{
		global $pfr;
		$q = gps('q');
		if (is_array($atts)) extract($atts);
		$wraptag = (empty($wraptag)) ? "" : $wraptag;
		$form = (empty($form)) ? '' : $form;

		if ($form) {
			$rs = fetch('form','txp_form','name',$form); 
			return ($rs) ? $rs : 'search form not found';
		}
		
		$size = (!empty($size)) ? $size : '15'; 
		$sub = (!empty($button)) ? '<input type="submit" value="'.$button.'" />' : '';

		$out = fInput('text','q',$q,'','','',$size);
		$out = (!empty($label)) ? $label.br.$out.$sub : $out.$sub;
		$out = ($wraptag) ? tag($out,$wraptag) : $out;
		
		return '<form action="'.$pfr.'index.php" method="get">'.$out.'</form>';
	}

// -------------------------------------------------------------
	function link_to_next($atts, $thing) // link to next article, if it exists
	{
		global $s,$pfr,$next_id,$next_title;
		$thing = (isset($thing)) ? parse($thing) : '';
		if($next_id) {		
			return formatHref($pfr,$s,$next_id,$thing,$next_title,'noline');
		}
		return '';
	}
		
// -------------------------------------------------------------
	function link_to_prev($atts, $thing) // link to next article, if it exists
	{
		global $s,$pfr,$prev_id,$prev_title,$url_mode;
		$thing = (isset($thing)) ? parse($thing) : '';
		if ($prev_id) {
			return formatHref($pfr,$s,$prev_id,$thing,$prev_title,'noline');
		}
		return '';
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
		global $pfr;
		if (!empty($thing)) {
			return '<a href="'.$pfr.'" class="noline">'.parse($thing).'</a>';
		}
	}

// -------------------------------------------------------------
	function newer($atts, $thing) 
	{	
		global $thispage,$url_mode;
		if (is_array($atts)) extract($atts);
		if (is_array($thispage)) { 
			extract($thispage);
		} else { 
			return '<txp:newer>'.$thing.'</txp:newer>'; 
		}

		ob_start();

		if ($pg > 1) {
			$out = array(
				'<a href="?pg='.($pg - 1),
				($c) ? a.'c='.urlencode($c) : '',
				($s && !$url_mode) ? a.'s='.urlencode($s) : '',
				'"',
				(empty($title)) ? '' : ' title="'.$title.'"',
				'>',
				$thing,
				'</a>');
			return join('',$out);
		} else return;
		
	}

// -------------------------------------------------------------
	function older($atts, $thing) 
	{
		global $thispage,$url_mode;
		if (is_array($atts)) extract($atts);
		if (is_array($thispage)) {
			extract($thispage); 
		} else { 
			return '<txp:older>'.$thing.'</txp:older>'; 
		}
		
		ob_start();

		if ($pg != $numPages) {
			$out = array(
				'<a href="?pg='.($pg + 1),
				($c) ? a.'c='.urlencode($c) : '',
				($s && !$url_mode) ? a.'s='.urlencode($s) : '',
				'"',
				(empty($title)) ? '' : ' title="'.$title.'"',
				'>',
				$thing,
				'</a>');
			return join('',$out);
		} else return;
	}

// -------------------------------------------------------------
	function mentions($atts) 
	{
		global $thisarticle;
		$out = $thisarticle['mentions_link'];
		if (is_array($atts)) extract($atts);
		if(!empty($wraptag)) return tag($out,$wraptag);
		return $out;	
	}	
	

// -------------------------------------------------------------
	function text($atts) 
	{
		if (is_array($atts)) extract($atts);
		if (!empty($item)) return gTxt($item);
	}

// -------------------------------------------------------------
	function posted($atts) 
	{
		global $dateformat,$archive_dateformat,$timeoffset,
				$count,$c,$thisarticle,$id,$txpcfg;

		$date_offset = $thisarticle['posted'] + $timeoffset;

		if (is_array($atts)) extract($atts);

		if(!empty($format)) {

			if($format=='since') {
			
				$date_out = since($thisarticle['posted']);
			
			} else {
			
				$date_out = date($format,$date_offset);
			
			}

		} else {

			if ($count > 0 or $id or $c) { 
			
				$dateformat = $archive_dateformat; 
			}
			
			if($dateformat == "since") { 
				
				$date_out = since($thisarticle['posted']); 
				
			} else { 
				
				$date_out = date($dateformat,$date_offset); 
			}
		}

		if (!empty($lang)) {

			if (empty($GLOBALS['date_lang'])) {
			
				$date_lang = load_lang($lang.'_dates');	
			
			} else global $date_lang;
		
			if ($date_lang) {
			
				foreach ($date_lang as $k => $v) {
			
					$date_out = str_replace($k,$v,$date_out);
				}
			}
		}

		if(!empty($wraptag)) $date_out = tag($date_out,$wraptag);

		return $date_out;
	}

// -------------------------------------------------------------
	function comments_invite($atts) 
	{
		global $thisarticle;
		$out = $thisarticle['comments_invite'];
		if (is_array($atts)) extract($atts);
		if(!empty($wraptag)) return tag($out,$wraptag);
		return $out;	
	}

// -------------------------------------------------------------
	function author($atts) 
	{
		global $thisarticle;
		if (is_array($atts)) extract($atts);
		return $thisarticle['author'];	
	}

// -------------------------------------------------------------
	function permlink($atts) 
	{
		global $thisarticle;
		if (is_array($atts)) extract($atts);
		if(!empty($wraptag)) return tag($thisarticle['permlink'],$wraptag);
		return $thisarticle['permlink'];
	}
	
// -------------------------------------------------------------
	function body($atts) 
	{
		global $thisarticle;
		if (is_array($atts)) extract($atts);
		return $thisarticle['body'];
	}	
	
// -------------------------------------------------------------
	function title($atts) 
	{
		global $thisarticle;
		if (is_array($atts)) extract($atts);
		return $thisarticle['title'];	
	}

// -------------------------------------------------------------
	function excerpt($atts) 
	{
		global $thisarticle;
		if (is_array($atts)) extract($atts);
		return $thisarticle['excerpt'];	
	}

// -------------------------------------------------------------
	function category1($atts) 
	{
		global $thisarticle, $pfr;
		if (is_array($atts)) extract($atts);
		if ($thisarticle['category1']) {
			if (!empty($link)) 
				return '<a href="'.$pfr.'?c='.$thisarticle['category1'].'">'.
					$thisarticle['category1'].'</a>';
			return $thisarticle['category1'];
		}
	}
	
// -------------------------------------------------------------
	function category2($atts) 
	{
		global $thisarticle, $pfr;
		if (is_array($atts)) extract($atts);
		if ($thisarticle['category2']) {
			if (!empty($link)) 
				return '<a href="'.$pfr.'?c='.$thisarticle['category2'].'">'.
					$thisarticle['category2'].'</a>';
			return $thisarticle['category2'];
		}
	}

// -------------------------------------------------------------
	function section($atts) 
	{
		global $thisarticle, $pfr;
		if (is_array($atts)) extract($atts);
		if ($thisarticle['section']) {
			if (!empty($link)) 
				return '<a href="'.$pfr.$thisarticle['section'].'/">'.
					$thisarticle['section'].'</a>';
			return $thisarticle['section'];
		}
	}

// -------------------------------------------------------------
	function search_result_title($atts) 
	{
		global $this_result;
		if (is_array($atts)) extract($atts);
		return $this_result['search_result_title'];
	}

// -------------------------------------------------------------
	function search_result_excerpt($atts) 
	{
		global $this_result;
		if (is_array($atts)) extract($atts);
		return $this_result['search_result_excerpt'];
	}

// -------------------------------------------------------------
	function search_result_url($atts) 
	{
		global $this_result;
		if (is_array($atts)) extract($atts);
		return $this_result['search_result_url'];
	}

// -------------------------------------------------------------
	function search_result_date($atts) 
	{
		global $this_result;
		if (is_array($atts)) extract($atts);
		return $this_result['search_result_date'];
	}

// -------------------------------------------------------------
	function image_index($atts) 
	{
		global $url_mode,$s,$c,$p,$pfr,$txpcfg,$img_dir;
		if (is_array($atts)) extract($atts);
		$c = doSlash($c);
		
		$rs = safe_rows("*", "txp_image","category='$c' and thumbnail=1 order by name");
		
		if ($rs) {
			foreach($rs as $a) {
				extract($a);
				$impath = $pfr.$img_dir.'/'.$id.'t'.$ext;
				$imginfo = getimagesize($txpcfg['doc_root'].$impath);
				$dims = (!empty($imginfo[3])) ? ' '.$imginfo[3] : '';
				$out[] = '<a href="'.$pfr.$s.'/?c='.urlencode($c).a.'p='.$id.'">'.
					'<img src="'.$impath.'"'.$dims.' alt="'.$alt.'" />'.
					'</a>';
			}
			return join('',$out);
		}
	}

// -------------------------------------------------------------
	function image_display($atts) 
	{
		if (is_array($atts)) extract($atts);
		global $url_mode,$s,$c,$p,$pfr,$img_dir;
		if($p) {
			$rs = safe_row("*", "txp_image", "id='$p' limit 1");
			if ($rs) {
				extract($rs);
				$impath = $pfr.$img_dir.'/'.$id.$ext;
				return '<img src="'.$impath.
					'" style="height:'.$h.'px;width:'.$w.'px" alt="'.$alt.'" />';
			}
		}
	}

// -------------------------------------------------------------
	function if_comments($atts, $thing)	
	{
		global $thisarticle;
		return ($thisarticle['if_comments']) ? parse($thing) : '';
	}

// -------------------------------------------------------------
	function if_individual_article($atts, $thing)	
	{
		global $is_article_list;
		return ($is_article_list == false) ? parse($thing) : '';
	}

// -------------------------------------------------------------
	function if_article_list($atts, $thing)	
	{
		global $is_article_list;
		return ($is_article_list == true) ? parse($thing) : '';
	}
?>
