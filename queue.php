<?php
require("config.php");

if ($_SESSION['username'])
{
    $username = $_SESSION['username'];
}
else
{
    die("Du har ikke adgang til denne side "
            . "<a href='main.php'>log ind</a> først");
}

if ($_POST['back'])
{
    header("Location:user.php");
    die();
}

$userData = $dbh->prepare("SELECT * FROM users WHERE email='$username'");
$userData->execute();
$pData = $userData->fetch(PDO::FETCH_NUM);
$pFirstName = $parentData[2];
$pLastName  = $parentData[3];
$salt       = $parentData[6];

if ($_SESSION['salt'])
{
    if ($salt = $_SESSION['salt'])
    {
        // Do nothing
    }
    else
    {
        die("Du er desværre blevet logget ud og "
                . "har derfor ikke adgang til denne side, "
                . "<a href='main.php'>log ind</a>");
    }
}

// If a child has been chosen, retrieve the data
// for the child
if ($_POST['childCPR'])
{
    $cpr = $_POST['childCPR'];
    // Shows the selected value
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