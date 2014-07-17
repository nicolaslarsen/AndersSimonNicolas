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
    // Get the highest ID from the messages table
    $getMaxId = $dbh->prepare("SELECT MAX(id) FROM Messages");
    $getMaxId->execute();
    $collectId = $getMaxId->fetch(PDO::FETCH_NUM);
    $maxId = $collectId[0];
    $newId = $maxId + 1;
    
    // Insert the new message, the id = the highest id in the table + 1
    $enteredMessage = nl2br($_POST['messageText']);
    $fixMsg1        = str_replace('æ', '&aelig', $enteredMessage);
    $fixMsg2        = str_replace('ø', '&oslash', $fixMsg1);
    $fixMsg3        = str_replace('å', '&aring', $fixMsg2);
    $fixMsg4        = str_replace('Æ', '&AElig', $fixMsg3);
    $fixMsg5        = str_replace('Ø', '&Oslash', $fixMsg4); 
    $newMessage     = str_replace('Å', '&Aring', $fixMsg5);

    if ($newMessage != '')
    {
        $insertMessage = $dbh->prepare("INSERT INTO Messages VALUES('$newMessage', '$newId')");
        $insertMessage->execute();
    }
}
if ($_POST['delete'])
{
    $messages = $_POST['messages'];
    $numberOfMessages = count($messages);
    for ($i = 0; $i < $numberOfMessages; $i++)
    {
         $id = $messages[$i];
         $deleteMessage = $dbh->prepare("DELETE FROM Messages WHERE id='$id'");
         $deleteMessage->execute();
    }
}
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <title>Beskeder</title>
    </head>
    <body>
        <h1>
            Skriv ny besked
        </h1>
        <form method='post'>
            <table style='float: left' width='350'>
                <tr>
                    <td>
                        (max 4000 tegn)
                    </td>
                </tr>
                <tr>
                    <td>
                        <textarea name="messageText" rows="10" 
                                  style="font-family: Arial;font-size: 12pt;"
                                    cols ='38' maxlength='4000'></textarea>
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type='button' onclick="window.location='messages.php'" value='Ryd tekst'> 
                        <input type='submit' name='newMessage' value='Udsend besked'>
                    </td>
                </tr>
                <tr>
                    <td>
                        <br>
                        <input type="button" 
                               onclick="window.location='user.php'" value="Tilbage">
                    </td>
                </tr>
            </table>
            <table border='2' style='float: center; font-family: Arial;font-size: 12pt;' width='310' cellpadding="3">
                <br>
                <th>
                    Gamle beskeder
                </th>
                    <?php
                    foreach($dbh->query("SELECT * FROM Messages "
                            . "ORDER BY id DESC") as $row)
                    {
                        $text = $row[0];
                        $id   = $row[1];
                        echo
                        "<tr>"
                      .     "<td>"
                      .         "<input type='checkbox' name='messages[]' "
                      .             "value='$id'><br>$text</input>"
                      .     "</td>"
                      . "</tr>";
                    }
                    ?>
            </table>
            <br>
            <input type='submit' name='delete' value='Slet valgte beskeder' 
                   onclick="return confirm('Er du sikker på at du vil slette de vagte beskeder?');">
        </form>
        <br>
    </body>
</html>