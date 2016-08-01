<?php
header('Refresh: 2; URL=http://'.$_SERVER['SERVER_NAME'].'/apptest.php');

$TitleText="Application Test Page";

require_once("/var/www/include/apptest.inc.php");

$UserLogin=trim($_POST['Username']);
$SubmittedPassword=trim($_POST['Password']);

$_SESSION['User']="";

if(isset($_POST['Redirect']))
{
	$Redirect=htmlspecialchars(trim($_POST['Redirect']));
}
else
{
	$Redirect=htmlspecialchars(trim($_SERVER['REQUEST_URI']));
}

if($_POST['Logout'])
{
	if(refresh_cookie())
	{
		if(setcookie("BRCtestapp", "", time() - 3600))
		{
			echo "Logout successful, redirecting...";
		}
		else
		{
			echo "Sorry, having trouble with the logout";
		}
	}
}
else
{
	try {
		$dbh = new PDO('mysql:host=localhost;dbname=webapp', $guestuser, $guestpassword);
		
		$stmt = $dbh->prepare("SELECT * FROM User WHERE login=?");
		
		$stmt->bindParam(1, $UserLogin, PDO::PARAM_STR, 12);
		
		if ($stmt->execute())
		{
			$result = $stmt->fetchAll();
			if(isset($result) && !empty($result))
			{
				foreach($result as $row)
				{
					if(isset($row['Password']) && $row['Password']==$SubmittedPassword)
					{
						if(refresh_cookie())
						{
							if(isset($UserLogin))
							{
								$_SESSION['User']=$UserLogin;
							}
							echo "Successfully logged in, redirecting...";
						}
						else
						{
							echo "Failed to set the cookie, please try again...";
						}
					}
					else
					{
						echo "Incorrect user name/password, please try again...";
					}
				}
			}
			else
			{
				echo "Incorrect user name/password, please try again...";
			}
		}
		else
		{
			echo "Login failure, please try again...";
		}
		
		$dbh=null;
	}
	catch (PDOException $e) {
		print "Error!: " . $e->getMessage() . "<br/>";
		die();
	}
}

?>