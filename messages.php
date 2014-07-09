<?php
require("config.php");
// Check if the user was logged in or redirect to login page
if ($_SESSION['username'] && $_SESSION['salt'])
{
    $username = $_SESSION['username'];
    $salt     = $_SESSION['salt'];
}
else
{
    die("Du har ikke adgang til denne side "
            . "<a href='logout.php'>log ind</a> f&oslashrst");
}
// get the userdata
$userData = $dbh->prepare("SELECT * FROM users WHERE email='$username'");
$userData->execute();
$data = $userData->fetch(PDO::FETCH_NUM);
$firstName  = $data[2];
$lastName   = $data[3];
$isAdmin    = $data[5];
$dbSalt     = $data[6];

// Check if the salt stored and the database matches the salt of the session.
// If it doesn't, redirect the user to logout.php
if ($salt != $dbSalt)
{
    die("Du er desv&aeligrre blevet logget ud, "
            . "<a href='logout.php'>log ind</a> igen");
}
if ($isAdmin != 'y')
{
    die("Du har ikke adgang til denne side, "
            . "<a href='logout.php'>Log ind</a> f&oslashrst");
}
if ($_POST['newMessage'])
{
    $newMessage = nl2br($_POST['newMessage']);
    print $newMessage;
}
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <title>Nyheder</title>
    </head>
    <body>
        <h1>
            Skriv ny nyhed
        </h1>
        <form method='post'>
            (max 4000 tegn)
            <br>
            <textarea name="newMessage" rows="10" cols ='40' maxlength='4000'></textarea>
            <br>
            <input type='button' onclick="window.location='messages.php'" value='annullér'>
            <input type='submit' name='text' value='udsend nyhed'>
        </form>
    </body>
</html>