<?php

session_start();

$user=$_SESSION['User'];

function refresh_cookie()
{
	global $salt;
	
	$ClientIP=trim($_SERVER['REMOTE_ADDR']);
	$CurrentTime=time();
	$_SESSION['TimeStamp']=$CurrentTime;

	$CookieName="BRCtestapp";
	$CookieValue=md5($salt.$ClientIP.$CurrentTime);
	$CookieExpire=time() + 900; //15 minutes
	$CookiePath="";
	$CookieDomain=$_SERVER['SERVER_NAME'];
	$CookieSecure=false;
	$CookieHTTP=true;
	
	if(setcookie($CookieName, $CookieValue, $CookieExpire, $CookiePath, $CookieDomain, $CookieSecure, $CookieHTTP))
	{
		return true;
	}
	else
	{
		echo "Cookie not set";
		return false;
	}
}

function check_login()
{
	global $salt;
	
	$CookieValue=$_COOKIE['BRCtestapp'];
	
	$ClientIP=trim($_SERVER['REMOTE_ADDR']);
	$SessionTimeStamp=$_SESSION['TimeStamp'];
	$CurrentValue=md5($salt.$ClientIP.$SessionTimeStamp);
	
	if($CurrentValue==$CookieValue)
	{
		if(refresh_cookie())
		{
			return true;
		}
		else
		{
			echo "did not refresh";
			return false;
		}
	}
	else
	{
		$_SESSION['User']="";
		return false;
	}
}


//hide the passwords out of the web directory
$passfile=file_get_contents("/home/www-data/sec_info");
$passlines=explode("\n", $passfile);
if(!empty($passlines))
{
	foreach($passlines as $templine)
	{
		$tempArray=explode("=>", $templine);
		switch($tempArray[0])
		{
			case "sec_user":
				$secure_user="sec_user";
				$secure_password=$tempArray[1];
				break;
			case "guest":
				$guestuser="guest";
				$guestpassword=$tempArray[1];
				break;
			case "salt";
				$salt=$tempArray[1];
				break;
		}
	}
}

//set up an array of the application data thats held in the mysql tables:
$ApplicationArray=array();

$mysqli = new mysqli("localhost", "$guestuser", "$guestpassword", "webapp");
	
/* check the mysqli connection */
if (mysqli_connect_errno())
{
	printf("Connect failed: %s\n", mysqli_connect_error());
	exit();
}

$query = "SELECT * FROM Application";
	
if ($result = $mysqli->query($query))
{
	// fetch an associative array:
	while ($row = $result->fetch_assoc())
	{
		$ApplicationArray[$row['Name']]['Description']=$row['Description'];
		$ApplicationArray[$row['Name']]['Color']=$row['Color'];
		$ApplicationArray[$row['Name']]['DefaultStatus']=$row['DefaultStatus'];
	}
	$result->close();
}

$query = "SELECT Name, UserStatus FROM UserApps WHERE login='$user'";

if ($result = $mysqli->query($query))
{
	// fetch an associative array:
	while ($row = $result->fetch_assoc())
	{
		$ApplicationArray[$row['Name']]['UserStatus']=$row['UserStatus'];
	}
	$result->close();
}

// prepare html sections of the page:
$HeaderStart="<!DOCTYPE html>
<html>

<head>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />

<link rel=\"stylesheet\" type=\"text/css\" href=\"apptest.css\">
<link rel=\"stylesheet\" type=\"text/css\" href=\"jquery-ui.css\">

<script src=\"jquery.js\"></script>
<script src=\"jquery-ui.js\"></script>
<script type=\"text/javascript\">

