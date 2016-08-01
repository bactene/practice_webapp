<?php

require_once("/var/www/include/apptest.inc.php");

$user=$_SESSION['User'];

$AppName=trim($_POST['AppName']);
$Status=trim($_POST['AppStatus']);

if(isset($ApplicationArray[$AppName]['Description']) && ($Status=="Yes" || $Status=="No")) // tests whether $AppName and $Status are valid submissions
{
	$mysqli = new mysqli("localhost", "$secure_user", "$secure_password", "webapp");
	
	/* check connection */
	if (mysqli_connect_errno()) {
		printf("MySQL connection failed: %s\n", mysqli_connect_error());
		exit();
	}
	
	//if the user has already set their preference, update the entry, otherwise insert
	if(isset($ApplicationArray[$AppName]['UserStatus']))
	{
		$update="UPDATE UserApps SET UserStatus='$Status' WHERE login='$user' AND Name='$AppName'";
		
		if($mysqli->query($update)===true)
		{
			echo "update for $user";
		}
		else
		{
			echo "update failed for $user";
		}
	}
	else
	{
		$insert="INSERT INTO UserApps (login, Name, UserStatus) VALUES ('$user','$AppName','$Status')";
		
		if(mysqli_query($mysqli, $insert)===true)
		{
			echo "insert for $user";
		}
		else
		{
			echo "insert failed for $user";
		}
	}
	
	/* close connection */
	$mysqli->close();
}
else
{
	echo "INVALID entry";
}


?>