<?php

session_start();

include("includes/header.php");

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

require_once "includes/config.php";


if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    $sql = "SELECT events.*, users.image AS user_image, users.pseudo AS user_pseudo FROM events LEFT JOIN users ON events.user_id = users.id WHERE events.id = :id";

    if ($stmt = $pdo->prepare($sql)) {
        $stmt->bindParam(":id", $param_id, PDO::PARAM_INT);

        $param_id = trim($_GET["id"]);

        if ($stmt->execute()) {
            if ($stmt->rowCount() == 1) {

                $event = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                header("location: error.php");
                exit();
            }
        } else {
            echo "Oops! Quelque chose s'est mal passé. Veuillez réessayer plus tard.";
        }
    }

    unset($stmt);
    unset($pdo);
} else {
    header("location: error.php");
    exit();
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
    <link rel="stylesheet" href="css/event-details.css">
</head>

<body>
    <div class="container">
        <h2>Détails de l'événement<br><?php echo htmlspecialchars($event["title"]); ?></h2>
        <?php if (!empty($event["image"])) : ?>
            <img class="event_image" src="data:image/jpeg;base64,<?php echo $event["image"]; ?>" alt="Image de l'événement" style="width:100%;height:auto;">
        <?php endif; ?>

        <div class="author">
            <?php if (!empty($event["user_image"])) : ?>
                <img src="data:image/jpeg;base64,<?php echo $event["user_image"]; ?>" alt="Image de profil de l'utilisateur" style="width:100px;height:100px;">
            <?php endif; ?>
            <p>événement de <br><?php echo htmlspecialchars($event["user_pseud"]); ?></p>
        </div>

        <p class="description"><b>Description :</b> <?php echo htmlspecialchars($event["description"]); ?></p>

        <p class="localisation">L'événement a lieu à <b><?php echo htmlspecialchars($event["location"]); ?></b>
        le <b><?php echo htmlspecialchars($event["event_date"]); ?></b>.</p>

        <p class="type">Il s'agit d'un événement <b><?php echo $event["is_public"] ? 'Public' : 'Privé'; ?></b> !</p>
    </div>

    <?php include('includes/footer.php'); ?>
</body>

</html>