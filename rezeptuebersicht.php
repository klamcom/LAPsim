<?php
require("includes/common.inc.php");
require("includes/config.inc.php");
require("includes/conn.inc.php");

$vornamefilter = "";
$nachnamefilter = "";

if(count($_POST)>0) {
    $vornamefilter = $_POST["VN"] ?? "";
    $nachnamefilter = $_POST["NN"] ?? "";
}

?>
<!doctype html>
<html lang="de">
    <head>
        <title>Rezepte</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="css/common.css">
    </head>
    <body>
        <nav>
            <ul>
                <li><a href="index.html">Startseite</a></li>
                <li><a href="rezeptdarstellung.php">Rezeptdarstellung</a></li>
                <li><a href="rezeptuebersicht.php">Rezeptübersicht je User</a></li>
                <li><a href="zutatenundrezepte.php">Zutaten und Rezepte</a></li>
            </ul>
        </nav>
        <main>
            <h1>Rezeptübersicht je User</h1>
            <form method="post">
            <label>
                Vorname:
                <input type="text" name="VN" placeholder="Vorname" value="<?php echo $vornamefilter; ?>">
            </label>
            <label>
                Nachname:
                <input type="text" name="NN" placeholder="Nachname" value="<?php echo $nachnamefilter; ?>">
            </label>
            <input type="submit" value="filter">    
            </form>

        <?php
        $where ="";
        $sql = "
                SELECT
                    tbl_rezepte.Titel,
                    tbl_rezepte.Beschreibung,
                    tbl_user.Vorname,
                    tbl_user.Nachname,
                    tbl_user.Emailadresse
                FROM tbl_rezepte
                INNER JOIN tbl_user ON tbl_user.IDUser=tbl_rezepte.FIDUser
                
        ";


        
        $arr = [];
        if(count($_POST)>0) {
            if(strlen($_POST["VN"])>0) {
                $arr[] = "tbl_user.Vorname LIKE '%" . $_POST["VN"] ."%'";
            }
            if(strlen($_POST["NN"])>0) {
                $arr[] = "tbl_user.Nachname LIKE '%" . $_POST["NN"] . "%'";
            }
            if(count($arr)>0) {
                $sql .= "
                WHERE(" . implode(" AND ",$arr) . ")
                ";
            }

            $sql .= "ORDER BY tbl_user.Nachname ASC, tbl_user.Vorname ASC";
            
    
        }
        echo('<ul>');
        $rezepte = $conn->query($sql) or die("Fehler in der Query: " . $conn->error . "<br>" . $sql);
        while($rezept = $rezepte->fetch_object()) {
            echo('
                <li> ' . $rezept->Vorname . ' ' . $rezept->Nachname . ' (' . $rezept->Emailadresse . '):<br>
                ' . $rezept->Titel . ': ' . $rezept->Beschreibung .  '</li>
            ');
        }
        echo('</ul>');
       
        ?>
        </main>

    </body>
</html>