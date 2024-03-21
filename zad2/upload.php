<?php

//Provjerava je li zahtjev poslan metodom POST i je li priložena datoteka za upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["fileToUpload"])) {
    $target_dir = "uploads/";  //Direktorij za pohranu uploadanih datoteka
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]); //Određuje se putanja do ciljne datoteke na serveru gdje će se pohraniti uploadana datoteka

    //Inicijalizira varijablu koja označava uspješnost uploada
    $uploadOk = 1;

    //Određuje ekstenziju datoteke koja se uploada
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    //Provjerava je li ekstenzija datoteke u skladu s dopuštenim formatima
    $allowed_formats = array("pdf", "jpg", "png");
    if (!in_array($imageFileType, $allowed_formats)) {
        echo "Only PDF, JPG, and PNG formats are allowed.";
        $uploadOk = 0;
    }

    //Provjerava postoji li već datoteka s istim imenom na odredišnom mjestu
    if (file_exists($target_file)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
    }

    //Ako je sve u redu s uploadom, nastavlja se s kriptiranjem
    if ($uploadOk == 1) {
        //Generira se ključ za kriptiranje i inicijalizacijski vektor
        $key = openssl_random_pseudo_bytes(32);
        $iv = openssl_random_pseudo_bytes(16);

        //Čita se sadržaj datoteke koja se uploada
        $file_content = file_get_contents($_FILES["fileToUpload"]["tmp_name"]);
        //Kriptira se sadržaj datoteke
        $encrypted_content = openssl_encrypt($file_content, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);

        //Pohranjuje se kriptirani sadržaj u datoteku
        $output_file = $target_dir . "encrypted_" . basename($_FILES["fileToUpload"]["name"]);
        file_put_contents($output_file, $iv . $encrypted_content);

        //Pohranjuje ključ u datoteku
        file_put_contents($target_dir . "key.txt", base64_encode($key));

        echo "The file " . htmlspecialchars(basename($_FILES["fileToUpload"]["name"])) . " has been successfully uploaded and encrypted.";
    } else {
        echo "An error occurred while uploading the file.";
    }
}
?>