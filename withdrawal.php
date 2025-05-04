<?php
// Hibafigyelés bekapcsolása (opcionális fejlesztési környezetben)
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 1);

// Kötelező fájlok betöltése
require_once "header.php";
require_once "maincore.php";
require_once "includes/dbconnector.class.php";

// Adatbázis kapcsolat
$db = new DbConnector;

// Ellenőrizni kell, hogy az űrlapot POST módszerrel küldték-e el
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['withdraw'])) {
    $withdrawAmount = floatval($_POST['amount']); // Felhasználó által megadott összeg

    // Ellenőrizd, hogy a felhasználónak van-e elegendő egyenlege
    if ($userdet['credit'] >= $withdrawAmount) {
        // Számítsd ki a díjakat (ha vannak)
        $fee = 0.0001; // Példa díj
        $finalAmount = $withdrawAmount - $fee;

        // Ellenőrizd, hogy a végső összeg pozitív-e
        if ($finalAmount > 0) {
            // Hozzáadás a `tbl_withdrawal` táblához
            $prepare = $db->mysqli->prepare("INSERT INTO tbl_withdrawal (user_id, amount, type) VALUES (?, ?, 1)");
            $prepare->bind_param('id', $uid, $finalAmount);
            $prepare->execute();
            $prepare->close();

            // Felhasználói egyenleg frissítése
            $db->query("UPDATE tbl_user SET credit = credit - $withdrawAmount WHERE user_id = $uid");

            $_SESSION['success'] = "Withdrawal of $finalAmount SOL processed successfully!";
            header('Location: withdrawal.php');
            exit();
        } else {
            $_SESSION['error'] = "The withdrawal amount after fees is too low.";
            header('Location: withdrawal.php');
            exit();
        }
    } else {
        $_SESSION['error'] = "Insufficient balance.";
        header('Location: withdrawal.php');
        exit();
    }
}
?>

<!-- HTML űrlap a withdrawal funkcióhoz -->
<form method="POST" action="withdrawal.php">
    <label for="amount">Amount to withdraw:</label>
    <input type="number" step="0.0001" name="amount" required>
    <button type="submit" name="withdraw">Withdraw</button>
</form>
