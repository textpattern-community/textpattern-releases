<?php
	
	define("t","\t");
	define("n","\n");
	define("br","<br />");
	define("sp","&#160;");
	define("a","&#38;");

// -------------------------------------------------------------
	function end_page()
	{
		global $txp_user,$event;
		if($event!='tag') {
			echo '<div style="text-align:center;margin:4em">
			<a href="http://www.textpattern.com"><img src="txp_img/carver.gif" width="60" height="48" border="0" alt="" /></a>';
			echo graf('Textpattern &#183; '.txp_version);
			echo($txp_user)
			?	graf(gTxt('logged_in_as').' '.$txp_user.br.
					'<a href="index.php?logout=1">'.gTxt('logout').'</a>').'</div>'
			:	'</div>';
			echo n.'</body>'.n.'</html>';
		}
	}

// -------------------------------------------------------------
	function column_head($value, $dbcolumn, $current_event, $islink, $sort)
	{
		$o = '<td class="small"><strong>';
			if (!empty($islink)) {
				$o.= "<a href=\"index.php";
				$o.= ($dbcolumn!='') ? "?order=$dbcolumn":'';
				$o.= ($sort!='') ? a."sort=$sort":'';
				$o.= ($current_event!='') ? a."event=$current_event":'';
				$o.= '&#38;step=list">';
			}
		$o .= $value;
			if (!empty($islink)) { $o .= "</a>"; }
		$o .= '</strong></td>';
		return $o;
	}
	
// -------------------------------------------------------------
	function hCell($text="",$caption="")
	{
		$text=($text=='')?'&#160;':$text;
		$o = '<th>'.$text.'</th>';
		return $o;
	}

// -------------------------------------------------------------
	function selectLink($event,$step,$thing,$value,$carrything,$carryvalue,$linktext)
	{
		return '<a href="?event='.$event.
			a.'step='.$step.a.$thing.'='.$value.
			a.$carrything.'='.$carryvalue.'">'.$linktext.'</a>';
	}
	
// -------------------------------------------------------------
	function sLink($event,$step,$linktext)
	{
		return '<a href="?event='.$event.a.'step='.$step.'">'.$linktext.'</a>';
	}

// -------------------------------------------------------------
	function eLink($event,$step='',$thing='',$value='',$linktext,$thing2='',$val2='')
	{
		return join('',array(
			'<a href="index.php?event='.$event,
			($step) ? a.'step='.$step : '',
			($thing) ? a.''.$thing.'='.urlencode($value) : '',
			($thing2) ? a.''.$thing2.'='.urlencode($val2) : '',
			'">'.$linktext.'</a>'
		));
	}

// -------------------------------------------------------------
	function wLink($event,$step='',$thing='',$value='')
	{
		return join('',array(
			'<a href="index.php?event='.$event,
			($step) ? a.'step='.$step : '',
			($thing) ? a.''.$thing.'='.urlencode($value) : '',
			'" class="dlink">'.sp.'!'.sp.'</a>'
		));
	}

// -------------------------------------------------------------
	function dLink($event,$step,$thing,$value,$verify='',$thing2='',$thing2val='',$get='')
	{
		if ($get) {
			return join('',array(
				'<a href="?event='.$event.a.'step='.$step.a.$thing.'='.urlencode($value),
				($thing2) ? a.$thing2.'='.$thing2val : '',
				'"',
				' class="dlink"',
				' onclick="return verify(\'',
				($verify) ? gTxt($verify) : gTxt('confirm_delete_popup'),
				'\')">&#215;</a>'
			));
		}

		return join('',array(
			'<form action="index.php" method="post" onsubmit="return confirm(\''.gTxt('confirm_delete_popup').'\');">',
			fInput('submit','','&#215;','smallerbox'),
			eInput($event).sInput($step),
			hInput($thing,$value),
			($thing2) ? hInput($thing2,$thing2val) : '',
			'</form>'));


	}

// -------------------------------------------------------------
	function aLink($event,$step,$thing,$value,$thing2,$value2)
	{
		$o = '<a href="index.php?event='.$event.a.'step='.$step.
			a.$thing.'='.urlencode($value).a.$thing2.'='
			.urlencode($value2).'"';
		$o.= ' class="alink">+</a>';
		return $o;
	}

// -------------------------------------------------------------
	function prevnext_link($name,$event,$step,$id,$titling='')
	{
		return '<a href="?event='.$event.a.'step='.$step.a.'ID='.$id.
			'" class="navlink" title="'.$titling.'">'.$name.'</a> ';
	}

// -------------------------------------------------------------
	function PrevNextLink($event,$topage,$label,$type)
	{
		return join('',array(
			'<a href="?event='.$event.a.'step=list'.a.'page='.$topage.'" class="navlink">',
			($type=="prev") ? '&#8249; '.$label : $label.' &#8250;',
			'</a> '
		));
	}

// -------------------------------------------------------------
	function startSkelTable()
	{
		return 
		'<table width="300" cellpadding="0" cellspacing="0" style="border:1px #ccc solid">';
	}

// -------------------------------------------------------------
	function startTable($type,$align='')
	{
		$p = ($type=='edit') ? 3 : 0;
		$align = (!$align) ? 'center' : $align;
		return
		'<table cellpadding="'.$p.'" cellspacing="0" border="0" id="'.
			$type.'" align="'.$align.'">'.n;
	}
	
