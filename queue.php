<?php
require("config.php");

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

if ($_POST['back'])
{
    header("Location:user.php");
    die();
}

$userData = $dbh->prepare("SELECT * FROM users WHERE email='$username'");
$userData->execute();
$parentData = $userData->fetch(PDO::FETCH_NUM);
$dbSalt     = $parentData[6];

// Check if the salt stored and the database matches the salt of the session.
// If it doesn't, redirect the user to logout.php
if ($salt != $dbSalt)
{
    die("Du er desv&aeligrre blevet logget ud, "
            . "<a href='logout.php'>log ind</a> igen");
}

// If a child has been chosen, retrieve the data
// for the child
if ($_POST['childCPR'])
{
    $cpr = $_POST['childCPR'];
    
    // Fetch the data for the child picked
    $childData = $dbh->prepare("SELECT * FROM children WHERE cpr='$cpr'");
    $childData->execute();
    $cData = $userData->fetch(PDO::FETCH_NUM);
    $cFirstName = $cData[1];
    $cLastName = $cData[2];
    $grade = $cData[3];
    $queueNumber = $cData[4];
}
?>

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <title>Venteliste</title>
    </head>
    <body>
        <h1>
            Venteliste for brugeren: <?php print $firstName . " " . $lastName; ?>
        </h1>
        <form method='post'>
            <table width='300'>
                <tr>
                    <td>
                        <b>
                        Se venteliste for:
                        </b>
                    </td>
                    <td>
                        <select name='childCPR' onchange="this.form.submit()">
                            <option value=NULL>---Vælg et barn---</option>
                            <?php
                            //Creates a dropdown menu with children
                            foreach($dbh->query("SELECT DISTINCT "
                            . "children.cpr, firstname, lastname "
                            . "FROM has, children WHERE email='123@hotmail.com' "
                            . "AND has.cpr = children.cpr "
                                    . "ORDER BY firstName") as $row)
                            {
                                $cpr = $row[0];
                                $cName = $row[1] . " " . $row[2];
                                print "<option value='$cpr'>$cName</option>";
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <?php
                    // If a child has been selected, retrieve the data
                    // for the child
                    if ($_POST['childCPR'])
                    {
                        $cpr = $_POST['childCPR'];
                        // Shows the selected value
                        $childData = $dbh->prepare("SELECT * FROM children "
                                . "WHERE cpr='$cpr'");
                        $childData->execute();
                        $cData = $childData->fetch(PDO::FETCH_NUM);
                        $cFirstName = $cData[1];
                        $cLastName = $cData[2];
                        $grade = $cData[3];
                        $queueNumber = $cData[4];
                        $siblings = $cData[5];
                // Execute the following html if a child is selected
                ?>
                <tr>
                    <td>
                        </br>

                        Navn:
                    </td>
                    <td>
                        </br>
                        <b>
                            <?php print $cFirstName . " " . $cLastName; ?>
                        </b>
                    </td>
                </tr>
                <tr>
                    <td>
                        Nummer i kø:
                    </td>
                    <td>
                        <b>
                            <?php print $queueNumber; ?>
                        </b>
                    </td>
                </tr>
                <tr>
                    <td>
                        Skal starte i:
                    </td>
                    <td>
                        <b>
                            <?php
                                if ($grade == 'bhkl')
                                {
                                    print "børnehaveklasse";
                                }
                                else
                                {
                                    print $grade;
                                }
                            ?>
                        </b>
                    </td>
                </tr>
                <tr>
                    <td>
                        Søskende:
                    </td>
                    <td>
                        <b>
                            <?php
                            if ($siblings == 'y')
                            {
                                print 'Ja';
                            }
                            else
                            {
                                print 'Nej';
                            }
                            ?>
                        </b>
                    </td>
                </tr>
                <?php
                    }
                ?>
                <tr>
                    <td>
                        <input type='submit' name='back' value="Tilbage">
                    </td>
                </tr>
            </table>
        </form>
    </body>
</html>