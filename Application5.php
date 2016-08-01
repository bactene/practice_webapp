<?php

header('Refresh: 5; URL=http://'.$_SERVER['SERVER_NAME'].'/apptest.php');

$TitleText="Application5";

require_once("/var/www/include/apptest.inc.php");

if(check_login())
{
	echo "$HeaderStart
<style>
p {padding-left:10px}
</style>
</head>

$BodyStart

<h3 style=\"text-align:center\">Welcome to Application5.</h3>

<p>You will be redirected to the home page in 5 seconds...</p>

$FooterText

";
}
else
{
	login($Redirect);
}

echo "</div>
</body>

</html>
";
?>