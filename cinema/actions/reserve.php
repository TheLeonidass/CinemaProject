<?php
require_once '../includes/db_config.php';

$connection = new Connection();
session_start();
if (!isset($_SESSION["emailorusername"]) || !$_POST['movie_id'] || !$_POST['screening_date'] || !$_POST['num_seats']) {
    exit;
}

$userId = $_SESSION["emailorusername"];
$movieId = $_POST['movie_id'];
$screeningDateTime = $_POST['screening_date'];
$numSeats = $_POST['num_seats'];
$status = "Pending";

$datetime_parts = isset($_POST['screening_date']) ? explode(' ', $_POST['screening_date']) : [];
list($screeningDate, $screeningTime) = count($datetime_parts) == 2 ? $datetime_parts : [null, null];

$success = $connection->addReservation(
    $userId,
    $movieId,
    $screeningDate,
    $screeningTime,
    $numSeats,
    $status
);

if ($success) {
    echo "Success! Pending acceptance";
} else {
    echo "Something went wrong.";
}
?>