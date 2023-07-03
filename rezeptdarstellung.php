<?php
require("includes/common.inc.php");
require("includes/config.inc.php");
require("includes/conn.inc.php");

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
                <h1>Rezeptdarstellung</h1>

        <?php
        $sql = "
                SELECT
                    tbl_rezepte.IDRezept,
            	    tbl_rezepte.Titel AS rTitel,
                    tbl_rezepte.Beschreibung,
                    tbl_rezepte.DauerVorbereitung,
                    tbl_rezepte.DauerZubereitung,
                    tbl_rezepte.DauerRuhen,
                    tbl_rezepte.AnzahlPersonen,
                    tbl_user.Vorname,
                    tbl_user.Nachname,
                    tbl_schwierigkeitsgrade.Titel AS sTitel,
                    tbl_schwierigkeitsgrade.Beschreibung AS sBeschreibung
                    FROM tbl_rezepte
                    INNER JOIN tbl_user ON tbl_user.IDUser=tbl_rezepte.FIDUser
                    INNER JOIN tbl_schwierigkeitsgrade ON tbl_schwierigkeitsgrade.IDSchwierigkeitsgrad=tbl_rezepte.FIDSchwierigkeitsgrad
                    ORDER BY tbl_rezepte.Titel ASC
        ";
        $rezepte = $conn->query($sql) or die("Fehler in der Query: " . $conn->error . "<br>" . $sql);

        
        while($rezept = $rezepte->fetch_object()) {
            echo('<article>
                    <section>
                        <h3>' . $rezept->rTitel . '</h3>
                        <p>(von ' . $rezept->Vorname . ' ' . $rezept->Nachname . ')</p>
                        <p>Zeiten:</p>
                            <ul>
                                <li>Vorbereitungszeit: ' . $rezept->DauerVorbereitung . ' Min.</li>
                                <li>Zubereitungszeit: ' . $rezept->DauerZubereitung . ' Min.</li>
                                <li>Nachbereitungs- oder Ruhezeit: ' . $rezept->DauerRuhen . ' Min.</li>
                            </ul>
                        <p>Für ' . $rezept->AnzahlPersonen . ' Personen</p>
                        <p>Schwierigkeitsgrad: ' . $rezept->sTitel . ' - ' . $rezept->sBeschreibung . '</p>
                    </section>
                    <section>
                        <h4>Zutaten:</h4>
                        <ul>
            ');
                $sql = "
                    SELECT
                        tbl_rezepte_zutaten.Anzahl,
                        tbl_einheiten.Bezeichnung AS einheitenBEZ,
                        tbl_zutaten.Bezeichnung AS zutatenBEZ
                    FROM tbl_rezepte_zutaten
                    LEFT JOIN tbl_einheiten ON tbl_einheiten.IDEinheit=tbl_rezepte_zutaten.FIDEinheit
                    INNER JOIN tbl_zutaten ON tbl_zutaten.IDZutat=tbl_rezepte_zutaten.FIDZutat
                    WHERE (
                        tbl_rezepte_zutaten.FIDRezept=" . $rezept->IDRezept . "
                        )
                    ";
                    $zutaten = $conn->query($sql) or die("Fehler in der Query: " . $conn->error . "<br>" . $sql);
            
                    while($zutat = $zutaten->fetch_object()) {
                        echo('
                            <li>' . $zutat->Anzahl . ' ' . $zutat->einheitenBEZ . ' ' . $zutat->zutatenBEZ . '</li>
                        ');      
                    }

                    echo('</ul>
                    </section>
                    <section>
                    <h4>Zubereitungsschritte:</h4>
                    <ol>             
                    ');
            
                $sql = "
                    SELECT
                        *
                    FROM tbl_zubereitungsschritte
                    WHERE (
                        tbl_zubereitungsschritte.FIDRezept=" . $rezept->IDRezept . "
                        )
                    ORDER BY tbl_zubereitungsschritte.Reihenfolge ASC
                ";

                $schritte = $conn->query($sql) or die("Fehler in der Query: " . $conn->error . "<br>" . $sql);
                while($schritt = $schritte->fetch_object()) {
                    echo('
                        <li>' . $schritt->Titel . ' ' . $schritt->Beschreibung . '</li>
                    
                    ');
                }

                echo('</ol>
                </section>
                ');



            echo('</article>');
        
        }
        ?>
        </main>

    </body>
</html>