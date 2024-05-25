<?php
// Connexion à la base de données
$conn = mysqli_connect('localhost', 'root', '', 'basede') or die(mysqli_error($conn));

// Sélection des données nécessaires
$req_selection = "
    SELECT 
        preteur.nom, preteur.prenom, preteur.carte, 
        pret.capital, pret.taux, pret.periode,pret.unite, pret.types, accorde.date, pret.id
    FROM 
        preteur
    INNER JOIN 
        accorde ON preteur.id = accorde.preteur_id
    INNER JOIN 
        pret ON accorde.pret_id = pret.id
    ORDER BY accorde.id DESC
";

$res_selection = mysqli_query($conn, $req_selection);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Historique des prêts</title>
    <link rel="stylesheet" href="CSS/news.css">
    <link rel="stylesheet" href="CSS/historique.css">
    <!-- <link rel="stylesheet" href="CSS/fontawesome/fontawesome.min.css"> -->
    <style>
        .en-cours {
            color: #007bff;
        }

        .termine {
            color:#28a745;
        }
        .ico{
            width: 2vw;
        }
        .tsi{
            position:absolute;
            /* margin-top:0.2%; */
            right:-5%;
            font-size:1.5vw;

        }
        .btnp{
            text-decoration:none;
            background:#ff450060;
            color:#000; 
            padding:15px;
            font-size:1.5vw;
            border-radius:10px;
        }
        .btnp:hover{
            color:black;
            background: #ff4500;
        }
    </style>
