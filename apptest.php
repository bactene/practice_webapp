<?php
//session_start();

$TitleText="Application Test Page"; // this needs to be declared before the include statement

require_once("/var/www/include/apptest.inc.php");

if(isset($_POST['Redirect']))
{
	$Redirect=htmlspecialchars(trim($_POST['Redirect']));
}
else
{
	$Redirect=htmlspecialchars(trim($_SERVER['REQUEST_URI']));
}


if(check_login())
{
	echo "$HeaderStart

</head>

$BodyStart

";

	if(empty($ApplicationArray))
	{
		echo "Unable to retrieve Application data";
	}
	else
	{
		echo "<div id=\"SelectDiv\"><select id=\"SelectApplication\" onchange=\"AddBox(value)\">
<option value=\"Default\" selected>Add Selected Application</option>
<option disabled>........................</option>
";
		foreach($ApplicationArray as $tempName=>$tempArray)
		{
			echo "<option value=\"$tempName\">".$ApplicationArray[$tempName]['Description']."</option>
";
		}
		echo "</select></div>
";
		
		foreach($ApplicationArray as $tempName=>$tempArray)
		{
			if((isset($ApplicationArray[$tempName]['UserStatus']) && $ApplicationArray[$tempName]['UserStatus']=="Yes") || (!isset($ApplicationArray[$tempName]['UserStatus']) && $ApplicationArray[$tempName]['DefaultStatus']=="Yes"))
			{
				$tempApplication=$ApplicationArray[$tempName]['Description'];
				
				echo "<span id=\"$tempName\" class=\"AppBox\" style=\"background-color:".$ApplicationArray[$tempName]['Color']."\"><span class=\"CloseBox\" onmouseover=\"this.style='opacity:1.0'\" onmouseout=\"this.style='opacity:0.3'\" onclick=\"CloseBox('$tempName')\" >X</span><hr class=\"AdjustRight\"><span class=\"DescriptionBox\" onmouseover=\"this.style.borderStyle='inset'\" onmouseout=\"this.style.borderStyle='outset'\" onclick=\"OpenApp('$tempApplication')\">$tempName<br />($tempApplication)</span></span>
";
			}
			else
			{
				$tempApplication=$ApplicationArray[$tempName]['Description'];
				
				echo "<span id=\"$tempName\" class=\"AppBox\" style=\"display:none; background-color:".$ApplicationArray[$tempName]['Color']."\"><span class=\"CloseBox\" onmouseover=\"this.style='opacity:1.0'\" onmouseout=\"this.style='opacity:0.3'\" onclick=\"CloseBox('$tempName')\" >X</span><hr class=\"AdjustRight\"><span class=\"DescriptionBox\" onmouseover=\"this.style.borderStyle='inset'\" onmouseout=\"this.style.borderStyle='outset'\" onclick=\"OpenApp('$tempApplication')\">$tempName<br />($tempApplication)</span></span>
";
			}
		}
	}

	echo "	<div id=\"Application1Note\">Clicking on \"Application1\" will generate a jQuery-enhanced version of this page.</div>
$FooterText

";
	
	/* close the mysqli connection */
	$mysqli->close();
}
else //user is not logged in, prompt for password
{
	login($Redirect);
}


echo "</div>
</body>

</html>
";

?>
