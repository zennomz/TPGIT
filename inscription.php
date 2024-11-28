<?php
require_once "includes/config.php";

session_start();

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: profil.php");
    exit;
}

$first_name = $last_name = $pseudo = $email = $password = "";
$first_name_err = $last_name_err = $pseudo_err = $email_err = $password_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty(trim($_POST["first_name"]))) {
        $first_name_err = "Veuillez entrer un prénom.";
    } else {
        $first_name = trim($_POST["first_name"]);
    }

    if (empty(trim($_POST["last_name"]))) {
        $last_name_err = "Veuillez entrer un nom de famille.";
    } else {
        $last_name = trim($_POST["last_name"]);
    }

    if (empty(trim($_POST["pseudo"]))) {
        $pseudo_err = "Veuillez entrer un pseudo.";
    } else {
        $pseudo = trim($_POST["pseudo"]);
    }

    if (empty(trim($_POST["email"]))) {
        $email_err = "Veuillez entrer un email.";
    } else {
        $sql = "SELECT id FROM users WHERE email = :email";
        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
            $param_email = trim($_POST["email"]);
            if ($stmt->execute()) {
                if ($stmt->rowCount() == 1) {
                    $email_err = "Cet email est déjà pris.";
                } else {
                    $email = trim($_POST["email"]);
                }
            } else {
                echo "Oops! Quelque chose s'est mal passé. Veuillez réessayer plus tard.";
            }
            unset($stmt);
        }
    }

    if (empty(trim($_POST["password"]))) {
        $password_err = "Veuillez entrer un mot de passe.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Le mot de passe doit avoir au moins 6 caractères.";
    } else {
        $password = trim($_POST["password"]);
    }

    if (empty($first_name_err) && empty($last_name_err) && empty($pseudo_err) && empty($email_err) && empty($password_err)) {
        $sql = "INSERT INTO users (first_name, last_name, pseudo, email, password) VALUES (:first_name, :last_name, :pseudo, :email, :password)";
        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":first_name", $param_first_name, PDO::PARAM_STR);
            $stmt->bindParam(":last_name", $param_last_name, PDO::PARAM_STR);
            $stmt->bindParam(":pseudo", $param_pseudo, PDO::PARAM_STR);
            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
            $stmt->bindParam(":password", $param_password, PDO::PARAM_STR);
            $param_first_name = $first_name;
            $param_last_name = $last_name;
            $param_pseudo = $pseudo;
            $param_email = $email;
            $param_password = password_hash($password, PASSWORD_DEFAULT);
            if ($stmt->execute()) {
                header("location: index.php");
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
</head>

<body>
    <?php include('includes/header.php'); ?>

    <div class="container">
        <h2>Bienvenue sur le Planificateur de Fêtes</h2>
        <div class="form_container">
            <h3>Créer ton compte</h3>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label>Pseudo</label>
                    <input type="text" name="pseudo" maxlength="10" value="<?php echo $pseudo; ?>">
                    <span class="error-message"><?php echo $pseudo_err; ?></span>
                </div>
                <div class="form-group">
                    <label>Prénom</label>
                    <input type="text" name="first_name" value="<?php echo $first_name; ?>">
                    <span class="error-message"><?php echo $first_name_err; ?></span>
                </div>
                <div class="form-group">
                    <label>Nom</label>
                    <input type="text" name="last_name" value="<?php echo $last_name; ?>">
                    <span class="error-message"><?php echo $last_name_err; ?></span>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?php echo $email; ?>">
                    <span class="error-message"><?php echo $email_err; ?></span>
                </div>
                <div class="form-group">
                    <label>Mot de passe</label>
                    <input type="password" name="password">
                    <span class="error-message"><?php echo $password_err; ?></span>
                </div>
                <button class="custom_button" type="submit">S'inscrire</button>
            </form>

            <p class="link_redirection">Avez-vous déjà un compte ? <a href="index.php">Connectez-vous ici</a>.</p>
        </div>
    </div>

    <?php include('includes/footer.php'); ?>
</body>

</html>
