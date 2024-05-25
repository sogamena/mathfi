<?php
function afficherTableauAmortissement($periode, $cap, $tau, $pe, $conversion_taux) {
    echo '<br><h3>Tableau  sur l\'annuité constante sur \'amortissement</h3><br>';
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
    $pm = $cap / $pe; // Montant de chaque paiement périodique
    for ($i = 1; $i <= $pe; $i++) {
        $interet = $cap * ($tau / $conversion_taux); // Calcul de l'intérêt pour cette période
        $annuite = $pm + $interet; // Calcul de l'annuité pour cette période
        $cap -= $pm; // Mise à jour du capital restant

        echo '<tr>';
        echo '<td>' . $i . '</td>';
        echo '<td>' . number_format($cap + $pm, 2, ' , ', ' ') . ' Ar</td>';
        echo '<td>' . number_format($interet, 2, ' , ', ' ') . ' Ar</td>';
        echo '<td>' . number_format($pm, 2, ' , ', ' ') . ' Ar</td>';
        echo '<td>' . number_format($annuite, 2, ' , ', ' ') . ' Ar</td>';
        echo '<td>' . number_format($cap, 2, ' , ', ' ') . ' Ar</td>';
        echo '</tr>';
    }
    echo '</table>';
    echo '</div>';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cap = $_POST['capital']; // Capital emprunté
    $tau = $_POST['taux']; // Taux d'intérêt annuel (%)
    $pe = $_POST['periode']; // Période d'emprunt
    $periode = $_POST['unite']; // Type de période ('jours', 'mois', 'ans')

    // Constantes de conversion pour les taux
    $TAUX_JOURS = 36000;
    $TAUX_MOIS = 1200;
    $TAUX_ans = 100;

    switch ($periode) {
        case 'jours':
            afficherTableauAmortissement($periode, $cap, $tau, $pe, $TAUX_JOURS);
            $interval_spec = 'D';
            break;
        case 'mois':
            afficherTableauAmortissement($periode, $cap, $tau, $pe , $TAUX_MOIS);
            $interval_spec = 'M';
            break;
        case 'ans':
            afficherTableauAmortissement($periode, $cap, $tau, $pe , $TAUX_ans);
            $interval_spec = 'Y';
            break;
        default:
            echo 'Type de période non supporté';
            exit();
    }

    $date_debut = new DateTime();
    $date_fin = clone $date_debut;
    $date_fin->add(new DateInterval('P' . $pe . $interval_spec));

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
        //echo "Statut du prêt : Terminé<br>";
    }
}
?>
