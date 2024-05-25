<?php
if (isset($_GET['id'])) {
    // Connexion à la base de données
    $conn = mysqli_connect('localhost', 'root', '', 'basede') or die(mysqli_error($conn));

    // Récupération des détails du prêt
    $id = intval($_GET['id']);
    $req_details = "
        SELECT 
            preteur.nom, preteur.prenom, preteur.carte, 
            pret.capital, pret.taux, pret.periode,pret.unite,pret.types, accorde.date, accorde.etat
        FROM 
            preteur
        INNER JOIN 
            accorde ON preteur.id = accorde.preteur_id
        INNER JOIN 
            pret ON accorde.pret_id = pret.id
        WHERE 
            pret.id = ?
    ";

    // Préparation et exécution de la requête
    $stmt = mysqli_prepare($conn, $req_details);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $pret = mysqli_fetch_assoc($result);
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/news.css">
    <title>Détails du prêt</title>
</head>
<body>
    <div class="container">
        <div id="nav">
            <div class="logo">
                <img src="log.png" alt="">
                <h3>Your <span style="color:red">Bank</span></h3>
            </div>
            <div class="nav">
                <nav>
                    <div class="navigation">
                        <ul>
                            <li><a href="index.php">Accueil</a></li>
                            <li><a href="new.php">Nouveau</a></li>
                            <li><a href="historique.php">Historique</a></li>
                            <li><a href="aide.php">Aide</a></li>
                        </ul>
                    </div>
                </nav>
            </div>
        </div>
        <div class="contourTab" style="margin-left:17%;">
            <h1>Détails du prêt</h1>
            <?php
                if ($pret) {
                    echo "<p><strong>Nom :</strong> " . $pret['nom'] . "</p>";
                    echo "<p><strong>Prénom :</strong> " . $pret['prenom'] . "</p>";
                    echo "<p><strong>Carte :</strong> " . $pret['carte'] . "</p>";
                    echo "<p><strong>Date de début :</strong> " . $pret['date'] . "</p>";
                    echo "<p><strong>Capital emprunté :</strong> " . $pret['capital'] . "</p>";
                    echo "<p><strong>Période :</strong> " . $pret['periode'] . " " . $pret['unite'] . "</p>";  // Afficher l'unité
                    echo "<p><strong>Taux :</strong> " . $pret['taux'] . "%</p>";
                    echo "<p><strong>État :</strong> " . $pret['etat'] . "</p>";
                    echo "<p><strong>État :</strong> " . $pret['types'] . "</p>";

                    // Calcul des dates et affichage des informations supplémentaires
                    $date_debut = new DateTime($pret['date']);
                    switch ($pret['unite']) {
                        case 'jours':
                            $interval = new DateInterval('P' . $pret['periode'] . 'D');
                            break;
                        case 'mois':
                            $interval = new DateInterval('P' . $pret['periode'] . 'M');
                            break;
                        case 'ans':
                            $interval = new DateInterval('P' . $pret['periode'] . 'Y');
                            break;
                        default:
                            echo "Unité de période non valide.";
                            exit;
                    }
                    $date_fin = clone $date_debut;
                    $date_fin->add($interval);

                    echo "<p><strong>Date de début du prêt :</strong> " . $date_debut->format('d-m-Y') . "</p>";
                    echo "<p><strong>Date de fin du prêt :</strong> " . $date_fin->format('d-m-Y') . "</p>";

                    $diff = $date_debut->diff($date_fin);
                    echo "<p><strong>Intervalle :</strong> " . $diff->y . " années, " . $diff->m . " mois, " . $diff->d . " jours</p>";

                    $date_actuelle = new DateTime();
                    if ($date_actuelle < $date_fin) {
                        echo "<p><strong>Statut du prêt :</strong> En cours</p>";
                    } else {
                        echo "<p><strong>Statut du prêt :</strong> Terminé</p>";
                    }

                    // Affichage du tableau d'amortissement
                    if ($pret['types']=='comparer') {
                        echo '<h3>Tableau d\'amortissement</h3>';
                        echo '<table class="tab">';
                        echo '<tr>';
                        echo '<th>Période</th>';
                        echo '<th>Amortissement</th>';
                        echo '<th>Annuités</th>';
                        echo '</tr>';
                    } else {
                        echo '<h3>Tableau d\'amortissement</h3>';
                        echo '<table class="tab">';
                        echo '<tr>';
                        echo '<th>Période</th>';
                        echo '<th>Capital emprunté</th>';
                        echo '<th>Intérêt</th>';
                        echo '<th>Amortissement</th>';
                        echo '<th>Annuités</th>';
                        echo '<th>Dette non remboursée</th>';
                        echo '</tr>';    
                    }




                    $cap = $pret['capital'];
                    $tau = $pret['taux'];
                    $pe = $pret['periode'];

                    // Assurez-vous d'ajuster le calcul pour l'annuité en fonction de l'unité de période
                    if ($pret['types'] == 'annuite') {
                        if ($pret['unite'] == 'jours') {
                            $tauxmois = $tau / (36000); // Ajustement pour les jours
                        } elseif ($pret['unite'] == 'mois') {
                            $tauxmois = $tau / 1200; // Ajustement pour les mois
                        } else {
                            $tauxmois = $tau / 100; // Ajustement pour les années
                        }
                        $an = ($cap * $tauxmois) / (1 - pow((1 + $tauxmois), -$pe));
                        $dette = $cap;
                    
                        for ($i = 1; $i <= $pe; $i++) {
                            $interet = $dette * $tauxmois;
                            $amortissement = $an - $interet;
                            $dette -= $amortissement;
                    
                            echo '<tr>';
                            echo '<td>' . $i . '</td>';
                            echo '<td>' . number_format($cap, 2, ',', ' ') . ' Ar' . '</td>';
                            echo '<td>' . number_format($interet, 2, ',', ' ') . ' Ar' . '</td>';
                            echo '<td>' . number_format($amortissement, 2, ',', ' ') . ' Ar' . '</td>';
                            echo '<td>' . number_format($an, 2, ',', ' ') . ' Ar' . '</td>';
                            echo '<td>' . number_format($dette, 2, ',', ' ') . ' Ar' . '</td>';
                            echo '</tr>';
                        }
                        # code...
                    } else if ($pret['types'] == 'amortissement') {
                        if ($pret['unite'] == 'jours') {
                            $tauxmois = $tau / (36000); // Ajustement pour les jours
                        } elseif ($pret['unite'] == 'mois') {
                            $tauxmois = $tau / 1200; // Ajustement pour les mois
                        } else {
                            $tauxmois = $tau / 100; // Ajustement pour les années
                        }
                        $an = ($cap * $tauxmois) / (1 - pow((1 + $tauxmois), -$pe));
                        $dette = $cap;
                        $pm = $cap / $pe; // Montant de chaque paiement périodique
                    
                        for ($i = 1; $i <= $pe; $i++) {
                            // $interet = $dette * $tauxmois;
                            // $amortissement = $an - $interet;
                            // $dette -= $amortissement;

                            $interet = $cap * $tauxmois; // Calcul de l'intérêt pour cette période
                            $annuite = $pm + $interet; // Calcul de l'annuité pour cette période
                            $cap -= $pm; // Mise à jour du capital restant
                    
                            echo '<tr>';
                            echo '<td>' . $i . '</td>';
                            echo '<td>' . number_format($cap + $pm, 2, ',', ' ') . ' Ar' . '</td>';
                            echo '<td>' . number_format($interet, 2, ',', ' ') . ' Ar' . '</td>';
                            echo '<td>' . number_format($pm, 2, ',', ' ') . ' Ar' . '</td>';
                            echo '<td>' . number_format($annuite, 2, ',', ' ') . ' Ar' . '</td>';
                            echo '<td>' . number_format($cap, 2, ',', ' ') . ' Ar' . '</td>';
                            echo '</tr>';
                        }
                    } else {
                        if ($pret['unite'] == 'jours') {
                            $tauxmois = $tau / (36000); // Ajustement pour les jours
                        } elseif ($pret['unite'] == 'mois') {
                            $tauxmois = $tau / 1200; // Ajustement pour les mois
                        } else {
                            $tauxmois = $tau / 100; // Ajustement pour les années
                        }
                        $an = ($cap * $tauxmois) / (1 - pow((1 + $tauxmois), -$pe));
                        $dette = $cap;
                        $caps = $cap; // Dette non remboursée initiale
                        $pm = $cap / $pe; // Montant de chaque paiement périodique
                        for ($i = 1; $i <= $pe; $i++) {
                            $interet0 = $caps * $tauxmois;
                            $interet = $dette * $tauxmois; // Calcul de l'intérêt pour cette période
                            $an = ($cap * $tauxmois) / (1 - pow((1 + $tauxmois), -$pe)); // Montant de chaque paiement périodique
                            $amortissement = $an - $interet; // Calcul de l'amortissement pour cette période
                            $dette -= $amortissement; // Mise à jour de la dette non remboursée
                            $caps -= $pm; // Mise à jour du capital restant
                    
                            echo '<tr>';
                            echo '<td>' . $i . '</td>';
                            echo '<td>' . number_format($interet0, 2, ' , ', ' ') .' Ar'. '</td>';
                            echo '<td>' . number_format($interet, 2, ' , ', ' ') .' Ar'. '</td>';
                            echo '</tr>';
                        }
                    } 

                    echo '</table>';
                } else {
                    echo "<p>Prêt non trouvé.</p>";
                }
            ?>
        </div>
    </div>
</body>
</html>

