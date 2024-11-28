<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

require_once "includes/config.php";

$title = $description = $event_date = $location = $image = "";
$is_public = 1;
$title_err = $description_err = $event_date_err = $location_err = $image_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty(trim($_POST["title"]))) {
        $title_err = "Veuillez entrer un titre.";
    } else {
        $title = trim($_POST["title"]);
    }

    if (empty(trim($_POST["description"]))) {
        $description_err = "Veuillez entrer une description.";
    } else {
        $description = trim($_POST["description"]);
    }

    if (empty(trim($_POST["event_date"]))) {
        $event_date_err = "Veuillez entrer une date pour l'événement.";
    } else {
        $event_date = trim($_POST["event_date"]);
    }

    if (empty(trim($_POST["location"]))) {
        $location_err = "Veuillez entrer un lieu pour l'événement.";
    } else {
        $location = trim($_POST["location"]);
    }

    $is_public = isset($_POST['is_public']) ? 1 : 0;

    if (isset($_FILES["profile_image"])) {
        if ($_FILES["profile_image"]["error"] == 4) {
            $image = NULL;
        } elseif ($_FILES["profile_image"]["error"] != 0) {
            $image_err = "Erreur lors du téléchargement du fichier. Code d'erreur: " . $_FILES["profile_image"]["error"];
        } else {
            $file = $_FILES["profile_image"];
            $allowedMimeTypes = ['image/jpeg', 'image/png'];

            if (!in_array($file['type'], $allowedMimeTypes)) {
                $image_err = "Type de fichier non autorisé. Seuls JPEG et PNG sont acceptés.";
            } elseif ($file['size'] > 5000000) { // 5MB max
                $image_err = "Le fichier est trop volumineux. Taille maximale autorisée : 5MB.";
            } else {
                $imageData = file_get_contents($file['tmp_name']);
                $image = base64_encode($imageData);
            }
        }
    }

    if (empty($title_err) && empty($description_err) && empty($event_date_err) && empty($location_err) && empty($image_err)) {
        $sql = "INSERT INTO events (user_id, title, description, event_date, location, is_public, image) VALUES (:user_id, :title, :description, :event_date, :location, :is_public, :image)";

        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":user_id", $param_user_id, PDO::PARAM_INT);
            $stmt->bindParam(":title", $param_title, PDO::PARAM_STR);
            $stmt->bindParam(":description", $param_description, PDO::PARAM_STR);
            $stmt->bindParam(":event_date", $param_event_date, PDO::PARAM_STR);
            $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
            $stmt->bindParam(":is_public", $param_is_public, PDO::PARAM_INT);
            $stmt->bindParam(":image", $param_image, $image === NULL ? PDO::PARAM_NULL : PDO::PARAM_LOB);

            $param_user_id = $_SESSION["id"];
            $param_title = $title;
            $param_description = $description;
            $param_event_date = $event_date;
            $param_location = $location;
            $param_is_public = $is_public;
            $param_image = $image;

            if ($stmt->execute()) {
                header("location: event-list.php");
            } else {
                echo "Oops! Quelque chose s'est mal passé. Veuillez réessayer plus tard.";
            }

            unset($stmt);
        }
    }

    unset($pdo);
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>FestiPlan</title>
    <link rel="stylesheet" href="css/root.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/creation-event.css">
</head>

<body>

    <?php include('includes/header.php'); ?>

    <div class="container">
        <h2>Créer un Nouvel événement</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            <div>
                <label>Titre du le événement</label>
                <input type="text" name="title" maxlength="50" value="<?php echo $title; ?>">
                <span><?php echo $title_err; ?></span>
            </div>
            <div>
                <label>Descripton</label>
                <textarea name="description" rows="5" collumn="3"><?php echo $description; ?></textarea>
                <span><?php echo $description_err; ?></span>
            </div>
            <div>
                <label>Date du le événement</label>
                <input type="datetime-local" name="event_date" value="<?php echo $event_date; ?>">
                <span><?php echo $event_date_err; ?></span>
            </div>
            <div>
                <label>Liieu</label>
                <input type="text" name="location" maxlength="60" value="<?php echo $location; ?>">
                <span><?php echo $location_err; ?></span>
            </div>
            <div>
                <label>événement Public</label>
                <input type="checkbox" name="is_public" <?php echo $is_public ? 'checked' : ''; ?>>
            </div>
            <div>
                <label>Image du le événement</label>
                <label for="fileInput" class="custom-file-input">Selectionne une photo</label>
                <input type="file" id="fileInput" name="profile_image" accept="image/*" style="display: none;">
                <span><?php echo $image_err; ?></span>
            </div>

            <div>
                <img id="imagePreview" src="" alt="Aperçu de l'image" style="max-width: 200px; max-height: 200px; display: none;" />
            </div>

            <div class="container_submit_button">
                <button class="custom_button" type="submit">Créer l'événement</button>
            </div>
        </form>
    </div>

    <?php include('includes/footer.php'); ?>

    <script src="js/create-event.js"></script>

</body>

</html>