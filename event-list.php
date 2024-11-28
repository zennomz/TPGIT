<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

require_once "includes/config.php";

$sql = "SELECT id, title, description, event_date, location, is_public, image FROM events ORDER BY event_date DESC";
$events = [];

if ($result = $pdo->query($sql)) {
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $events[] = $row;
    }
    unset($result);
} else {
    echo "Oops! Quelque chose s'est mal passé. Veuillez réessayer plus tard.";
}

unset($pdo);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Liste des événements</title>
    <link rel="stylesheet" href="css/root.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/event-list.css">
</head>

<body>

    <?php include('includes/header.php'); ?>

    <div class="container">
        <h2>FestiPlan</h2>
        <div class="event-container">
            <?php if (count($events) > 0) : ?>
                <?php foreach ($events as $event) : ?>
                    <a href="event-details.php?id=<?php echo $event["id"]; ?>" class="event-card-link">
                        <div class="event-card">
                            <div class="top">
                                <h3><?php echo htmlspecialchars($event["title"]); ?></h3>
                                <div class="image-container">
                                    <?php if (!empty($event["image"])) : ?>
                                        <img class="image-rect" src="data:image/jpeg;base64,<?php echo $event["image"]; ?>" alt="Image de l'événement">
                                    <?php else : ?>
                                        <img src="/images/no-image.jpg" alt="Pas d'image disponible">
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="bottom">
                                <p>Lieu: <?php echo htmlspecialchars($event["location"]); ?></p>
                                <p>Date: <?php echo htmlspecialchars($event["event_date"]); ?></p>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else : ?>
                <p>Aucun événement à afficher.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php include('includes/footer.php'); ?>

</body>

</html>