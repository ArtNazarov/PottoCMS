<?php



function first_day($m, $y)
{
$timestamp = mktime(0,0,0, $m, 1, $y);
$dw = date( "w", $timestamp);
return $dw;
}

function num_day($d, $m, $y)
{
$timestamp = mktime(0,0,0, $m, $d, $y);
$dw = date( "w", $timestamp);
return $dw;
}

function rus_day($d, $m, $y)
{
$timestamp = mktime(0,0,0, $m, $d, $y);
$dw = date( "w", $timestamp);
$standard_week = array("Вс", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб");
return $standard_week[$dw];
}


function w_timeline($m, $y, $links)
{
$start_day = 1;
$end_day = cal_days_in_month(CAL_GREGORIAN, $m, $y);
$html = "<table class='cal' border='1'><tr><td>$m-$y</td>";
for ($d = $start_day; $d<=$end_day; $d++)
{
     $rd = rus_day($d, $m, $y);
	if ($links["d$d"]=='')
	
		{
   
		$html.= "<td>$d <br/> $rd</td>"; }
		else
		{
		$alt = $links["d$d"];
		$html .= 
		"<td style='background-color:#DD0000'><a title='$alt' href=".$links['base_url'].'/'.$links['action_url']."/$d-$m-$y>$d <br/> $rd</a></td>";
		};
};
$html .="</tr></table>";
return $html;
}


function w_calendar($m, $y, $links)
{
$rus_week = array("Пн", "Вт", "Ср", "Чт", "Пт", "Сб", "Вс");
$start_day = 1;
$end_day = cal_days_in_month(CAL_GREGORIAN, $m, $y);
$html = "<table class='cal' border='1'>";
$html .="<tr><td colspan='7' align='center'>$m-$y</td></tr>";
$r = 1;
$c = first_day($m, $y)-1;
for ($i=1; $i<=7; $i++)
{
			for ($j=1; $j<=7; $j++)
			{
				$cell[$i][$j] = '&nbsp;';
			};
};
for ($d = 1; $d<=$end_day; $d++)
	{
		
			$dw = num_day($d, $m, $y);
			if ($dw == 1) 
				{
				if ($d!=1) {$r = $r+1;};
				$c = 1;
			    }   else {$c = $c + 1;}
			$cell[$r][$c] = $d;	
			$ptr[$r][$c] = $links['basic_url'].$links['action_url']."/$d-$m-$y";
			$setted[$r][$c] = $links["d$d"];
            
	};
	$html .= "<tr>";
	for ($i=0; $i<7; $i++)
	{
	  $html .= "<td>".$rus_week[$i]."</td>";
	}
	$html .= "</tr>";
	for ($i=1; $i<=$r; $i++) 
	{
	$html .="<tr>";	
		for ($j=1; $j<=7; $j++) 
		{	
            if ($setted[$i][$j]=='')
			{
			if ($j>5)
			{
			$html .= "<td style='background-color:#FF9933'>".$cell[$i][$j]."</td>";
			}
			else
			{
			$html .= "<td style='background-color:#CCCCCC'>".$cell[$i][$j]."</td>";
			};
			}
			else
			{
			$d = $cell[$i][$j];
			$alt = $links["d$d"];
			$html .= "<td style='background-color:#DD0000'><a title='$alt' href=".$ptr[$i][$j].">".$cell[$i][$j]."</a></td>";
			};
		}
			
	
	  $html .= "</tr>";	
    };		
		

$html .= "</table>";
return $html;
}

function w_progress_bar($width, $height, $percent, $text, $colorw, $colorm)
{
$inner_width = floor($percent*$width);
$p = floor($percent*100);
$html = "<div style='background-color:#$colorw; width:$width; height:$height'>
<div style='position:relative'>
<div class='meter' style='background-color:#$colorm; width:$inner_width; height:$height'></div>
<div style='position:absolute; text-align:center; width:100%; top:0; left:0;'>$text $p%</div>
</div>
</div>";
return $html;
}

function w_step_bar($width, $height, $total_steps, $current_step, $g_text, $step_text)
{
$percent = $current_step / $total_steps;
$inner_width = floor($percent*$width);
$p = floor($percent*100);
$html = "<div style='background-color:#FF99CC; width:$width; height:$height'>
<div style='position:relative'>
<div class='meter' style='background-color:#FF6699; width:$inner_width; height:$height'></div>
<div style='position:absolute; text-align:center; width:100%; top:0; left:0;'>$g_text($current_step из $total_steps):$step_text</div>
</div>
</div>";

return $html;
}

function w_notify($h, $b, $color)
{
$id = 'notify'.rand(1,999).'block'.rand(1,999);
$html = "<div id='$id' ";
$html .= " style='position:absolute; z-index:1999; top:0; left:40%; box-shadow:2px 2px 3px #000000; background-color:#$color' ";
$html .= " onclick ="."document.getElementById('$id').style.display='none';".">[$h]<br/>";
$html .=$b."</div>";
return $html;
}

function w_msg($h, $b, $color)
{
$id = 'notify'.rand(1,999).'block'.rand(1,999);
$html = "<div id='$id' ";
$html .= " style='box-shadow:2px 2px 3px #000000; background-color:#$color' ";
$html .= " onclick ="."document.getElementById('$id').style.display='none';".">[$h]<br/>";
$html .=$b."</div>";
return $html;
}

function w_pages($pages, $open_page, $width, $height, $bottom = false)
{
$count = count($pages);
$id = 'pages'.rand(1,999).'block'.rand(1,999);
$js_heading = <<<JS
<script language='javascript'>
page_$id = $open_page;
</script>
JS;
$html = "";
$current_page = 1;
$pager = "<tr>";
foreach ($pages as $title=>$body)
{
$pager .= "<td style='cursor:pointer' id='title$id$current_page' onclick='fpage_$w_$id($current_page)'>$title</td>"; 
$current_page++;
}
$pager .= "</tr>";
$html.="<tr><td colspan=$count>";
$current_page = 1;
foreach ($pages as $title=>$body)
{
if ($current_page == $open_page) {$display = "block";} else {$display="none";};
$html .= "<div id='page$id$current_page' style='display:$display'>$body</div>";
$current_page++;
};
$html.="</td></tr>";
if ($bottom == false)
{
	$html = $js_heading."<table width='$width' height='$height'>".$pager.$html."</table>";
}
else
  { $html = $js_heading."<table width='$width' height='$height'>".$html.$pager."</table>"; };
 
$html.=<<<JS
<script language='javascript'>
document.getElementById('title$id$open_page').style.backgroundColor="#FFCCFF";
function fpage_$id(n)
{

title_show = "title$id"+String(n);
page_show = "page$id"+String(n);

title_hide = "title$id"+String(page_$id);
page_hide = "page$id"+String(page_$id);

page_$id = n;

document.getElementById(page_show).style.display="block";
document.getElementById(page_hide).style.display="none";

document.getElementById(title_hide).style.backgroundColor="#FFFFFF";
document.getElementById(title_show).style.backgroundColor="#FFCCFF";

}
</script>
JS;
return $html;
}

function w_analog($text, $cur, $min, $max)
{
$id = 'analog'.rand(1,999).'block'.rand(1,999);
$width = 120;
$percent = ($cur-$min)/($max-$min);
$stepR = ($max-$min)/10;
$html = "<canvas id='$id' width='$width' height='$width'></canvas>";
$js = <<<JS
<script language="javascript">
var canvas = document.getElementById('$id'); 
var context = canvas.getContext("2d");
var centerX = $width / 2;
    var centerY = $width-5;
    var radius = $width / 2;
    var startingAngle = 1.0 * Math.PI;
    var endingAngle = 2.0 * Math.PI;
	var toAngle = $percent * (endingAngle-startingAngle) + startingAngle;
	
	
    
    context.arc(centerX, centerY, radius / 6, startingAngle,    -startingAngle);
 
context.fill();
context.save();


context.strokeStyle = "red";
 context.lineWidth = 3;
 context.moveTo(centerX, centerY);
 context.lineTo(centerX+radius*Math.cos(toAngle), centerY+radius*Math.sin(toAngle));
 context.lineWidth = 1;
 context.stroke();
 context.save();
 context.strokeStyle = "#cc0000"; // line color		
 context.strokeText("$text = $cur; ц.д. = $stepR", 0, centerY); 	
 context.save();
 toAngle = startingAngle;
 stepAngle = (endingAngle - startingAngle) / 10;
 
 for (var i=0; i<=10; i++)
 {
 context.restore();
  context.moveTo(centerX+radius*0.5*Math.cos(toAngle), centerY+0.5*radius*Math.sin(toAngle));
  context.lineTo(centerX+radius*0.55*Math.cos(toAngle), centerY+0.55*radius*Math.sin(toAngle));
 context.stroke();
 context.save();
 toAngle = toAngle + stepAngle;
 }
 
 
</script>
JS;
$html .= $js;
return $html;
}


?>

