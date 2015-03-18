<?php 
/****************************************************************\
## FileName: apiv2.class.php								 
## Author: Brad Riemann									 
## Usage: Central API management Class
## Copywrite 2013 FTW Entertainment LLC, All Rights Reserved
## Modified: 12/08/2013
## Version: 1.5.0
\****************************************************************/

Class api extends Config {
	
	protected $Method, $Style, $Data = array(), $DevTable, $TokenTable, $UserID, $AccessLevel;
	var $username, $password, $DevArray, $MessageCodes;
	
	private $APIActions = array(
		'display-episodes' => array(
			'action' => 'display-episodes',
			'location' => 'episode.v2.class.php', // action location
			'classname' => 'Episode', // class name
			'method' => 'array_displayEpisodes', // method name
			'disabled' => '0',
			'description' => 'Displays all of the episodes for a series with a given id, must have the id of the series to view all episodes.'
		),
		'display-single-episode' => array(
			'action' => 'display-single-episode',
			'location' => 'episode.v2.class.php', // action location
			'classname' => 'Episode', // class name
			'method' => 'array_displaySingleEpisode', // method name
			'disabled' => '0',
			'description' => 'Display a single Episode, displays all of the details to a single episode. Requires an episode id to complete the action.'
		),
		'display-series' => array(
			'action' => 'display-series',
			'location' => 'series.v2.class.php', // action location
			'classname' => 'Series', // class name
			'method' => 'array_displaySeries', // method name
			'disabled' => '0',
			'description' => 'Displays X series in Alphabetic order, will start A-Z with the first 20 series.'
		),
		'display-single-series' => array(
			'action' => 'display-single-series',
			'location' => 'series.v2.class.php', // action location
			'classname' => 'Series', // class name
			'method' => 'array_displaySingleSeries', // method name
			'disabled' => '0',
			'description' => 'Displays a single series, you must have the id of the series in order to use it.'
		),
		'display-movies' => array(
			'action' => 'display-movies',
			'location' => 'episode.v2.class.php', // action location
			'classname' => 'Episode', // class name
			'method' => 'array_displayMovies', // method name
			'disabled' => '1',
			'description' => 'Displays only Movies from the episode listing.'
		),
		'display-latest-episodes' => array(
			'action' => 'display-latest-episodes',
			'location' => 'episode.v2.class.php', // action location
			'classname' => 'Episode', // class name
			'method' => 'array_displayLatestEpisodes', // method name
			'disabled' => '1',
			'description' => 'Displays the X latest episodes, defaults to 30 latest episodes.'
		),
		'display-latest-series' => array(
			'action' => 'display-latest-series',
			'location' => 'series.v2.class.php', // action location
			'classname' => 'Series', // class name
			'method' => 'array_displayLatestSeries', // method name
			'disabled' => '1',
			'description' => 'Display the X latest series, defaults to last 10 added series.'
		),
		'display-tag-cloud' => array(
			'action' => 'display-tag-cloud',
			'location' => 'series.v2.class.php', // action location
			'classname' => 'Series', // class name
			'method' => 'array_displayTagCloud', // method name
			'disabled' => '1',
			'description' => 'Display the tag cloud.'
		),
		'search' => array(
			'action' => 'search',
			'location' => 'search.v2.class.php', // action location
			'classname' => 'Search', // class name
			'method' => 'array_siteSearch', // method name
			'disabled' => '1',
			'description' => 'Search the site for a series or a video.'
		),
		'display-comments' => array(
			'action' => 'display-comments',
			'location' => 'comments.v2.class.php', // action location
			'classname' => 'Comment', // class name
			'method' => 'array_displayComments', // method name
			'disabled' => '0',
			'description' => 'Display comments for a video or profile.'
		),
		'add-comment' => array(
			'action' => 'add-comment',
			'location' => 'comments.v2.class.php', // action location
			'classname' => 'Comment', // class name
			'method' => 'array_addComment', // method name
			'disabled' => '1',
			'description' => 'Add a comment to a video'
		),
		'add-comment' => array(
			'action' => 'logout',
			'location' => 'user.v2.class.php', // action location
			'classname' => 'User', // class name
			'method' => 'logoutUser', // method name
			'disabled' => '1',
			'description' => 'Logs out the user, destroys the Token'
		),
		'register' => array(
			'action' => 'register',
			'location' => 'register.v2.class.php', // action location
			'classname' => 'Register', // class name
			'method' => 'registerUser', // method name
			'disabled' => '1',
			'description' => 'Register an account with AnimeFTW.tv'
		),
		'display-reviews' => array(
			'action' => 'display-reviews',
			'location' => 'review.v2.class.php', // action location
			'classname' => 'Review', // class name
			'method' => 'array_displayReviews', // method name
			'disabled' => '1',
			'description' => 'Display reviews for a specific Series.'
		),
		'random-series' => array(
			'action' => 'random-series',
			'location' => 'series.v2.class.php', // action location
			'classname' => 'Series', // class name
			'method' => 'array_displaySeries', // method name
			'disabled' => '0',
			'description' => 'Display X Random Series.'
		),
		'latest-news' => array(
			'action' => 'latest-news',
			'location' => 'news.v2.class.php', // action location
			'classname' => 'News', // class name
			'method' => 'array_showLatestNews', // method name
			'disabled' => '0',
			'description' => 'Shows the Latest News for the site.'
		),
		'top-series' => array(
			'action' => 'top-series',
			'location' => 'toplist.v2.class.php', // action location
			'classname' => 'toplist', // class name
			'method' => 'array_showTopAnime', // method name
			'disabled' => '0',
			'description' => 'Shows the top X series in the listing'
		),
	);

	// class constructor method
	public function __construct()
	{
		// import the functions from the parent class.
		parent::__construct();
		
		// Globals that need to be filled out.
		$this->DevTable 	= 'developers'; // Developers Table
		$this->TokenTable 	= 'developers_api_sessions'; 	// API tokens Table
		
		// Scripts..
		$this->array_buildAPICodes(); // establish the status codes to be returned to the api.
		$this->determineMethod(); // this will figure out if they are using POST or GET requests
		$this->determineStyle(); // This will figure out if it will be JSON or XML output
		$this->launchAPI(); // This is the main method for the API, after we have done everything else we need to `initialize` the script.
	}
	
	// function will determine what style it is, AND will also validate to make sure an application key is given
	private function determineMethod()
	{
		if(isset($_POST['devkey']))
		{
			$this->Method = 'POST'; // This is a posted method, we need to indicate as such.
			$this->parsePostData(); //we need to have the system parse through the post data.
		}
		else
		{
			if(isset($_GET['devkey']))
			{
				$this->Method = 'GET'; // this is a GET based method, we need to let the rest of the script know.
				$this->parseGetData(); // since it is a GET function, we need to parse through the GET requests.
			}
			else
			{
				$this->reportResult(401);
			}
		}
	}
	
	// function will determine if the developer is requesting JSON or XML output. (We can expand later.)
	private function determineStyle()
	{
		// we check, is there an output format request, is it json?
		if(isset($this->Data['output']) && strtolower($this->Data['output'] == 'json'))
		{
			$this->Style = 'json';
		}
		// since there was nothing for Json, we cannot assume the dev asked for it, we need to see if they want xml
		elseif(isset($this->Data['output']) && strtolower($this->Data['output'] == 'xml'))
		{
			$this->Style = 'xml';
		}
		// for everything else we default to json, cause it looks nicer..
		else
		{
			$this->Style = 'json';
		}
	}
	
	// function will parse through all POST data and subelegate appropriately.
	private function parsePostData()
	{
		$this->Data = $_POST;
	}
	
	// function will parse through all GET data and subelegate appropriately.
	private function parseGetData()
	{
		$this->Data = $_GET;
	}
	
	// Part of the API includes error reporting to the developers, this will need to output formats that 
	// are known by the Devs.
	private function reportResult($ResultCode = 401,$Message = NULL)
	{
		if($Message == NULL)
		{
			// Message is null, which means we can take it from the array
			$Message = $this->MessageCodes["Result Codes"][$ResultCode]["Message"];
		}
		else
		{
			// Message was given, so we need to use THAT.
			$Message = $Message;
		}
		$Result = array('status' => $this->MessageCodes["Result Codes"][$ResultCode]["Status"], 'message' => $Message); // we put the error and the message together for the output..
		$this->formatData($Result); // format the data how the client is looking for..
		exit; // we exit the script so it doesnt keep trying to go forward.
	}	
	
	// function will take an array of data and output to the format requested by the developer
	private function formatData($data)
	{
		// we have to check the data to make sure its an array
		if(is_array($data))
		{
			if($this->Style == 'xml')
			{
				foreach($data as $column => $value)
				{
					echo '<' . $column . '><![CDATA[' . stripslashes($value) . ']]></' . $column . '>';
				}
			}
			// JSON output
			else
			{
				echo json_encode($data, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
			}
			
		}
		else
		{
			// the aw crap option.. the array wasnt an array so we have to halt everything!
			$this->reportResult(400);
		}
	}
	
	// the `final frontier` for the API script, this will get things rolling.
	private function launchAPI()
	{
		// 1) check devkey, verify that its valid.
		// 2) validate the token for the user
		// 3) launch into the sub routines of the API.
		
		// we validate the developer key, if they pass go, they collect 200 bits
		if($this->validateDevKey() == TRUE)
		{
			if(isset($this->Data['username']) && isset($this->Data['password']) && (!isset($this->Data['action']) || (isset($this->Data['action']) && $this->Data['action'] == 'login')))
			{
				$this->tokenAuthorization('create'); // we need to create a token for this user
			}
			else if(!isset($this->Data['token']) && (isset($this->Data['action']) && $this->Data['action'] == 'register'))
			{
				// There was no sign of a login, the last thing we can do in the non-authorized state is to register an account, so lets check that
				include_once("register.v2.class.php");
				$Register = new Register($this->Data,$this->UserID,$this->DevArray);
				$this->formatData($Register->registerUser()); // set the wheels in motion.
			}
			else
			{
				//Username and password were not given, we need to see if the token that should be given is propr
				if($this->tokenAuthorization('validate') == TRUE)
				{
					// the validation of the token has gone through, now the sub functions can be called.
					$this->launchAPISubFunctions();
				}
				else
				{
					// since validation failed, we need to let them know they need to login again.
					$this->reportResult(405);
				}
			}
			// check if username (email) and password are given, if they are, authenticate against the database
			// and get a token created for the user.
		}
		else
		{
			// wrong, good bye..
			$this->reportResult(403);
		}
	}
	
	// function will validate the developer key
	private function validateDevKey()
	{
		$DevKey = $this->Data['devkey']; // declare the developer key from the dataset
		$query = "SELECT `id`, `devkey`, `name`, `info`, `frequency`, `uid`, `ads` FROM `" . $this->MainDB . "`.`" . $this->DevTable . "` WHERE `devkey` = '" . $this->mysqli->real_escape_string($DevKey) . "'";

		$result = $this->mysqli->query($query);
		
		$count = mysqli_num_rows($result);
		
		// if there are no results, throw an error.
		if($count < 1)
		{
			return FALSE; // There was no match.. return a false..
		}
		else
		{
			$row = $result->fetch_assoc();
			$this->DevArray = array(
				'id' => $row['id'], 
				'devkey' => $row['devkey'], 
				'name' => $row['name'], 
				'info' => $row['info'], 
				'frequency' => $row['frequency'], 
				'uid' => $row['uid'], 
				'ads' => $row['ads']
			);
			return TRUE; // Return TRUE to let it continue on.
		}
	}
	
	// Token validation and authorization function, options, `create` and `validate`
	private function tokenAuthorization($Type = 'create')
	{
		if($Type == 'validate')
		{
			// We need to validate the token given to us
			if(isset($this->Data['token']))
			{
				// this will need some sanitization...
				$query = "SELECT `" . $this->TokenTable . "`.`id`, `" . $this->TokenTable . "`.`session_hash`, `" . $this->TokenTable . "`.`date`, `" . $this->TokenTable . "`.`did`, `" . $this->TokenTable . "`.`uid`, `users`.`Level_access` FROM `" . $this->MainDB . "`.`" . $this->TokenTable . "`, `" . $this->MainDB . "`.`users` WHERE `" . $this->TokenTable . "`.`session_hash` = '" . $this->Data['token'] . "' AND `" . $this->TokenTable . "`.`did` = '" . $this->DevArray['id'] . "' AND `users`.`ID`=`" . $this->TokenTable . "`.`uid`";
				$results = $this->mysqli->query($query);
				$count = mysqli_num_rows($results);
				if($count < 1)
				{
					// no rows found, they need to go back and try again.
					return FALSE;
				}
				else
				{
					$row = $results->fetch_assoc();
					$this->UserID 		= $row['uid']; 			// Userid of the user needed later in life
					$this->AccessLevel	= $row['Level_access'];	// Level access of the logged in user.
					// w00t, this is a success, update the date on the row, to make sure.
					$results = $this->mysqli->query("UPDATE `" . $this->MainDB . "`.`" . $this->TokenTable . "` SET `date` = '" . time() . "' WHERE `session_hash` = '" . $this->mysqli->real_escape_string($this->Data['token']) . "' AND did = '" . $this->DevArray['id'] . "'");
					// return true to the system so that it can continue on.
					return TRUE;
				}
			}
			else
			{
				// token was not set, how they got here I dont know..
				return FALSE;
			}
		}
		else
		{
			// Since there are only two functions we, we assume they must be wanting to create a session.
			if($this->validateUser() == TRUE)
			{
				// the authentication was correct, which means the user can log in, we need to create the token,
				// and hand it back to the developer.
				$this->createToken();
			}
			else
			{
				// credentials were NOT correct, they need to try again.
				$this->reportResult(406);
			}
		}
	}
	
	// creates the token, and prints it back to the screen.
	private function createToken()
	{
		// the hash is a mix of the developer id, the username, the login server and the date, it will give
		// a unique value that can be input into the database for later use.
		$hash = md5($this->DevArray['id'] . $this->Data['username'] . "this is my secret" . time());		
				
		// If the developer gives us the remember option and it is yes, then we need to add a sticky session for this token. 
		// TODO: Either make the developer in charge of the duration of the hash or assign it to the developer account within the database.
		if(isset($this->Data['remember']))
		{
			$sticky = 1;
		}
		else
		{
			$sticky = 0;
		}
		
		// The following will check the table, if the user was disconnected or the server crashed their token is still on the server and we need to delete before we can 
		// setup a new one.
		$query = "SELECT `id` FROM `" . $this->MainDB . "`.`" . $this->TokenTable . "` WHERE `did` = '" . $this->DevArray['id'] . "' AND `uid` = '" . $this->UserID . "'";
		$results = $this->mysqli->query($query); // do the query
		if(!$results)
		{
			// there was an error running the query, no idea what, but the user needs to know what.
			$this->reportResult(500);
		}
		else
		{ 
			$count = mysqli_num_rows($results); // count the rows
			if($rows > 0)
			{	
				// There were rows found, we need to remove them all.
				$results = $this->mysqli->query("DELETE FROM `" . $this->MainDB . "`.`" . $this->TokenTable . "` WHERE `did` = '" . $this->DevArray['id'] . "' AND `uid` = '" . $this->UserID . "'");
				if(!$results)
				{
					// there was an error running the query, no idea what, but the user needs to know what.
					$this->reportResult(500);
				}
			}
			else
			{
				// Nothing was found, so nothing to do here.
			}
			$query = "INSERT INTO `" . $this->MainDB . "`.`" . $this->TokenTable . "` (`id`, `session_hash`, `date`, `did`, `uid`, `sticky`, `ip`) VALUES (NULL, '" . $hash . "', '" . time() . "', '" . $this->DevArray['id'] . "', '" . $this->UserID . "', '" . $sticky . "', '" . $_SERVER['REMOTE_ADDR'] . "')";
			$results = $this->mysqli->query($query);
			if(!$results)
			{
				// there was an error running the query, no idea what, but the user needs to know what.
				$this->reportResult(500);
			}
			else
			{
				// After outputting the hash to the client, we need to log them in to the system (if their server is included in the function
				$this->reportResult(200,$hash);
			}
		}
	}
	
	// validates the user so as to verify that the login credentials are indeed correct.
	private function validateUser()
	{
		// the following were needed for portability to the config class..
		$this->username = $this->Data['username'];
		$this->password = $this->Data['password'];
		
		// We need to validate the user, see if it works for us.
		$UserValidation = $this->array_validateAPIUser($this->Data['username'],$this->Data['password']);
		
		// validate the user logins given to us.
		if($UserValidation[0] == TRUE)
		{
			$this->UserID = $UserValidation[1]; // we need to make the userid be a global for later usage..
			return TRUE;
		}
		else
		{
			// no users turned up anywhere.
			return FALSE;
		}
	}
	
	// after the user has been authenticated via the token. We need to parse through the available options
	// that the dev could push through the app, this function jumps to all of those.
	private function launchAPISubFunctions()
	{
		$this->RecordDevLogs();
		if(isset($this->Data['action']) && $this->Data['action'] == 'result-codes')
		{
			$this->formatData($this->MessageCodes);
		}
		else if(isset($this->Data['action']) && $this->Data['action'] == 'available-actions')
		{
			//$this->formatData($this->APIActions);
			//print_r($this->APIActions);
			foreach($this->APIActions AS $Action)
			{
				if($Action['disabled'] == 0)
				{
					$array = array();
					$array[$Action['action']]['action'] = $Action['action'];
					$array[$Action['action']]['description'] = $Action['description'];
					print_r($array);
					unset($array);
				}
			}
		}
		else if(isset($this->Data['action']) && $this->Data['action'] == $this->APIActions[$this->Data['action']]["action"])
		{
			include("includes/classes/" . $this->APIActions[$this->Data['action']]["location"]);
			
			$C = new $this->APIActions[$this->Data['action']]["classname"]($this->Data,$this->UserID,$this->DevArray,$this->AccessLevel);
			$Method = $this->APIActions[$this->Data['action']]["method"];
			$this->formatData($C->$Method());
		}
		else
		{
			// The default action/no action will check the token and give a simple response back stating that it is still active.
			$this->reportResult(300);
		}
	}
	
	// record the functions accessed as well as the details of the request
	private function RecordDevLogs()
	{
		$query = "INSERT INTO developers_logs (`date`, `did`, `uid`, `agent`, `ip`, `url`)
VALUES ('".time()."', '" . $this->DevArray['id'] . "', '" . $this->UserID . "', '" . $_SERVER['HTTP_USER_AGENT'] . "', '" . $_SERVER['REMOTE_ADDR'] . "', '" . $_SERVER['REQUEST_URI'] . "')";
		$this->mysqli->query($query);
		$query = 'UPDATE users SET lastActivity=\''.time().'\' WHERE ID=\'' . $this->UserID . '\'';
		$this->mysqli->query($query);
	}
}