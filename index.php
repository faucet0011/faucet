<?php
// Hibafigyelés bekapcsolása (opcionális fejlesztési környezetben)
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 1);

// Kötelező fájlok betöltése
require_once "header.php";
require_once "maincore.php";
require_once "includes/dbconnector.class.php";

// Új claim funkció
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['claim'])) {
    $creditAmount = 0.0002; // 0.0002 SOL
    
    // Ellenőrizd, hogy a felhasználó be van-e jelentkezve
    if (isset($_SESSION['user']['uid'])) {
        $uid = $_SESSION['user']['uid'];
        
        // Frissítsd a felhasználó egyenlegét
        $db->query("UPDATE tbl_user SET credit = credit + $creditAmount WHERE user_id = $uid");

        // Üzenet küldése a felhasználónak
        $_SESSION['success'] = "You have successfully claimed $creditAmount SOL!";
        header('Location: index.php');
        exit();
    } else {
        $_SESSION['error'] = "Please log in to claim SOL.";
        header('Location: login.php');
        exit();
    }
}
?>

<!-- HTML űrlap a claim funkcióhoz -->
<form method="POST" action="index.php">
    <button type="submit" name="claim">Claim 0.0002 SOL</button>
</form>
