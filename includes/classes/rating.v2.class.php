<?php
/****************************************************************\
## FileName: rating.v2.class.php									 
## Author: Brad Riemann										 
## Usage: Rating Class and Functions
## Copyright 2015 FTW Entertainment LLC, All Rights Reserved
## Updated: 02/21/2014 by Robotman321
## Version: 1.0
\****************************************************************/

class Rating extends Config {
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function array_ratingsInformation($id, $UserID=0,$type=0)
	{
		// The object of this is to give all of the rating information about a specific item.
		// we need to figure out what the rating type can be..
		if($type == 1)
		{
			// this is for comment ratings
			$rating_id = "c$id";
		}
		else
		{
			// we will default to video ratings first..
			$rating_id = "v$id";
		}
		$query = "SELECT `rating_num`, `IP` FROM `ratings` WHERE `rating_id` = '$rating_id'";
		$result = $this->mysqli->query($query);
		$count = mysqli_num_rows($result);
		// some vars
		$rated = -1; // we set to 0 by default, assuming no one has rated anything before.
		$returnarray = array(); // the array we will be returning.
		$ratingstotal = 0; // we will add each rating up then average it for our average rating.
		$i = 1;
		if($count > 0)
		{
			while($row = $result->fetch_assoc())
			{
				if($type == 0 && $UserID == $row['IP'])
				{
					// This person rated this episode.
					$rated = $row['rating_num'];
				}
				$returnarray['ratings'][$row['IP']] = $row['rating_num'];
				$ratingstotal = $ratingstotal+$row['rating_num'];
				// if not, then they have not rated the episode.
				$i++;
			}
			// we give back a rounded number so they can see an average rating to the tenth
			$returnarray['average-rating'] = round($ratingstotal/$i,1);
		}
		else
		{
			$returnarray['average-rating'] = 0;
		}
		// the user rated on this episode already.
		$returnarray['user-rated'] = $rated;
		return $returnarray;
	}
}