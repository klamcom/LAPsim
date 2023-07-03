<?php
require("includes/common.inc.php");
require("includes/config.inc.php");
require("includes/conn.inc.php");

$filter = "";
if (count($_POST) > 0) {
	if (isset($_POST["zutatenSelect"])) {
		if ($_POST["zutatenSelect"] > 0) {
			$filter = "
                    WHERE (
                        tbl_rezepte_zutaten.FIDZutat=" . $_POST["zutatenSelect"] . "
                        )
            ";    
        }

	} 
}
ta($_POST);
ta($filter);
?>
<!doctype html>
<html lang="de">
    <head>
        <title>Zutaten und Rezepte</title>
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
        <form method="POST">
        <label for="zutatenSelect">Zutat:</label>
        

        <select name="zutatenSelect">
            <?php
            $sql = "
            SELECT
                *
            FROM tbl_zutaten
        ";
            $zutaten = $conn->query($sql) or die("Fehler in der Query: " . $conn->error . "<br>" . $sql);
                echo('<option value="0" selected>Bitte wählen:</option>');
                while($zutat = $zutaten->fetch_object()) {
                    echo('<option value="' . $zutat->IDZutat . '">' . $zutat->Bezeichnung . '</option>');

                }
                

            ?>
        </select>
        </label>
        <input type="submit" name="filtern" value="filtern">
        </form>
        <?php
        $sql = "
                SELECT
                    tbl_rezepte.Titel,
                    tbl_rezepte.AnzahlPersonen,
                    tbl_rezepte.Beschreibung,
                    tbl_user.Vorname,
                    tbl_user.Nachname
                FROM tbl_rezepte_zutaten
                INNER JOIN tbl_rezepte ON tbl_rezepte.IDRezept= tbl_rezepte_zutaten.FIDRezept
                INNER JOIN tbl_user ON tbl_user.IDUser=tbl_rezepte.FIDUser
                " . $filter . "
                GROUP BY tbl_rezepte.IDRezept
                ORDER BY tbl_rezepte.Titel ASC    
        ";
        ta($sql);
        $rezepte = $conn->query($sql) or die("Fehler in der Query: " . $conn->error . "<br>" . $sql);
        
        echo('<ul>');
        while($rezept = $rezepte->fetch_object()) {
                echo('
                <li>' . $rezept->Titel . ' (von '  . $rezept->Vorname . ' ' . $rezept->Nachname . ', für ' . $rezept->AnzahlPersonen . ' Personen):<br>
                ' . $rezept->Beschreibung . '</li>
            ');
                    
            
        }

        echo('</ul>');

        ?>



        </main>

    </body>
</html>