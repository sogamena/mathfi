<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/news.css">
    <style>
        .disabled-cursor {
            cursor: zoom-in; /* Modifier ici le curseur lorsque le bouton est désactivé */
        }
    </style>
    <title>Document</title>
</head>
<body onload="verifForm(form1)">
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
                            <li><a href="historique.php">Historique</a></li>
                            <li><a href="aide.php">Aide</a></li>
                        </ul>
                    </div>
                </nav>
            </div>
        </div>
        <div class="contour">
            <div class="forme">
                <form action="calcul.php" method="post" id="form1" name="form1">
                    <div class="info">
                        <div class="pret">
                            <h3>Information du prêteur</h3> <br>
                            <input  type="text" name="nom" placeholder="Nom" oninput="verifForm()"><br>
                            <input  type="text" name="prenom" placeholder="Prénom" oninput="verifForm()"><br>
                            <input  type="text" name="carte" placeholder="Numéro Carte" oninput="verifForm()"><br>
                            <input  type="date" name="daty" placeholder="Numéro Carte" oninput="verifForm()"><br><br>
                        </div>
                        <div class="cap">
                            <h3 style="text-align:center;">Information prêt</h3> <br>
                            <input type="text" name="capital" onkeypress="return isNonAlphabetSymbol(event)" placeholder="Capital (Ar)" oninput="verifForm()"><br>
                            <input type="text" name="taux" onkeypress="return isNonAlphabetSymbol(event)" placeholder="Taux (%)" oninput="verifForm()"><br>
                            <input type="number" class="duree" id="identifiant" name="periode" onkeypress="return isNonAlphabetSymbos(event)" placeholder="Période" oninput="verifForm()"><!-- Ajout de l'événement oninput -->
                            <select name="unite" id="" oninput="verifForm()">
                                <option value="jours">Jours</option>
                                <option value="mois">Mois</option>
                                <option value="ans">Années</option><!-- Correction de la valeur -->
                            </select><br><br>
                            <select name="choix" id="" oninput="verifForm()">
                                <option value="annuite">Annuités constantes</option>
                                <option value="amortissement">Amortissements constante</option>
                                <option value="comparer">Comparaison des interêts</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" id="bouton" name="forminscription" value="submit" disabled>Confirmé</button><!-- Modification de l'attribut disabled -->
                </form>
            </div>
        </div>
    </div>
    <script>
        function verifForm() {
            var inputs = document.getElementsByTagName('input');
            var selects = document.getElementsByTagName('select');
            var button = document.getElementById('bouton');
            var allFilled = true;

            // Vérification des champs input
            for (var i = 0; i < inputs.length; i++) {
                if (inputs[i].value === '' || inputs[i].value === 'Identifiant...' || inputs[i].value === 'Code secret...') {
                    allFilled = false;
                    break;
                }
            }

            // Vérification des champs select
            for (var j = 0; j < selects.length; j++) {
                if (selects[j].value === '') {
                    allFilled = false;
                    break;
                }
            }

            // Activer/désactiver le bouton en fonction de l'état des champs
            if (allFilled) {
                button.disabled = false;
                button.style.cursor = "pointer";
                button.style.background = 'linear-gradient(60deg, #02367b, #006ca5, #0496c7, #04bade,#55e2e9)';
            } else {
                button.disabled = true;
                button.style.cursor = "no-drop";
                button.style.background = 'linear-gradient(60deg, #02367b40, #006ca540, #0496c740, #04bade40,#55e2e940)';
            }
        }
    </script>
    <script>
        function isNonAlphabetSymbol(evt) {
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if ((charCode >= 65 && charCode <= 90) || (charCode >= 97 && charCode <= 122)
                || (charCode === 43) || (charCode === 45) || (charCode === 42) || (charCode === 47)) {
                evt.preventDefault();
                return false;
            }
            return true;
        }

        function isNonAlphabetSymbos(event) {
            var charCode = (event.which) ? event.which : event.keyCode;
            if (charCode == 46 || (charCode > 31 && (charCode < 48 || charCode > 57))) {
                return false;
            }
            return true;
        }

    </script>
</body>
</html>
