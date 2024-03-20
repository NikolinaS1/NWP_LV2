<?php
//Informacije potrebne za uspostavu veze s bazom podataka
//Spajanje se vrši s bazom kreiranom u prvoj laboratorijskoj vježbi
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "radovi";

//Uspostava veze s bazom podataka
//Ako se veza ne uspostavi uspješno, skripta završava izvršavanje i ispisuje poruku o greški
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//Nakon uspostave veze s bazom podataka, poziva se funkcija backup_tables() kako bi se izvršio postupak stvaranja backupa
backup_tables($conn);

//Backup_tables obavlja glavni posao stvaranja backupa baze podataka
//Prvo se dobivaju imena svih tablica u bazi podataka pomoću SQL upita SHOW TABLES.
//Zatim se prolazi kroz svaku tablicu i izvlače se svi retci iz tablice koristeći upit SELECT * FROM table_name
//Svaki redak se pretvara u niz SQL naredbi INSERT INTO koje se dodaju u varijablu $return
//Na kraju, rezultirajući SQL upiti se zapisuju u tekstualnu datoteku $fileName
//Za potrebe sažimanja datoteke koristi se klasa ZipArchive
function backup_tables($conn) {
    $return = '';
    
    //Dohvati sve tablice
    $result = $conn->query('SHOW TABLES');
    while($row = $result->fetch_row()) {
        $tables[] = $row[0];
    }

    //Prolaz kroz tablice
    foreach($tables as $table) {
        $result = $conn->query('SELECT * FROM '.$table);
        $num_fields = $result->field_count;

        //Prolaz kroz rezultate
        while($row = $result->fetch_assoc()) {
            $return .= "INSERT INTO ".$table." (";
            $return .= implode(", ", array_keys($row));
            $return .= ") VALUES ('";
            $return .= implode("', '", array_map('addslashes', $row));
            $return .= "');\n";
        }
        $return .= "\n\n";
    }

    //Spremi datoteku
    $fileName = 'db-backup.txt';
    $handle = fopen($fileName, 'w+');
    fwrite($handle, $return);
    fclose($handle);

    //Sažmi datoteku u .zip format
    //Open otvara novu .zip arhivu, addFile dodaje .txt datoteku u .zip arhivu, a close zatvara .zip arhivu
    //U skladu s uspješnosti ili ne uspješnosti navedenog, ispisuje se odgovarajuća poruka na zaslon
    $zipFileName = 'db-backup.zip';
    $zip = new ZipArchive;
    if ($zip->open($zipFileName, ZipArchive::CREATE) === TRUE) {
        $zip->addFile($fileName, basename($fileName));
        $zip->close();
        echo "Backup je uspješno stvoren i sažet te se nalazi u datoteci: ".$zipFileName;
    } else {
        echo "Pogreška prilikom stvaranja i sažimanja backup-a.";
    }

    //Izbriši izvornu .txt datoteku nakon što je backup sažet
    unlink($fileName);
}

//Zatvaranje veze s bazom podataka
$conn->close();

?>
