<?php
//Provjerava postoji li datoteka s ključem
$key_file = "uploads/key.txt";
if (file_exists($key_file)) {
    //Dohvaća ključ iz datoteke
    $key = base64_decode(file_get_contents($key_file));

    $dir = "uploads/";
    $files = glob($dir . "encrypted_*");

    //Provjera postoji li kriptiranih datoteka za dekripciju
    if (!empty($files)) {
        echo "<p>Files to download:</p>"; 

        //Petlja za dekripciju svake kriptirane datoteke
        foreach ($files as $file) {
            //Dohvaća inicijalizacijski vektor (IV) iz datoteke
            $iv = substr(file_get_contents($file), 0, 16);

            //Dohvaća kriptirani sadržaj datoteke
            $file_content = substr(file_get_contents($file), 16);

            //Dekriptira datoteku koristeći ključ i inicijalizacijski vektor
            $decrypted_content = openssl_decrypt($file_content, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);

            //Određuje putanju za dekriptiranu datoteku
            $decrypted_filename = str_replace("encrypted_", "", basename($file));
            $decrypted_file_path = $dir . $decrypted_filename;

             //Sprema dekriptiran sadržaj u novu datoteku te ispisuje link za preuzimanje dekriptirane datoteke, dok u slučaju greške ispisuje prikladnu poruku
            if (file_put_contents($decrypted_file_path, $decrypted_content) !== false) {
                echo "<a href='" . $decrypted_file_path . "' download>" . $decrypted_filename . "</a><br>";
            } else {
                echo "An error occurred while decrypting the document " . $decrypted_filename . "<br>";
            }
        }
    } else {
        //Ispisuje poruku ako nema dostupnih datoteka za preuzimanje
        echo "<p>No files available for download.</p>";
    }
} else {
    //Ispisuje poruku ako datoteka s ključem nije pronađena
    echo "Key file not found.";
}
?>
