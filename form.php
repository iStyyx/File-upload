<?php
// Initialisation des variables
$errors = [];
$validForm = false;

// Je vérifie que le formulaire est soumis, comme pour tout traitement de formulaire.
if($_SERVER['REQUEST_METHOD'] === "POST"){ 
    // Securité en php
    // chemin vers un dossier sur le serveur qui va recevoir les fichiers uploadés (attention ce dossier doit être accessible en écriture)
    $uploadDir = 'public/uploads/';
    // le nom de fichier sur le serveur est ici généré à partir du nom de fichier sur le poste du client (mais d'autre stratégies de nommage sont possibles)
    $uploadFile = $uploadDir . basename($_FILES['avatar']['name']) . '_filtred';
    // Je récupère l'extension du fichier
    $extension = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
    // Les extensions autorisées
    $authorizedExtensions = ['jpg','png','gif','webp'];
    // Le poids max géré par PHP par défaut est de 2M
    $maxFileSize = 1000000;
    
    // Je sécurise et effectue mes tests

    /****** Si l'extension est autorisée *************/
    if( (!in_array($extension, $authorizedExtensions))){
        $errors[] = 'Veuillez sélectionner une image de type JPG, PNG, GIF ou WEBP uniquement !';
    }

    /****** On vérifie si l'image existe et si le poids est autorisé en octets *************/
    if( file_exists($_FILES['avatar']['tmp_name']) && filesize($_FILES['avatar']['tmp_name']) > $maxFileSize)
    {
    $errors[] = "Votre fichier doit faire moins de 1M !";
    }

    // On valide les données entrées dans le formulaires
    $identityCard = array_map('trim', $_POST);
    if (empty($identityCard['prenom'])) {
        $errors[] = 'Votre prénom est requis';
    }
    if (empty($identityCard['nom'])) {
        $errors[] = 'Votre nom est requis';
    }
    if (empty($identityCard['age'])) {
        $errors[] = 'Votre âge est requis';
    } 
    
    /****** Si je n'ai pas d"erreur alors j'upload *************/
    // on déplace le fichier temporaire vers le nouvel emplacement sur le serveur. Ça y est, le fichier est uploadé
    if(empty($errors)){
        move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadFile);
        $validForm = true;
    }
    
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Upload</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <form action="form.php" method="post" enctype="multipart/form-data">
        <label for="prenom">Votre prénom :</label>
        <input id="prenom" type="text" name="prenom"><br>
        <label for="nom">Votre nom :</label>
        <input id="nom" type="text" name="nom"><br>
        <label for="age">Votre âge :</label>
        <input id="age" type="number" name="age"><br>
        <label for="imageUpload">Uploadez votre image de profil :</label><br>
        <input id="imageUpload" type="file" name="avatar"><br><br>
        <button name="send">Envoyer</button>
    </form>

    <?php
        if($validForm) {
            echo "<br><br><div class='identity-card'>";
            echo "<img src='" .$uploadFile . "' alt=''>";
            echo "<ul><li> Nom : " . $identityCard['nom'] . "</li>";
            echo "<li> Prénom : " . $identityCard['prenom'] . "</li>";
            echo "<li> Âge : " . $identityCard['age'] . "</li></ul>";
            echo "</div>";
        } else {
            echo "<ul>";
            foreach($errors as $error){
                echo "<li> $error </li>";
            }
            echo "</ul>";
        }
    ?>
</body>
</html>