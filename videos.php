<?php
include('init.php');
$v = new AFTWVideos(); //Build our videos
$p = new AFTWpage();
if(isset($_GET['type']) && $_GET['type'] == 'anime'){
	if(isset($_GET['seo'])){$seo = $_GET['seo'];}else{$seo = '';}
	if(isset($_GET['eid'])){$eid = $_GET['eid'];}else{$eid = '';}
	if(isset($_GET['oid'])){$oid = $_GET['oid'];}else{$oid = '';}
	if(isset($_GET['mid'])){$mid = $_GET['mid'];}else{$mid = '';}
	$PageTitle = $v->PageTitle($seo,$eid,$oid,$mid,$_GET['type']);
}
else {
	$PageTitle = 'Video Library - AnimeFTW.TV';
}
if($_SERVER['REQUEST_URI'] == '/videos/' || $_SERVER['REQUEST_URI'] == '/videos')
{
	header("location: /anime");
}
include('header.php');
include('header-nav.php');

if(isset($_GET['ref']))
{
	//$query = "INSERT INTO `referals` (`Link`, `Destination`, `referalId`, `Date`, `ip`) VALUES ('%s', NULL, '%s', '%s', '%s')";
	$query = "INSERT INTO `referals` (`Link`, `Destination`, `referalId`, `Date`, `ip`) 
	VALUES ('" . mysql_real_escape_string($_SERVER['HTTP_REFERER']) . "', '" . mysql_real_escape_string($_SERVER['REQUEST_URI']) . "', '" . mysql_real_escape_string($_GET['ref']) . "', '" . time() . "', '" . mysql_real_escape_string($_SERVER['REMOTE_ADDR']) . "')";
	mysql_query($query) or die('Could not connect, way to go retard:' . mysql_error());	
}

echo psa($profileArray,2);
$index_global_message = NULL;
function bodyTopInfo($message,$bdybr){
	if($bdybr == NULL){$bodyTop = "";}
	else {$bodyTop = "<br /><br /><br /><br /><br /><br />\n";}
	// Start Main BG
   	$bodyTop .= "<table align='center' cellpadding='0' cellspacing='0' width='".THEME_WIDTH."'>\n<tr>\n";
	$bodyTop .= "<td width='".THEME_WIDTH."' class='main-bg'>\n";
	// End Main BG
	if($message == NULL){}
	else {
    $bodyTop .= "<table cellpadding='0' cellspacing='0' width='100%'>\n<tr>\n";
	$bodyTop .= "<td class='note-message' align='center'>".$message."</td>\n";
	$bodyTop .= "</tr>\n</table>\n";
	$bodyTop .= "<br />\n<br />\n";
	}
	// Start Mid and Right Content
	$bodyTop .= "<table cellpadding='0' cellspacing='0' width='100%'>\n<tr>\n";
	$bodyTop .= "<td valign='top' class='main-mid'>\n";
	return $bodyTop;
}
		if($_GET['node'] == 'sort'){			
			if($_GET['type'] == 'anime'){
				if(isset($_GET['param'])){
					echo bodyTopInfo($index_global_message,'yes');
					echo "<div class='side-body-bg'>\n";
					echo "<span class='scapmain'>AnimeFTW.tv's Anime Selection</span>\n";
					echo "<br />\n";
					echo "<span class='poster'>Sorting through Anime for the tag: <b>".$_GET['param']."</b></span>\n";
					echo "</div>\n";
					echo "<br />";
					echo "<div id=\"lister\">";
					echo '<br />'.tagCloud('anime').'<br />';
					echo $v->showListing(0,$_GET['param'],$profileArray[2],0);
					echo "</div>";
				}
				else {
					if(isset($_GET['vtype'])){
						echo bodyTopInfo($index_global_message,'yes');
						echo "<div class='side-body-bg'>\n";
						echo "<span class='scapmain'>AnimeFTW.tv's Anime Selection</span>\n";
						echo "<br />\n";
						echo "<span class='poster'>Sorting through series for: <b>".$_GET['vtype']."</b></span>\n";
						echo "</div>\n";
						echo "<br />";
						echo "<div id=\"lister\">";
						echo '<br />'.tagCloud('anime').'<br />';
						echo $v->showListing(0,$_GET['vtype'],$profileArray[2],1);
						echo "</div>";
					}
					else {}
				}
				
			}
			if($_GET['type'] == 'drama')
			{
			}
		}
		if($_GET['node'] == 'age'){	
			if($_GET['type'] == 'anime'){
				if(isset($_GET['param'])){
					echo bodyTopInfo($index_global_message,'yes');
					echo "<div class='side-body-bg'>\n";
					echo "<span class='scapmain'>AnimeFTW.tv's Anime Selection</span>\n";
					echo "<br />\n";
					echo "<span class='poster'>Displaying all results that are in the: <b>".$_GET['param']."+</b> age Tag.</span>\n";
					echo "</div>\n";
					echo "<br />";
					echo "<div id=\"lister\">";
					echo '<br />'.tagCloud('anime').'<br />';
					echo $v->showListing(0,$_GET['param'],$profileArray[2],2);
					echo "</div>";
				}
				else {}
			}
		}
		if($_GET['node'] == 'list')
		{
			if($_GET['type'] == 'anime')
			{
				echo bodyTopInfo($index_global_message,'yes');
				echo "<div class='side-body-bg'>\n";
				echo "<span class='scapmain'>AnimeFTW.tv's Anime Selection</span>\n";
				echo "<br />\n";
				echo "<span class='poster'>&nbsp;All of the Anime that AnimeFTW.tv supplies is listed below.</span>\n";
				echo "</div>\n";
				echo "<br />";
				echo '<div align="center" ><a href="#" id="tagcloud-toggle">:: Toggle the Tag Cloud ::</a></div>';
				echo "<div id=\"tagcloud\" style=\"display:none\">";
				echo '<br />'.tagCloud('anime').'<br />';
				echo "</div><br /><div id=\"lister\">";
				echo $v->showListing(0,NULL,$profileArray[2],0);
				echo "</div></div>";
			}
			if($_GET['type'] == 'drama')
			{
				echo bodyTopInfo($index_global_message,'yes');
				echo '<div class="left_articles_mod">
				<h2>AnimeFTW.tv\'s Drama Selection</h2>
				<p class="description">Drama Selection for AnimeFTW.tv</p>
				<div id="lister">';
				echo '<br />'.tagCloud('drama').'<br />';
				echo $v->showListing(1,NULL,$profileArray[2],0);
				echo '</div></div>';
			}
			if($_GET['type'] == 'amv')
			{
				echo bodyTopInfo($index_global_message,'yes');
				echo '<div class="left_articles_mod">
				<h2>AMV\'s Uploaded to AnimeFTW.tv\'s Servers</h2>
				<p class="description">AMVs listed Below</p>
				<div id="lister">';
				echo '<br />'.tagCloud('amvs').'<br />';
				echo $v->showListing(2,NULL,$profileArray[2],0);
				echo '</div></div>';
			}
		}
		if($_GET['node'] == 'video'){
			if($_GET['type'] == 'anime'){
				echo $v->DisplaySeries($seo,$seo,$eid,$oid,$mid);
			}
		}
	//Body part..
	echo "</td>\n";
	echo "</tr>\n</table>\n";
	// Start Main BG
    echo "</td>\n";
	echo "</tr>\n</table>\n";
	// End Main BG
include('footer.php')
?>