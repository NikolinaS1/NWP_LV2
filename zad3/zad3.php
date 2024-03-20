<!DOCTYPE html>
<html>
  <head>
      <title>Profiles</title>
      <!--Uključujemo CSS datoteku za stilizaciju kartica-->
      <link rel="stylesheet" href="zad3.css" />
  </head>

  <body>
    <?php
      //Učitavamo XML datoteku
      $xml = simplexml_load_file("LV2.xml");

       //Iteriramo kroz svaki <record> element u XML datoteci te dobivamo podatke iz svakog <record> elementa
      foreach ($xml->record as $record) {
          $id = $record->id;
          $ime = $record->ime;
          $prezime = $record->prezime;
          $email = $record->email;
          $spol = $record->spol;
          $slika = $record->slika;
          $zivotopis = $record->zivotopis;

          //Prikazujemo profil osobe u obliku kartice
          echo "<div class='card'>";
          echo "<img src='$slika' alt='$ime $prezime' />";
          echo "<h2>$ime $prezime</h2>";
          echo "<p><strong>Email:</strong> $email</p>";
          echo "<p><strong>Spol:</strong> $spol</p>";
          echo "<p><strong>Životopis:</strong> $zivotopis</p>";
          echo "</div>";
      }
    ?>
  </body>
</html>
