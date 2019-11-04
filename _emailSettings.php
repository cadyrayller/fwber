<?php 

	session_start();

	require_once("_init.php");
	require_once("_profileVars.php");
	require_once("_secrets.php");
	require_once("_globals.php");
	
	//first make sure we are a legit user.
	if(deleteCookiesIfInvalid()==false){header('Location: '.getSiteURL());return;}//full auth for actions
	
	goHomeIfCookieNotSet();

	//make sure we've got an action.
	if(!isset($_GET['emailMatches'])||empty($_GET['emailMatches']))exit('no emailMatches');
	if(!isset($_GET['emailInterested'])||empty($_GET['emailInterested']))exit('no emailInterested');
	if(!isset($_GET['emailApproved'])||empty($_GET['emailApproved']))exit('no emailApproved');

	$db = mysqli_connect($dburl,$dbuser,$dbpass);
	if(!$db)exit(mysqli_connect_error());
	
	$email = mysqli_escape_string($db,$_SESSION["email"]);	
	
	$emailMatches=0;
	$emailInterested=0;	
	$emailApproved=0;	
	
	if($_GET['emailMatches']=="true")$emailMatches=1;
	if($_GET['emailInterested']=="true")$emailInterested=1;
	if($_GET['emailApproved']=="true")$emailApproved=1;

	$dbquerystring = 
	sprintf("UPDATE ".$dbname.".users SET emailMatches = '%s',emailInterested = '%s',emailApproved = '%s' WHERE email='%s'",
	$emailMatches,
	$emailInterested,
	$emailApproved,
	$email
	);
	if(!mysqli_query($db,$dbquerystring))exit("didn't work");
	
	//done
	mysqli_close($db);

	//send back done
	echo "done";