function CloseBox(id)
{
	document.getElementById(id).style.display=\"none\";
	
	document.getElementById(\"AppName\").value=id;
	document.getElementById(\"AppStatus\").value=\"No\";
	document.getElementById(\"SubmitChangeForm\").submit();
}

function AddBox(id)
{
	document.getElementById(id).style.display=\"block\";
	document.getElementById(\"SelectApplication\").value=\"Default\";
	
	$.post( \"UpdateStatus.php\", { AppName: id, AppStatus: \"Yes\" } );
}

function OpenApp(app)
{
	var url=app + \".php\";
	window.open(url, \"_self\");
}

function Logout()
{
	document.getElementById(\"LogoutForm\").submit();
}

// The jQuery portion of this script is derived from sortable portlets:
// https://jqueryui.com/sortable/#portlets

$( function() {
	$( \".column\" ).sortable({
		connectWith: \".column\",
		handle: \".portlet-header\",
		cancel: \".portlet-toggle\",
		placeholder: \"portlet-placeholder ui-corner-all\"});
	
	$( \".portlet\" )
		.addClass( \"ui-widget ui-widget-content ui-helper-clearfix ui-corner-all\" )
		.find( \".portlet-header\" )
		.addClass( \"ui-widget-header ui-corner-all\" )
		.prepend( \"<span class='ui-icon ui-icon-minusthick portlet-toggle'></span><span class='ui-icon ui-icon-closethick portlet-close'></span>\");
	
	$( \".portlet-toggle\" ).on( \"click\", function() {
		var icon = $( this );
		icon.toggleClass( \"ui-icon-minusthick ui-icon-plusthick\" );
		icon.closest( \".portlet\" ).find( \".portlet-content\" ).toggle();});
		
	$( \".portlet-close\" ).on( \"click\", function() {
		var icon = $( this );
		var id = icon.closest( \".portlet\" ).attr(\"id\");
		document.getElementById(id).style.display=\"none\";
		
		$.post( \"UpdateStatus.php\", { AppName: id, AppStatus: \"No\" });});
} );

</script>

<style>
body {
	min-width: 520px;}
.column {
	width: 200px;
	float: left;
	padding-bottom: 100px;}
.portlet {
	margin: 0 1em 1em 0;
	padding: 0.3em;}
.portlet-header {
	height:25px;
	padding: 0.2em 0.3em;
	margin-bottom: 0.5em;
	position: relative;
	cursor: grab}
.portlet-toggle {
	position: absolute;
	top: 50%;
	left: 10px;
	margin-top: -8px;
	cursor: pointer}
.portlet-close {
	position: absolute;
	top: 50%;
	right: 10px;
	margin-top: -8px;
	cursor: pointer}
.portlet-content {
	padding: 0.4em;}
.portlet-placeholder {
	border: 1px dotted black;
	margin: 0 1em 1em 0;
	height: 50px;}

.jqCloseBox {display:inline-block; position:relative; float:right; padding-right:10px; opacity:0.3; cursor:pointer}
.jqDescriptionBox {display:block; height:95px; text-align:center; padding-top:10px; border:2px solid black; border-style:outset; cursor:pointer}

#MainSection {margin:25px; padding:25px;}

</style>

<title>$TitleText</title>
";

if(isset($_COOKIE['BRCtestapp']) && !empty($_COOKIE['BRCtestapp']))
{
	$LoginText="logout";
}
else
{
	$LoginText="login"; // this situation should never occur
}

$BodyStart="<body id=\"PageBody\">
<div id=\"Container\">
	<div id=\"Header\">
		<h1>jQuery-Enhanced Test Page</h1>
		<div id=\"LoginDiv\" onmouseover=\"this.style.borderStyle='inset'\" onmouseout=\"this.style.borderStyle='outset'\" onclick=\"Logout()\">
		$LoginText
		</div>
	</div>
";

 
$FooterText="	
	<div id=\"Footer\">
	</div>
	
	<form id=\"LogoutForm\" action=\"login.php\" method=\"post\">
	<input type=\"hidden\" name=\"Logout\" value=\"true\" />
	</form>
";


function login($Redirect)
{
	echo "<html>
<head>
<style>
#MainDiv {width:250px; margin:25px; padding:25px; border:thin solid black; background-color:#ffffb3}
</style>
</head>
<body>
<div id=\"MainDiv\">
<p>You are not currently logged in:</p>
<form action=\"login.php\" method=\"post\">
<input type=\"hidden\" name=\"Redirect\" value=\"$Redirect\" />
<p>Username: <input type=\"text\" name=\"Username\" /></p>
<p>Password: <input type=\"password\" name=\"Password\" /></p>
<p><input type=\"submit\" value=\"Submit\" /></p>
</form>
";
}

?>
