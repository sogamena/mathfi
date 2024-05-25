<?php
function afficherTableauAmortissement($periode, $intervalle, $cap, $tau, $pe) {
    // Afficher le titre
    echo '<br><h3>Tableau d\'amortissement sur l\'annuité constante</h3><br>';
    echo '<div class="droite">';
    echo '<table class="tab">';
    echo '<tr>';
    echo '<th>Période</th>';
    echo '<th>Capital emprunté</th>';
    echo '<th>Intérêt</th>';
    echo '<th>Amortissement</th>';
    echo '<th>Annuités</th>';
    echo '<th>Dette non remboursée</th>';
    echo '</tr>';

    $dette = $cap; // Dette non remboursée initiale
    for ($i = 1; $i <= $pe; $i++) {
        $interet = $dette * $intervalle; // Calcul de l'intérêt pour cette période
        $an = ($cap * $intervalle) / (1 - pow((1 + $intervalle), -$pe)); // Montant de chaque paiement périodique
        $amortissement = $an - $interet; // Calcul de l'amortissement pour cette période
        $dette -= $amortissement; // Mise à jour de la dette non remboursée

        echo '<tr>';
        echo '<td>' . $i . '</td>';
        echo '<td>' . ($i > 1 ? number_format($dette + $amortissement, 2, ' , ', ' ') : number_format($cap, 2, ' , ', ' ')) .' Ar'. '</td>';
        echo '<td>' . number_format($interet, 2, ' , ', ' ') .' Ar'. '</td>';
        echo '<td>' . number_format($amortissement, 2, ' , ', ' ') .' Ar'. '</td>';
        echo '<td>' . number_format($an, 2, ' , ', ' ') .' Ar'. '</td>';
        echo '<td>' . number_format($dette, 2, ' , ', ' ') .' Ar'. '</td>';
        echo '</tr>';
    }
    echo '</table>';
    echo '</div>';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cap = $_POST['capital']; // Capital emprunté
    $tau = $_POST['taux']; // Taux d'intérêt annuel (%)
    $pe = $_POST['periode']; // Période d'emprunt (en périodes sélectionnées)
    $periode = $_POST['unite']; // Type de période ('jours', 'mois', 'ans')

    switch ($periode) {
        case 'jours':
            $intervalle = $tau / 36000;
            afficherTableauAmortissement($periode, $intervalle, $cap, $tau, $pe);
            $date_interval = 'D';
            break;
        case 'mois':
            $intervalle = $tau / 1200;
            afficherTableauAmortissement($periode, $intervalle, $cap, $tau, $pe);
            $date_interval = 'M';
            break;
        case 'ans':
            $intervalle = $tau / 100;
            afficherTableauAmortissement($periode, $intervalle, $cap, $tau, $pe);
            $date_interval = 'Y';
            break;
        default:
            echo 'Type de période non supporté';
            exit();
    }

    $date_debut = new DateTime();
    $date_fin = clone $date_debut;
    $date_fin->add(new DateInterval('P' . $pe . $date_interval));

    // Afficher les dates de début et de fin
    //echo "Date de début du prêt : " . $date_debut->format('d-m-Y') . "<br>";
    //echo "Date de fin du prêt : " . $date_fin->format('d-m-Y') . "<br>";

    // Calculer la différence en jours, mois, années
    $diff = $date_debut->diff($date_fin);
    //echo "Intervalle : " . $diff->y . " années, " . $diff->m . " mois, " . $diff->d . " jours<br>";
    $date_actuelle = new DateTime();

    // Comparer la date actuelle avec la date de fin
    if ($date_actuelle < $date_fin) {
       // echo "Statut du prêt : En cours<br>";
    } else {
        // echo "Statut du prêt : Terminé<br>";
    }
}
?>
