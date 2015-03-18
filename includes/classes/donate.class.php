<?php
/****************************************************************\
## FileName: donate.class.php									 
## Author: Brad Riemann										 
## Usage: Donate page class
## Copyright 2012 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class AFTWDonate {
	
	/* The premise of this script is to create a central place where we can push and show users the data they need 
	#  To gain information regarding current donation runs for AnimeFTW.tv
	#  Due to our past issue with paypal, this page MUST be secured through the header for members only.
	#  Goals: Create a page, that relies on the counts from the paypal_donations table, since everytime a user donates it gets 
	#		  added to this table.
	#  Notes: This is to be like kickstarter, two column setup, with possiblity for tabbed content, lets think small and
	#  		  go with one page, right nav starts with the goal, then the progress bar, then the levels for donors, that are associated 
	#		  with that specific donation drive. Variables will be taken from the global settings table, then parsed out from the 
	#		  donation table and the table that handles the donation levels, brought together in this script.
	*/
	
	//#- Vars -#\\
	var $profileArray, $donation_round, $donation_name, $donation_active, $donation_goal;
	
	//#- Contruct -#\\
	public function __construct(){
		$this->BuildGlobalVars(); // we offload the queries to make the construct less messy.. yay
	}
	
	//#- Public Functions -#\\
	public function Build($profileArray){
		$this->profileArray = $profileArray;
	}
	
	public function Output(){
		$this->LeftColumn();
		echo "</td>\n";
		echo "<td style='padding-left:10px; width:250px;  vertical-align:top;' class='main-right'>\n";
		$this->RightColumn(); // Right column data..
		$this->UpdateViews();
	}
	
	public function ScriptsOutput(){
		$this->BuildDonatePage();
		$this->DonateJumpPage();
	}
	
	private function LeftColumn(){
		echo '<span class="scapmain">AnimeFTW.tv Donation Area</span>
			<br />
			<span class="poster">The website and all our services are completely self funded, we rely on <i>viewers like you</i> to maintain our Anime Library and keep us going strong!</span>
			</div>';
		$query = mysql_query("SELECT description FROM donation_settings WHERE round_id = ".$this->donation_round);
		$row = mysql_fetch_array($query);
		echo '<div class="tbl">';
		echo $row['description'];
		echo '</div><br />';
		echo '<div class="side-body-bg"><span class="scapmain">Specific Prize Details</span>
			<br />
			<span class="poster">We we\'re asked about what types of boxed anime would we offer, well here is a list of the ones that we either have or can get super quick :)</span>
			</div>';
		echo '<div class="tbl">';
		echo 'Here is a current list of DVD/BD box sets we can get in a snap or have:
			<ul>
			<li>Red Garden (DVD)</li>
			<li>Ergo Proxy (DVD)</li>
			<li>Shuffle (DVD)</li>
			<li>Trigun (DVD)</li>
			<li>Kanon (DVD)</li>
			<li>Utawarerumono (DVD)</li>
			<li>Desert Punk (DVD)</li>
			<li>Gunslinger Girl (DVD)</li>
			<li>Tokyo Majin (DVD)</li>
			<li>Welcome to the NHK (DVD)</li>
			<li>Air Gear (DVD)</li>
			<li>Mongolian Chop Squad (DVD)</li>
			<li>Claymore (DVD)</li>
			<li>Trinity Blood (DVD)</li>
			<li>Gurren Lagann (DVD)</li>
			<li>Black Cat (DVD)</li>
			<li>Melancholy of Haruhi Suzumiya (DVD)</li>
			<li>Samurai Champloo (DVD) & (BD)</li>
			<li>Ragnarok (DVD)</li>
			<li>Chaos;Head (BD/DVD Combo)</li>
			</ul><br />
			There are more that we can get, just shoot us a PM if there is one you are looking for and we can tell you if it will be do-able or not :)';
		echo '</div><br />';
		echo '<div class="tbl">';
		echo 'It was asked of us, what type of space would we gain from this upgrade, and why would you need so much space?<br /><br />
		Well, the simple answer is this, we currently utilize 4TB of space over two RAID-1 drive arrays, it\'s nice but not something to be super proud of.. In the new system, we will employe 12, 2TB disks, over a <a href="http://en.wikipedia.org/wiki/RAID_5#RAID_5">RAID 5</a>, which would give us roughly 20.5TB of usable space, all while keeping a new level of redundancy in place. Not to mention, we will keep Zeus 1.0 onsite as a backup box, using it to sync files and possibly as a hot failover in case of an emergency. <br /><br /> There has been months of thought and work put into this setup, it gives us an easy 4-5 year growth period without the need to worry about space, and has the processing power to let us do what we need in terms of video manipulation.</div>';
	}
	
	private function RightColumn(){
		$this->ProgressBox();
		$this->DonationTiers();
		echo '<div class="apple_overlay" id="donate">';
		echo '<h2 style="margin:0px">Donate to AnimeFTW.tv</h2>';
		echo '<div class="comments" id="donatediv">Loading. Please Wait...</div>';
		echo '</div>';
	}
	
	private function ProgressBox(){
		$goal = $this->donation_goal;
		$query = mysql_query("SELECT mc_gross FROM donation_paypal WHERE item_name = '".$this->donation_name."'");
		$total = 0;
		while(list($mc_gross) = mysql_fetch_array($query)){
			$total = $total+$mc_gross;
		}
		$current = $total;
		$whatsleft = $goal-$current;
		$percentage = ($current/$goal)*100;
		if($percentage < 100){
			$percentage = substr($percentage, 0, 2);
		}
		else {
			$percentage = substr($percentage, 0, 3);
		}
		//$percentage = $percentage+8;
		echo "<div class='side-body-bg'>";
		echo "<div class='scapmain'>Current Progress</div>\n";
		echo "<div class='side-body floatfix'>\n";
		echo '<div align="left">';
		$data = "<div id=\"progress-bar\" class=\"all-rounded\">\n<div id=\"progress-bar-percentage\" class=\"all-rounded\" style=\"width: $percentage%\">";
        if ($percentage > 5) { $data .= "&nbsp;$percentage%";} else {$data .= "<div class=\"spacer\">&nbsp;$percentage%</div>";}
		$data .= "</div></div>";
		echo '<span class="goal" style="margin-left:60px;font-size:22px;">Goal: </span><span class="goalamount" style="font-size:22px;font-weight:bold;color:#00a232;">$'.$goal.'</span><br />';
		echo $data;
		echo '<span class="current" style="margin-left:28px;font-size:22px;">Current: </span><span class="currentamount" style="font-size:20px;font-weight:bold;">$'.$current.'</span><br />';
		echo '<span class="current" style="font-size:22px;">Difference: </span><span class="currentamount" style="font-size:18px;font-weight:bold;">$'.$whatsleft.'</span><br />';
		echo "</div></div></div>\n";
		
	}
	
	private function DonationTiers(){
		echo "<div class='side-body-bg'>";
		echo "<div class='scapmain'>Donation Tiers</div>\n";
		echo '<div style="font-size:10px;" align="center">(Click a box to proceed)</div>';
		$query = mysql_query("SELECT COUNT(id) FROM donation_tiers WHERE donation_round = ".$this->donation_round);
		$total = mysql_result($query, 0);
		if($total == 0){ // we need to be able to display nothing if an error occours.. gogo awesomesauce
			echo "<div class='side-body floatfix' align='center'>\n";
			echo "<h4>The donations are either closed or misconfigured.</h4>";
			echo "</div>";
		}
		else {
			$query = "SELECT id, name, donate, donate_limit, details FROM donation_tiers WHERE donation_round = ".$this->donation_round." ORDER BY level ASC";
			$results = mysql_query($query);
			while(list($id,$name,$donate,$donate_limit,$details) = mysql_fetch_array($results)){
				echo '<div id="tieritem" class="floatfix">
					<a href="#" rel="#donate" onClick="javascript:ajax_loadContent(\'donatediv\',\'/scripts.php?view=donate&id='.$id.'\');">
					<div class="tiertitle">'.$name.'</div>
					'.$this->CalculateDonors($donate,$donate_limit).'
					<div class="tierdonation"><i>Donate '.$donate.' Dollars or more.</i></div>
					<div>Prizes: <br />'.$details.'</div>
					</a>
				</div>';
			}
		}
		echo "</div>\n";
	}
	
	# function CalculateDonors
	private function CalculateDonors($donate,$dlimit){
		// This is basically going to check all the donation records and let us know if it falls under the specifications
		if($dlimit != 0){$limit = ' AND mc_gross <= '.$dlimit;}else{$limit = '';} //if the tier is the top tier theres nothing to compare it with, so don't give it the limit..
		$query = "SELECT count(id) FROM donation_paypal WHERE mc_gross >= ".$donate.$limit." AND item_name = '".$this->donation_name."'";
		$query = mysql_query($query);
		$total = mysql_result($query, 0); // nom nom nom, counting...
		if($total > 0){
			$querya = "SELECT first_name FROM donation_paypal WHERE mc_gross >= ".$donate.$limit." AND item_name = '".$this->donation_name."'";
			$query = mysql_query($querya);
			$i = 0; $first_name1 = '';
			while(list($first_name) = mysql_fetch_array($query)){
				$first_name1 .= $first_name;
				if($i < ($total-1)){$first_name1 .= ', ';}
				$i++;
			}
			return '<div class="totaldonors"><b>'.$total.' Donors</b><div style="font-size:8px;">Thanks to: '.$first_name1.'</div></div>'; // return the variable so it doesnt screw everything up!
		}
		else {
			return '<div class="totaldonors"><b>'.$total.' Donors</b></div>'; // return the variable so it doesnt screw everything up!
		}
	}
	
	# function BuildGlobalVars
	private function BuildGlobalVars(){
		// Function is designed to query the settings table to load the global class variables for the donation script
		$query = "SELECT name, value FROM settings WHERE name = 'donation_round' OR name = 'donation_active'";
		$results = mysql_query($query);
		while(list($name,$value) = mysql_fetch_array($results)){
			if($name == 'donation_round'){ // if this is the donation round, lets set it.
				$this->donation_round = $value;
			}
			if($name == 'donation_active'){ // if the donations are active, let's set that as well
				$this->donation_active = $value;
			}
		}
		$query = "SELECT goal, round_name FROM donation_settings WHERE round_id = ".$this->donation_round;
		$results = mysql_query($query);
		$row = mysql_fetch_array($results);
		$this->donation_name = $row['round_name'];
		$this->donation_goal = $row['goal'];		
	}
	
	private function BuildDonatePage(){
		if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
			echo 'invalid ID';
		}
		else {
			if(isset($_GET['step']) && $_GET['step'] == 'after'){
			}
			else {
				$tid = $_GET['id'];
				$query = mysql_query("SELECT name, donate, details FROM donation_tiers WHERE id = ".mysql_real_escape_string($tid));
				$row = mysql_fetch_array($query);
				echo '<form method="GET" name="fd4" id="fd4">
				<input type="hidden" name="rid" value="1">
				<br /><br /><div class="donatestep1" align="center">';
				echo '<label class="left" for="amount" style="margin: 0px 0px 0px 0px;color:#555555;">Donation Amount:</label>
					<input name="amount" id="amount" type="text" class="donateForm" value="'.$row['donate'].'.00" /><br />
					<div style="color:#555555;text-size:6px;">(or More!)</div><br />';
				echo '</div><br /><br />';
				echo '<div class="donatestep2" align="center">';
				echo '<label class="left" for="price" style="margin: 0px 0px 0px 0px;color:#555555;">Prizes for a Donation:</label>';
				echo '<div class="pricedetails">'.$row['details'].'</div>';
				echo '</div><br /><br />';
				echo '<div class="donatestep2">';
				echo '<div align="center" style="color:#5A5655;"><i>Just a friendly reminder<br /> AnimeFTW.tv is completely crowd funded, we rely on your donations to expand and exist. <br />Without our fans and our members, we wouldn\'t be able to share the greatness and the fun that anime is!</i></div>';
				echo '</div><br />';
				echo '<div align="center" class="donate_button"><br /><a href="#" onclick="ajax_loadContent(\'donatediv\',\'https://www.animeftw.tv/scripts.php?view=donate&id='.$tid.'&step=after&random=sometime\' + getFormElementValuesAsString(document.forms[\'fd4\'])); return false;">Continue to Choose the method of Donation!</a></div>
					</form>';
			}
			
		}
		
	}
	
	private function DonateJumpPage(){
		if(!isset($_GET['step']) || $_GET['step'] != 'after'){
		}
		else {
			//echo $_SERVER['REQUEST_URI'];
			$tid = $_GET['id'];
			if(!isset($tid)){
				echo 'error';
			}
			else {
				$query = mysql_query("SELECT name, details FROM donation_tiers WHERE id = ".mysql_real_escape_string($tid));
				$row = mysql_fetch_array($query);
				echo '<br /><div class="donatestep2" align="center"><label class="left" for="amount" style="margin: 0px 0px 0px 0px;color:#555555;">Confirmation:</label>';
				echo '<div class="pricedetails" align="left">You have chosen to donate $<b>'.$_GET['amount'].'</b>, the prize package is <b>'.$row['name'].'</b>, it entails the following:<br />'.$row['details'].'</div>';
				echo '</div><br />';
				echo '<div class="donatestep2" align="center">Below are the current supported methods for donations/transactions. At the time of creation, FTW Entertainment LLC only supports two methods, Paypal and Google Wallet. If you cannot donate due to location restrictions and/or unsupported CC\'s you CAN donate via snail mail, any interested parties are encouraged to pm <a href="https://www.animeftw.tv/pm/compose/1">robotman321</a></div>';
				echo '
				<table width="95%">
				<tr>
				<td width="50%">
				<div align="center">
				<h4>Donate using Google Wallet:</h4><form action="https://checkout.google.com/api/checkout/v2/checkoutForm/Merchant/456133132125502" id="BB_BuyButtonForm" method="post" name="BB_BuyButtonForm" target="_top">
					<input name="item_name_1" type="hidden" value="Zeus 2.0 Server Drive"/>
					<input name="item_description_1" type="hidden" value=""/>
					<input name="item_quantity_1" type="hidden" value="1"/>
					<input name="item_price_1" type="hidden" value="'.$_GET['amount'].'"/>
					<input name="item_currency_1" type="hidden" value="USD"/>
					<input name="shopping-cart.items.item-1.digital-content.key" type="hidden" value="wV6ez3FK/WUTuAhNsh5+NsU5txukR7oi4mGRLMH4OM4="/>
					<input name="shopping-cart.items.item-1.digital-content.key.is-encrypted" type="hidden" value="true"/>
					<input name="shopping-cart.items.item-1.digital-content.url" type="hidden" value="http://www.aniemftw.tv"/>
					<input name="_charset_" type="hidden" value="utf-8"/>
					<input alt="" src="https://www.animeftw.tv/images/google-wallet.png" type="image"/>
				</form>
				</div>
				</td>
				<td>
				<div align="center">
				<h4>Donate using Paypal:</h4>
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
				<input type="hidden" name="cmd" value="_donations">
				<input type="hidden" name="notify_url" value="https://ftwentertainment.com/members/paypal.php?action=ipn">
				<input type="hidden" name="amount" value="'.$_GET['amount'].'">
				<input type="hidden" name="item_name" value="Zeus 2.0 Server Drive">
				<input type="hidden" name="quantity" value="1">
				<input type="hidden" name="return" value="https://www.animeftw.tv/donate/thank-you">
				<input type="hidden" name="business" value="paypal@ftwentertainment.com">
				<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
				<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
				</form>
				</div>
				</td>
				</tr>
				</table>
				
				';
			}
		}
	}
	
	private function UpdateViews(){
		mysql_query("UPDATE donation_settings SET views = views+1 WHERE round_id = ".$this->donation_round);
	}
}

?>