</head>
<body>
   <div class="container">
   <div id="nav">
                <div class="logo">
                    <img src="log.png" alt="">
                    <h3>Yours Bank</h3>
                </div>
                <div class="nav">
                    <nav>
                        <div class="navigation">
                            <ul>
                                <li><a href="index.php">Accueil</a></li>
                                <li><a href="new.php">Nouveau</a></li>
                                <li><a href="aide.php">Aide</a></li>
                            </ul>
                        </div>
                    </nav>
                </div>
            </div>
    <div id="historique">
    <div class="historique">
        <h3>HISTORIQUES</h3><br>
        <div class="droite">
            <table class="tab">
                <tr>
                    <th>NOM</th>
                    <th>PRENOM</th>
                    <th>CAPITAL</th>
                    <th>PERIODE</th>
                    <th>TAUX</th>
                    <th>DATE</th>
                    <th>ETAT</th>
                    <th>TYPES</th>
                    <th>ACTION</th>
                </tr>
                <?php
                if ($res_selection) {
                    while ($row = mysqli_fetch_assoc($res_selection)) {
                        // Calcul de la date de fin en fonction de l'unité
                        $date_debut = new DateTime($row['date']);
                        switch ($row['unite']) {
                            case 'jours':
                                $interval = new DateInterval('P' . $row['periode'] . 'D');
                                break;
                            case 'mois':
                                $interval = new DateInterval('P' . $row['periode'] . 'M');
                                break;
                            case 'ans':
                                $interval = new DateInterval('P' . $row['periode'] . 'Y');
                                break;
                            default:
                                $interval = new DateInterval('P0D'); // Par défaut, intervalle de 0 jours
                        }
                        $date_fin = clone $date_debut;
                        $date_fin->add($interval);

                        // Comparaison de la date actuelle avec la date de fin
                        $date_actuelle = new DateTime();
                        $etat = ($date_actuelle < $date_fin) ? "En cours" : "Terminé";
                        $classe_etat = ($etat === "En cours") ? "en-cours" : "termine";

                        echo '<tr>';
                        echo '<td>' . $row['nom'] . '</td>';
                        echo '<td>' . $row['prenom'] . '</td>';
                        echo '<td>' . $row['capital'] . '</td>';
                        echo '<td>' . $row['periode'] . ' ' . $row['unite'] . '</td>';
                        echo '<td>' . $row['taux'] . '</td>';
                        echo '<td>' . $row['date'] . '</td>';
                        echo '<td class="' . $classe_etat . '">' . $etat . '</td>';
                        echo '<td>' . $row['types'] . '</td>';
                        echo '<td>
                                <a href="detail.php?id=' . $row['id'] . '"><img class="ico" src="image/vu.png" alt=""></a>
                                <span class="tsi">|&nbsp;</span>
                                <a href="#" class="delete-btn" data-id="' . $row['id'] . '"><img class="ico" src="image/sup.png" alt=""></a>
                              </td>';
                        echo '</tr>';
                    }
                }
                ?>
            </table>
            <br>
            <a href="#" class="btnp" id="delete-all-btn">Supprimer tous les prêts</a>
        </div>
    </div>

    <!-- Boîte modale pour suppression de tous les prêts -->
    <div id="delete-all-modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <p>Voulez-vous vraiment supprimer tous les prêts ?</p>
            <div class="modal-buttons">
                <button id="confirm-delete-all" class="button">Oui</button>
                <button id="cancel-delete-all" class="button">Non</button>
            </div>
        </div>
    </div>

    <!-- Boîte modale pour suppression d'un prêt spécifique -->
    <div id="delete-modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <p>Voulez-vous vraiment supprimer ce prêt ?</p>
            <div class="modal-buttons">
                <button id="confirm-delete" style="background:darkgrey;" class="button">Oui</button>
                <button id="cancel-delete" style="background:darkgrey;" class="button">Non</button>
            </div>
        </div>
    </div>  
    </div>
   </div>

    <script>
        // Récupération des éléments de la modale pour supprimer tous les prêts
        var deleteAllModal = document.getElementById("delete-all-modal");
        var deleteAllBtn = document.getElementById("delete-all-btn");
        var closeAllSpan = deleteAllModal.getElementsByClassName("close")[0];
        var confirmDeleteAllBtn = document.getElementById("confirm-delete-all");
        var cancelDeleteAllBtn = document.getElementById("cancel-delete-all");

        // Ouvrir la modale lors du clic sur le bouton "Supprimer tous les prêts"
        deleteAllBtn.onclick = function() {
            deleteAllModal.style.display = "block";
        }

        // Fermer la modale lors du clic sur le "X"
        closeAllSpan.onclick = function() {
            deleteAllModal.style.display = "none";
        }

        // Fermer la modale lors du clic sur le bouton "Non"
        cancelDeleteAllBtn.onclick = function() {
            deleteAllModal.style.display = "none";
        }

        // Rediriger vers la page de suppression lors du clic sur "Oui"
        confirmDeleteAllBtn.onclick = function() {
            window.location.href = "delete.php?action=delete_all";
        }

        // Fermer la modale lorsqu'on clique en dehors de celle-ci
        window.onclick = function(event) {
            if (event.target == deleteAllModal) {
                deleteAllModal.style.display = "none";
            }
            if (event.target == deleteModal) {
                deleteModal.style.display = "none";
            }
        }

        // Récupération des éléments de la modale pour supprimer un prêt spécifique
        var deleteModal = document.getElementById("delete-modal");
        var deleteBtns = document.getElementsByClassName("delete-btn");
        var closeSpan = deleteModal.getElementsByClassName("close")[0];
        var confirmDeleteBtn = document.getElementById("confirm-delete");
        var cancelDeleteBtn = document.getElementById("cancel-delete");
        var deleteId;

        // Ouvrir la modale lors du clic sur un bouton "Supprimer"
        for (var i = 0; i < deleteBtns.length; i++) {
            deleteBtns[i].onclick = function() {
                deleteId = this.getAttribute("data-id");
                deleteModal.style.display = "block";
            }
        }

        // Fermer la modale lors du clic sur le "X"
        closeSpan.onclick = function() {
            deleteModal.style.display = "none";
        }

        // Fermer la modale lors du clic sur le bouton "Non"
        cancelDeleteBtn.onclick = function() {
            deleteModal.style.display = "none";
        }

        // Rediriger vers la page de suppression lors du clic sur "Oui"
        confirmDeleteBtn.onclick = function() {
            window.location.href = "delete.php?id=" + deleteId;
        }
    </script>
</body>
</html>

