<?php

if (!defined('APP')) {die('ERROR');};

class Paginator
{

function __construct($params)
{
}

function SPages(&$owner, $page, $where, $category, $articles,  $plink, $table)
{
$owner->components['db']->setTable($table);
$owner->components['db']->Select('count(*) as tpages', $where);
$link = "";
$data = $owner->components['db']->Read();
  $TOTAL_PAGES = $data['tpages'];

	 $PAGES_SELECTOR_COUNT = floor($TOTAL_PAGES / $articles);
	 if (($TOTAL_PAGES % $articles)>0) {$PAGES_SELECTOR_COUNT ++;};
	 if ( $PAGES_SELECTOR_COUNT >=1 )
	 {
	 $paginator = '';
	 
	 
	 if ($page>1) {
	 $prev_link = $page-1; 
	 $html_prevlink = "<a class='pagelink' href='"."$plink"."$prev_link"."'>&larr;</a>&nbsp;";
	 } 
	 else {$html_prevlink="";};
	 
	 if ($page<round($TOTAL_PAGES / $articles)) {
	 $next_link = $page+1; 
	 $html_nextlink = "<a class='pagelink' href='"."$plink"."$next_link"."'>&rarr;</a>&nbsp;";
	 } else {$html_nextlink="";};
	 
	 
	 for ($p=$page-3; $p<=($page+3); $p++)
	 {
	 if (($p<1) ||  ($p>$PAGES_SELECTOR_COUNT)) {continue;};
	 
	 if ($page!=$p)
	 {
		 $link = "<a class='pagelink' href='"."$plink"."$p"."'>"."$p"."</a>&nbsp;";
	 }
	  else
	  {
	  $link = "<b>[$p]</b>&nbsp;";
	  }
		 $paginator .= $link;
	 };
	 } else $PAGES_SELECTOR_COUNT = 1;
	 $paginator = $html_prevlink.$paginator.$html_nextlink;
     $owner->components['view']->SetVar('PAGES', "$page / $PAGES_SELECTOR_COUNT ".$paginator);
}


function Pages(&$owner, $page, $attribute, $category, $articles,  $plink, $table)
{
$owner->components['db']->setTable($table);
if ($category == "") {$category = "1=1";} else {$category = "$attribute = '$category'";};
$owner->components['db']->Select('count(*) as tpages', $category);
$link = "";
$data = $owner->components['db']->Read();
  $TOTAL_PAGES = $data['tpages'];

	 $PAGES_SELECTOR_COUNT = floor($TOTAL_PAGES / $articles);
	 if (($TOTAL_PAGES % $articles)>0) {$PAGES_SELECTOR_COUNT ++;};
	 if ( $PAGES_SELECTOR_COUNT >=1 )
	 {
	 $paginator = '';
	 for ($p=1; $p<=$PAGES_SELECTOR_COUNT; $p++)
	 {
	 if ($page!=$p)
	 {
		 $link = "<a class='pagelink' href='"."$plink"."$p"."'>"."$p"."</a>&nbsp;";
	 }
	  else
	  {
	  $link = "<b>[$p]</b>&nbsp;";
	  }
		 $paginator .= $link;
	 };
	 } else $PAGES_SELECTOR_COUNT = 1;
	 
     $owner->components['view']->SetVar('PAGES', "$page / $PAGES_SELECTOR_COUNT ".$paginator);
}
}

?>