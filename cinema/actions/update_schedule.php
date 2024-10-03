<?php
require_once '../includes/db_config.php';
$connection = new Connection();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $movie_id = $_POST['movie_id'] = isset($_POST['movie_id']) ? $_POST['movie_id'] : null;
    $existing_dates = isset($_POST['movie_date']) ? $_POST['movie_date'] : [];
    $existing_times = isset($_POST['movie_time']) ? $_POST['movie_time'] : [];

    $new_date = $_POST['new_date'] = isset($_POST['new_date']) ? $_POST['new_date'] : null;
    $new_time = $_POST['new_time'] = isset($_POST['new_time']) ? $_POST['new_time'] : null;
    $new_seats = $_POST['new_seats'] = isset($_POST['new_date']) ? $_POST['new_seats'] : null;
    $new_room = $_POST['new_room'] = isset($_POST['new_date']) ? $_POST['new_room'] : null;

    $numExistingDates = count($existing_dates);

    //schedule update
    for ($i = 0; $i < $numExistingDates; $i++) {
        $success = $connection->updateMovie(
            $movie_id,
            $existing_dates[$i],
            $existing_times[$i],
            ($new_seats) ? $new_seats : 0,
            ($new_room) ? $new_room : 0
        );
    }

    //new schedule
    if (!empty($new_date) && !empty($new_time)) {
        $success = $connection->addNewSchedule(
            $movie_id,
            $new_date,
            $new_time,
            $new_seats,
            $new_room
        );
        if ($success) {
            echo "Movie updated successfully";
        } else {
            echo "Update failed";
        }
    }

    //reservation logic
    if (isset($_POST['accept_reservation'])) {
        $reservationId = $_POST['reservation_id'];
        $success = $connection->updateReservationStatus($reservationId, 'Accepted');
        if ($success) {
            echo "Reservation accepted!";
        } else {
            echo "Something went wrong!";
        }

    } elseif (isset($_POST['decline_reservation'])) {
        $reservationId = $_POST['reservation_id'];
        $success = $connection->updateReservationStatus($reservationId, 'Declined');
        if ($success) {
            echo "Reservation declined!";
        } else {
            echo "Something went wrong!";
        }

    }

}
?>