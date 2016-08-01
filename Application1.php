<?php

$TitleText="Application Test Page - jQuery Enhanced"; // this needs to be declared before the include statement

require_once("/var/www/include/jq_apptest.inc.php");

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

<div id=\"MainSection\">
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
				
				echo "<div class=\"column\">
	<div class=\"portlet\" id=\"$tempName\" style=\"background-color:".$ApplicationArray[$tempName]['Color']."\">
		<div class=\"portlet-header\"></div>
		<div class=\"portlet-content\">
			<span class=\"jqDescriptionBox\" onmouseover=\"this.style.borderStyle='inset'\" onmouseout=\"this.style.borderStyle='outset'\" onclick=\"OpenApp('$tempApplication')\">$tempName<br />($tempApplication)</span>
		</div>
	</div>
</div>
";
			}
			else
			{
				$tempApplication=$ApplicationArray[$tempName]['Description'];
				
				echo "<div class=\"column\">
	<div class=\"portlet\" id=\"$tempName\" style=\"display:none; background-color:".$ApplicationArray[$tempName]['Color']."\">
		<div class=\"portlet-header\"></div>
		<div class=\"portlet-content\">
			<span class=\"jqDescriptionBox\" onmouseover=\"this.style.borderStyle='inset'\" onmouseout=\"this.style.borderStyle='outset'\" onclick=\"OpenApp('$tempApplication')\">$tempName<br />($tempApplication)</span>
		</div>
	</div>
</div>
";
			}
		}
	}

	echo "</div>
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