<?php

$TitleText="Application Test Page";

require_once("/var/www/include/apptest.inc.php");

if(check_login())
{
	echo "$HeaderStart

<style>
p {padding-left:10px}
</style>

</head>

$BodyStart

<p>Try the <a href=\"apptest.php\">application test page</a></p>



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
