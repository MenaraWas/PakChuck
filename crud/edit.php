<?php
require_once "pdo.php";
session_start();

if (
    isset($_POST['autos_id']) && isset($_POST['make']) && isset($_POST['model'])
    && isset($_POST['year']) && isset($_POST['mileage'])
) {

    // Data validation
    if (strlen($_POST['make']) < 1 || strlen($_POST['model']) < 1 || strlen($_POST['year']) < 1 || strlen($_POST['mileage']) < 1) {
        $_SESSION['error'] = 'All Field required';
        header("Location: edit.php?autos_id=" . $_POST['autos_id']);
        return;
    } else if (!is_numeric($_POST['year']) || !is_numeric($_POST['mileage'])) {
        $_SESSION["error"] = "Mileage and year must be numeric";
        header("Location: edit.php?autos_id=".$_POST['autos_id']);
        return;
    } else {
        $sql = "UPDATE autos SET make = :make,
            model = :model, year = :year,
            mileage = :mileage
            WHERE autos_id = :autos_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':autos_id' => $_POST['autos_id'],
            ':make' => $_POST['make'],
            ':model' => $_POST['model'],
            ':year' => $_POST['year'],
            ':mileage' => $_POST['mileage']
        ));
        $_SESSION['success'] = 'Record updated';
        header('Location: index.php');
        return;
    }
}

// Guardian: Make sure that user_id is present
if (!isset($_GET['autos_id'])) {
    $_SESSION['error'] = "Missing autos_id";
    header('Location: index.php');
    return;
}

$stmt = $pdo->prepare("SELECT * FROM autos where autos_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['autos_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row === false) {
    $_SESSION['error'] = 'Bad value for autos_id';
    header('Location: index.php');
    return;
}

// Flash pattern
if (isset($_SESSION['error'])) {
    echo '<p style="color:red">' . $_SESSION['error'] . "</p>\n";
    unset($_SESSION['error']);
}

$autos_id = $row['autos_id'];
$mk = htmlentities($row['make']);
$mo = htmlentities($row['model']);
$yr = htmlentities($row['year']);
$mi = htmlentities($row['mileage']);

?>
<head>
    <title>Menara Lintang Was</title>
</head>
<p>Edit Auto</p>
<form method="post">
    <p>Make:
        <input type="text" name="make" value="<?= $mk ?>"></p>
    <p>Model:
        <input type="text" name="model" value="<?= $mo ?>"></p>
    <p>Year:
        <input type="text" name="year" value="<?= $yr ?>"></p>
    <p>Mileage:
        <input type="text" name="mileage" value="<?= $mi ?>"></p>
    <input type="hidden" name="autos_id" value="<?= $autos_id ?>">
    <p><input type="submit" value="Save" />
        <a href="index.php">Cancel</a></p>
</form>