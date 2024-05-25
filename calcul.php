<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/news.css">
    <title>Document</title>
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
            <?php
            // Vérifier si le formulaire a été soumis
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                // Vérifier si les champs obligatoires sont remplis
                $champs_obligatoires = ['nom', 'prenom', 'carte', 'daty', 'choix', 'unite'];
                $champs_vide = [];
                foreach ($champs_obligatoires as $champ) {
                    if (empty($_POST[$champ])) {
                        $champs_vide[] = $champ;
                    }
                }

                // Vérifier si les valeurs sont inférieures à zéro
                $erreurs = [];
                if ($_POST['capital'] <= 0) {
                    $erreurs[] = "Le capital ne peut pas être inférieur ou égal à zéro.";
                }
                if ($_POST['taux'] <= 0) {
                    $erreurs[] = "Le taux ne peut pas être inférieur ou égal à zéro.";
                }
                if ($_POST['periode'] <= 0) {
                    $erreurs[] = "La période ne peut pas être inférieure ou égal à zéro.";
                }

                // Afficher les messages d'erreur
                if (!empty($champs_vide) || !empty($erreurs)) {
                    if (!empty($champs_vide)) {
                        echo "Les champs suivants sont obligatoires : " . implode(', ', $champs_vide) . "<br>";
                    }
                    if (!empty($erreurs)) {
                        echo implode('<br>', $erreurs) . "<br>";
                    }
                } else {
                    // Vérifier si la clé 'choix' existe dans $_POST
                    if (isset($_POST['choix'])) {
                        // Récupérer la valeur sélectionnée
                        $choix = $_POST['choix'];
                        $periode = $_POST['periode'];
                        $debut_pret = $_POST['daty'];
                        $unite = $_POST['unite'];

                        // Convertir la date de début en objet DateTime
                        $date_debut = new DateTime($debut_pret);

                        // Calculer la date de fin en fonction de la période
                        switch ($unite) {
                            case 'jours':
                                $interval = new DateInterval('P' . $periode . 'D');
                                break;
                            case 'mois':
                                $interval = new DateInterval('P' . $periode . 'M');
                                break;
                            case 'ans':
                                $interval = new DateInterval('P' . $periode . 'Y');
                                break;
                            default:
                                echo "Période non valide.";
                                exit;
                        }

                        // Ajouter l'intervalle à la date de début pour obtenir la date de fin
                        $date_fin = clone $date_debut;
                        $date_fin->add($interval);

                        // Calculer la différence en jours, mois, années
                        $diff = $date_debut->diff($date_fin);

                        // Calculer la date actuelle
                        $date_actuelle = new DateTime();

                        // Comparer la date actuelle avec la date de fin
                        $etat = ($date_actuelle < $date_fin) ? 'En cours' : 'Terminé';

                        // Connexion à la base de données
                        $conn = mysqli_connect('localhost', 'root', '', 'basede') or die(mysqli_error($conn));
                        $nom = $_POST['nom'];
                        $prenom = $_POST['prenom'];
                        $carte = $_POST['carte'];
                        $capital = $_POST['capital'];
                        $taux = $_POST['taux'];

                        $req_preteur = "INSERT INTO preteur (nom, prenom, carte) VALUES ('$nom', '$prenom', '$carte')";
                        $res_preteur = mysqli_query($conn, $req_preteur);
                        if ($res_preteur) {
                            $preteur_id = mysqli_insert_id($conn);
                            $req_pret = "INSERT INTO pret (capital, taux, periode, unite, types) VALUES ('$capital', '$taux', '$periode', '$unite','$choix')";
                            $res_pret = mysqli_query($conn, $req_pret);
                            if ($res_pret) {
                                $pret_id = mysqli_insert_id($conn);
                                $req_accorde = "INSERT INTO accorde (preteur_id, pret_id, date, etat) VALUES ('$preteur_id', '$pret_id', '$debut_pret', '$etat')";
                                $res_accorde = mysqli_query($conn, $req_accorde);
                                if ($res_accorde) {
                                    //echo "Données insérées avec succès.";
                                } else {
                                    echo "Erreur lors de l'insertion dans la table accorde : " . mysqli_error($conn);
                                }
                            } else {
                                echo "Erreur lors de l'insertion dans la table pret : " . mysqli_error($conn);
                            }
                        } else {
                            echo "Erreur lors de l'insertion dans la table preteur : " . mysqli_error($conn);
                        }
                        mysqli_close($conn);

                        // Afficher le tableau d'amortissement (vous pouvez ajouter cette partie si nécessaire)
                        switch ($choix) {
                            case 'annuite':
                                include('annuite.php');
                                break;
                            case 'amortissement':
                                include('amort.php');
                                break;
                            case 'comparer':
                                include('comparer.php');
                                break;
                            default:
                                echo "Option non prise en charge";
                                break;
                        }
                    } else {
                        echo "Aucune option sélectionnée";
                    }
                }
            } else {
                // Rediriger si le formulaire n'a pas été soumis
                header("Location: new.php");
                exit;
            }
            ?>
        </div>
    </div>
</body>
</html>
