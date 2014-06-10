<?php
require("config.php");
// If user has already logged in
if ($_SESSION['username'])
{
    header("Location:user.php");
}
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <title>Ansøgning til venteliste</title>
    </head>
    <body>
        <h1>
            Ansøgning om plads på ventelisten
        </h1>
        </br>
        Udfyldes efter spørgeskema fra skolen
    </body>
</html>