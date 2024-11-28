<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

require_once "includes/config.php";

if (isset($_GET["delete"]) && !empty(trim($_GET["delete"]))) {
    $sql = "DELETE FROM events WHERE id = :id AND user_id = :user_id";

    if ($stmt = $pdo->prepare($sql)) {
        $stmt->bindParam(":id", $param_id, PDO::PARAM_INT);
        $stmt->bindParam(":user_id", $_SESSION["id"], PDO::PARAM_INT);
        $param_id = trim($_GET["delete"]);

        if ($stmt->execute()) {
            header("location: my-events.php");
            exit();
        } else {
            echo "Oops! Quelque chose s'est mal passé. Veuillez réessayer plus tard.";
        }
    }
    unset($stmt);
}

$sql = "SELECT id, title, image FROM events WHERE user_id = :user_id";
$events = [];

if ($stmt = $pdo->prepare($sql)) {
    $stmt->bindParam(":user_id", $_SESSION["id"], PDO::PARAM_INT);

    if ($stmt->execute()) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $events[] = $row;
        }
    } else {
        echo "Oops! Quelque chose s'est mal passé. Veuillez réessayer plus tard.";
    }
    unset($stmt);
}
unset($pdo);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>FestiPlan</title>
    <link rel="stylesheet" href="css/root.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/my-events.css">
</head>

<body>

    <?php include('includes/header.php'); ?>

    <div class="container">
        <h2>Mes événements</h2>
        <div class="event-container">
            <?php if (count($events) > 0) : ?>
                <?php foreach ($events as $event) : ?>
                    <div class="event">
                        <div>
                            <?php if (!empty($event["image"])) : ?>
                                <img class="image-rect" src="data:image/jpeg;base64,<?php echo $event["image"]; ?>" alt="Image de l'événement">
                            <?php else : ?>
                                <img src="/images/no-image.jpg" alt="Pas d'image disponible">
                            <?php endif; ?>
                            <p><?php echo htmlspecialchars($event["title"]); ?>
                            <p>
                        </div>

                        <div class="buttons">
                            <a class="custom_button" href="event-details.php?id=<?php echo $event["id"]; ?>">Détails</a>
                            <a class="custom_button custom_button_delete" href="my-events.php?delete=<?php echo $event["id"]; ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet événement ?');">Supprimer</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <p>Aucun événement à afficher.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php include('includes/footer.php'); ?>

</body>

</html>