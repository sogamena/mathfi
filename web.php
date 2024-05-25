<?php
$date_actuelle = new DateTime(); // ou votre méthode pour obtenir la date actuelle
$date_fin = new DateTime('2024-12-31'); // ou votre méthode pour obtenir la date de fin

$etat = ($date_actuelle < $date_fin) ? "En cours" : "Terminé";
$classe_etat = ($etat === "En cours") ? "en-cours" : "termine";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>État du Projet</title>
    <style>
        .en-cours {
            color: green;
        }

        .termine {
            color: red;
        }
    </style>
</head>
<body>
    <p>État du projet: <span class="<?php echo $classe_etat; ?>"><?php echo $etat; ?></span></p>
</body>
</html>