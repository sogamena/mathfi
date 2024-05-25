<?php
// Connexion à la base de données
$conn = mysqli_connect('localhost', 'root', '', 'basede') or die(mysqli_error($conn));

if (isset($_GET['id'])) {
    // Suppression d'un prêt spécifique
    $id = intval($_GET['id']);
    
    // Supprimer de la table `accorde`
    $req_delete_accorde = "DELETE FROM accorde WHERE pret_id = ?";
    $stmt_accorde = mysqli_prepare($conn, $req_delete_accorde);
    mysqli_stmt_bind_param($stmt_accorde, 'i', $id);
    mysqli_stmt_execute($stmt_accorde);

    // Supprimer de la table `pret`
    $req_delete_pret = "DELETE FROM pret WHERE id = ?";
    $stmt_pret = mysqli_prepare($conn, $req_delete_pret);
    mysqli_stmt_bind_param($stmt_pret, 'i', $id);
    mysqli_stmt_execute($stmt_pret);

    header("Location: historique.php");
} elseif (isset($_GET['action']) && $_GET['action'] == 'delete_all') {
    // Suppression de tous les prêts
    // Supprimer de la table `accorde`
    $req_delete_all_accorde = "DELETE FROM accorde";
    mysqli_query($conn, $req_delete_all_accorde);

    // Supprimer de la table `pret`
    $req_delete_all_pret = "DELETE FROM pret";
    mysqli_query($conn, $req_delete_all_pret);

    header("Location: historique.php");
}
?>
