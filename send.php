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

// returns false if not enough info was entered 
$success = true;

if ($_POST['send'])
{
    // To, Subject, Message and From must be filled in
    if (empty($_POST['to']) || empty($_POST['subject']) 
            || empty($_POST['message']) || empty($_POST['from']))
    {
        $success = false;
    }
    else
    {
        $to         = $_POST['to'];
        $subject    = $_POST['subject'];
        $message    = $_POST['message'];
        $from       = $_POST['from'];
        $headers    = "From: $from";
        
        // wraps the message, since a line can't be more than 70 characters in php
        $message    = wordwrap($message, 70);
        
        // If cc is entered
        if (!empty($_POST['cc']))
        {
            $cc = $_POST['cc'];
            $headers = $headers . "\r\nCc: " . $cc;
        }
        // if bcc is entered
        if (!empty($_POST['bcc']))
        {
            $bcc = $_POST['bcc'];
            $headers = $headers . "\r\nBcc: " . $bcc;
        }
        
        mail($to, $subject, $message, $headers);
        
    }    
}
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <title>Send</title>
    </head>
    <body>
        <h1>
            Send E-mail
        </h1>
        <form method="post">
            <table>
                <tr>
                    <td>
                        Til: 
                    </td>
                    <td>
                        <input type="text" placeholder="Eksempel@email.dk" 
                               name="to" size="40">
                    </td>
                    <?php
                    if (!$success)
                    {
                        echo
                        "<td>"
                    .       " *"
                    .   "</td>";
                    }
                    ?>
                </tr>
                <tr>
                    <td>
                        Cc: 
                    </td>
                    <td>
                        <input type="text" name="cc" size="40">
                    </td>
                </tr>
                <tr>
                    <td>
                        Bcc: 
                    </td>
                    <td>
                        <input type="text" name="bcc" size="40">
                    </td>
                </tr>
                <tr>
                    <td>
                        Emne: 
                    </td>
                    <td>
                        <input type="text" placeholder="Udfyld emne" 
                               name="subject" size="40">
                    </td>
                    <?php
                    if (!$success)
                    {
                        echo
                        "<td>"
                    .       " *"
                    .   "</td>";
                    }
                    ?>
                </tr>
                <tr>
                    <td>
                        Fra: 
                    </td>
                    <td>
                        <input type="text" placeholder="Din-email@email.dk" 
                               name="from" size="40"><br>
                    </td>
                    <?php
                    if (!$success)
                    {
                        echo
                        "<td>"
                    .       " *"
                    .   "</td>";
                    }
                    ?>
                </tr>
            </table><br>
            <textarea rows="20" cols="50" name="message" 
                        maxlength="4000"></textarea>
            <?php
            if (!$success)
            {
                echo
                " *";
            }
            ?>
            <br><br>
            <input type="button" onclick="window.location='user.php'"
                    value="Tilbage">
            <input type="submit" name="send" value="Send" 
                   onclick="return confirm(
                               'Er du sikker på at du vil sende denne e-mail?')">
            <br><br>
            <?php
            if (!$success)
            {
                echo
                "Felter markeret med *, skal udfyldes";
            }
            ?>
        </form>
    </body>
</html>