// -------------------------------------------------------------
	function endTable ()
	{
		return n.'</table>'.n;
	}
	
// -------------------------------------------------------------
	function stackRows() 
	{
		foreach(func_get_args() as $a) { $o[] = tr($a); }
		return join('',$o);
	}
	
// -------------------------------------------------------------
	function td($content='',$width='',$class='',$id='')
	{
		$content = (!$content) ? '&#160;' : $content;
		$atts[] = ($width)  ? ' width="'.$width.'"' : '';
		$atts[] = ($class)  ? ' class="'.$class.'"' : '';
		$atts[] = ($id)  ? ' id="'.$id.'"' : '';
		return t.tag($content,'td',join('',$atts)).n;
	}

// -------------------------------------------------------------
	function tda($content,$atts='')
	{
		return tag($content,'td',$atts);
	}

// -------------------------------------------------------------
	function tdtl($content,$atts='')
	{
		return tag($content,'td',' style="vertical-align:top;text-align;left;padding:8px"');
	}

// -------------------------------------------------------------
	function tr($content,$atts='')
	{
		return tag($content,'tr',$atts);
	}

// -------------------------------------------------------------
	function tdcs($content,$span,$width="")
	{
		return join('',array(
			t.'<td align="left" valign="top" colspan="'.$span.'"',
			($width) ? ' width="'.$width.'"' : '',">$content</td>\n"
		));
	}

// -------------------------------------------------------------
	function tdrs($content,$span,$width="")
	{
		return join('',array(
			t.'<td align="left" valign="top" rowspan="'.$span.'"',
			($width) ? ' width="'.$width.'"' : '',">$content</td>".n
		));
	}

// -------------------------------------------------------------
	function fLabelCell ($text,$help='') 
	{
		$help = ($help) ? popHelp($help) : '';
		return tda(gTxt($text).$help,' style="vertical-align:middle;text-align:right"');
	}

// -------------------------------------------------------------
	function fInputCell ($name,$var='',$tabindex='',$size='',$help="") 
	{
		$pop = ($help) ? popHelp($name) : '';
		return tda(fInput('text',$name,$var,'edit','','',$size,$tabindex).$pop
		,' valign="top" align="left"');
	}

// -------------------------------------------------------------
	function tag($content,$tag,$atts='') 
	{
		return ($content) ? '<'.$tag.$atts.'>'.$content.'</'.$tag.'>' : '';
	}

// -------------------------------------------------------------
	function graf ($item,$atts='') 
	{
		return tag($item,'p',$atts);
	}

// -------------------------------------------------------------
	function hed($item,$level,$atts='') 
	{
		return tag($item,'h'.$level,$atts);
	}

// -------------------------------------------------------------
	function strong($item)
	{
		return tag($item,'strong');
	}	

// -------------------------------------------------------------
	function htmlPre($item)
	{
		return '<pre>'.tag($item,'code').'</pre>';
	}	

// -------------------------------------------------------------
	function comment($item)
	{
		return '<!-- '.$item.' -->';
	}	

// -------------------------------------------------------------
	function small($item)
	{
		return tag($item,'small');
	}	

// -------------------------------------------------------------
	function assRow($array)
	{
		foreach($array as $a => $b) $o[] = tda($a,' width="'.$b.'"');
		return tr(join(n.t,$o));
	}
	
// -------------------------------------------------------------
	function assHead()
	{
		$array = func_get_args();
		foreach($array as $a) $o[] = hCell(gTxt($a));
		return tr(join('',$o));
	}

// -------------------------------------------------------------
	function popHelp($helpvar,$winW='',$winH='') 
	{
		return join('',array(
			' <a target="_blank" href="http://www.textpattern.com/help/?item='.$helpvar.'"',
			' onclick="',
			"window.open(this.href, 'popupwindow', 'width=",
			($winW) ? $winW : 400,
			',height=',
			($winH) ? $winH : 400,
			',scrollbars,resizable\'); return false;" class="pophelp">?</a>'
		));
	}

// -------------------------------------------------------------
	function popHelpSubtle($helpvar,$winW='',$winH='') 
	{
		return join('',array(
			' <a target="_blank" href="http://www.textpattern.com/help/?item='.$helpvar.'"',
			' onclick="',
			"window.open(this.href, 'popupwindow', 'width=",
			($winW) ? $winW : 400,
			',height=',
			($winH) ? $winH : 400,
			',scrollbars,resizable\'); return false;">?</a>'
		));
	}


// -------------------------------------------------------------
	function popTag($var,$text,$winW='',$winH='') 
	{
		return join('',array(
			' <a target="_blank" href="?event=tag'.a.'name='.$var.'"',
			' onclick="',
			"window.open(this.href, 'popupwindow', 'width=",
			($winW) ? $winW : 400,
			',height=',
			($winH) ? $winH : 400,
			',scrollbars,resizable\'); return false;">',
			$text,'</a>'
		));
	}
	
// -------------------------------------------------------------
	function popTagLinks($type) 
	{
		global $txpcfg;
		include $txpcfg['txpath'].'/lib/taglib.php';
		$arname = $type.'_tags';
		asort($$arname);
		foreach($$arname as $a) {
			$out[] = popTag($a,gTxt('tag_'.$a));
		}
		return join(br,$out);
	}

//-------------------------------------------------------------
	function messenger($thing,$thething,$action)
	{
		return gTxt($thing).' '.strong($thething).' '.gTxt($action);
	}

?>
