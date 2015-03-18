<?php
/****************************************************************\
## FileName: toplist.v2.class.php								 
## Author: Brad Riemann									 
## Usage: TopList scripts
## Copywrite 2014 FTW Entertainment LLC, All Rights Reserved
## Modified: 09/19/2014
## Version: 1.0.0
\****************************************************************/

class toplist extends Config {

	public $Data, $UserID, $DevArray, $AccessLevel, $MessageCodes;

	public function __construct($Data = NULL,$UserID = NULL,$DevArray = NULL,$AccessLevel = NULL)
	{
		parent::__construct();
		$this->Data = $Data;
		$this->UserID = $UserID;
		$this->DevArray = $DevArray;
		$this->AccessLevel = $AccessLevel;
		$this->array_buildAPICodes(); // establish the status codes to be returned to the api.
	}
	
	// Parses through script variables sent via the scripts.php file
	
	public function scriptsFunctions()
	{
		if(isset($_GET['action']) && $_GET['action'] == 'record')
		{
			$this->recordEpisodeView();
		}
		else
		{
			echo 'That was not the function you were looking for..';
		}
	}
	
	public function array_showTopAnime($count = NULL)
	{
		$returnarray = array();
		if($count == NULL && isset($_GET['count']))
		{
			$count = $_GET['count'];
		}
		else if($count != NULL && !isset($_GET['count']))
		{
			$count = $count;
		}
		else
		{
			$count = 25;
		}
		$query = "SELECT `site_topseries`.`seriesId`, `site_topseries`.`lastPosition`, `site_topseries`.`currentPosition`, `series`.`fullSeriesName` FROM `site_topseries`, `series` WHERE `series`.`id`=`site_topseries`.`seriesId` ORDER BY `site_topseries`.`currentPosition` ASC LIMIT 0, " . $this->mysqli->real_escape_string($count);
		$result = $this->mysqli->query($query);
		
		if($result)
		{
			$returnarray['status'] = $this->MessageCodes["Result Codes"]["201"]["Status"];
			$returnarray['message'] = $this->MessageCodes["Result Codes"]["201"]["Message"];
			$returnarray['count'] = $count;
			$i=0;
			while($row = $result->fetch_assoc())
			{
				$returnarray['results'][$i]['id'] = $row['seriesId'];
				$returnarray['results'][$i]['name'] = stripslashes($row['fullSeriesName']);
				$returnarray['results'][$i]['last-position'] = $row['lastPosition'];
				$returnarray['results'][$i]['current-position'] = $row['currentPosition'];
				$i++;
			}
		}
		else
		{
			$returnarray['status'] = $this->MessageCodes["Result Codes"]["05-400"]["Status"];
			$returnarray['message'] = $this->MessageCodes["Result Codes"]["05-400"]["Message"];
		}
		return $returnarray;
	}
	
	// Will take an episode id and record it in the toplist records.
	private function recordEpisodeView()
	{
		// Check if the epid is set.. if it is not throw the book at em!
		if(!isset($_GET['epid']) && !is_numeric($_GET['epid']))
		{
			echo 'Error: The episode ID was invalid or wrong. Please try again.';
		}
		else
		{			
			$query = "SELECT `sid`, `epnumber` FROM `episode` WHERE `id` = '" . $this->mysqli->real_escape_string($_GET['epid']) . "'";
			$result = $this->mysqli->query($query);
			$row = $result->fetch_assoc();
	
			//Get the Date for today, all 24 hours
			$currentDay = date('d-m-Y',time());
			$midnight = strtotime($currentDay);
			$elevenfiftynine = $midnight+86399;
		
			//check for any rows that were done today...
			// we will want to switch out the seriesid and the epnumber for epid later.. just makes it easier..
			$query  = $this->mysqli->query("SELECT `id` FROM `episodestats` WHERE `ip` = '" . $_SERVER['REMOTE_ADDR'] . "' AND `epSeriesId` = '" . $row['sid'] . "' AND `epNumber` = '" . $row['epnumber'] . "' AND `date` >= '" . $midnight . "'");
			$count = mysqli_num_rows($query);
		
			if($count == 0)
			{
				$query = "INSERT INTO `episodestats` (`eid`, `epSeriesId`, `ip`, `date`, `epnumber`)  VALUES ('" . $this->mysqli->real_escape_string($_GET['epid']) . "', '" . $row['sid'] . "', '" . $_SERVER['REMOTE_ADDR'] . "', '".time()."', '" . $row['epnumber'] . "')";
				$result = $this->mysqli->query($query);
			}
		}
	}
}