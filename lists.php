<?php
require("config.php");
// Check if the user was logged in or redirect to the login page
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
$dbPass     = $data[1];
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
?>

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <title>Venteliste</title>
    </head>
    <body>
        <h1>
            Ventelister
        </h1>
        <form method="post">
            <table width="300">
                <tr>
                    <td>
                        Se venteliste for:
                    </td>
                    <td>
                        <select name="grade" onchange="this.form.submit()">
                            <option value=NULL>---Vælg et klassetrin---</option>
                            <option value="bhkl"
                                    <?php 
                                    // If bhkl is selected
                                    if ($_POST['grade'] == bhkl)
                                    {
                                        echo "selected='bhkl'";
                                    }
                                    ?>
                                    >Børnehaveklasse</option>
                        <?php 
                        // Creates a dropdown menu with grades 1-9
                        for ($i=1; $i<10; $i++)
                        {
                            $value      = $i . '_kl';
                            $name       = $i . '. klasse';
                            $selected   = '';
                            // If the grade is selected
                            if ($_POST['grade'] == $value)
                            {
                                $selected = "selected='$value'";
                            }
                            echo "<option value='$value'" . $selected .
                                    ">$name</option>";
                        }
                        ?>
                        </select>
                    </td>
                </tr>
            </table>
        </form>
    </body>
</html>
