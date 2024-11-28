<?php
session_start();

require_once "includes/config.php";

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: profil.php");
    exit;
}

$email = $password = "";
$email_err = $password_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty(trim($_POST["email"]))) {
        $email_err = "Veuillez entrer votre email.";
    } else {
        $email = trim($_POST["email"]);
    }

    if (empty(trim($_POST["password"]))) {
        $password_err = "Veuillez entrer votre mot de passe.";
    } else {
        $password = trim($_POST["password"]);
    }

    if (empty($email_err) && empty($password_err)) {
        $sql = "SELECT id, email, password FROM users WHERE email = :email";

        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
            $param_email = $email;

            if ($stmt->execute()) {
                if ($stmt->rowCount() == 1) {
                    if ($row = $stmt->fetch()) {
                        $id = $row["id"];
                        $hashed_password = $row["password"];
                        if (password_verify($password, $hashed_password)) {
                            session_start();
                            
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["email"] = $email;
                            
                            header("location: profil.php");
                            exit;
                        } else {
                            header("location: index.php");
                            exit;
                        }
                    }
                } else {
                    header("location: index.php");
                    exit;
                }
            } else {
                echo "Oops! Quelque chose s'est mal passé. Veuillez réessayer plus tard.";
            }
            unset($stmt);
        }
    }
    
    unset($pdo);
}
?>