<html>
<?php
require_once '../includes/bootstrap.php';
require_once '../includes/footer.php';
require_once '../includes/db_config.php';
echo bootstrap(); ?>

<body>
    <header class="text-white bg-dark">
        <div class=container>
            <div class="d-flex flex-wrap mb-2 align-items-center justify-content-center justify-content-between">
                <a class="d-flex align-items-center mb-2 mb-lg-0 text-white text-decoration-none">Movie Night!
                </a>
                <h1>Admin Panel</h1>
                <a href="../actions/logout.php">Log out</a>
            </div>
        </div>
    </header>

    <?php

    $connection = new Connection();

    session_start();
    if (!isset($_SESSION['admin_logged_in'])) {
        header("Location: index.php");
        exit();
    }

    $movies = $connection->getAllMovies();
    echo "<h2 class='text-center fw-bold'>Movie Management</h2>";
    foreach ($movies as $movie) {
        echo "<div class='container'>";
        echo "<div class='d-flex flex-wrap mx-auto justify-content-between mt-4 bg-secondary text-white'>";
        echo "<div class='text-center mx-auto'>";
        echo "<form method='POST' action ='../actions/update_schedule.php'>";
        echo "<input type='hidden' name='movie_id' value='{$movie['id']}'>";
        echo "<h3 class='fw-bold'><u>{$movie['title']}</u></h3>";

        if (!empty($movie['schedules'])) {
            echo "<h4>Available Screenings:</h4>";

            foreach ($movie['schedules'] as $schedule) {
                echo "<h5><u>Date: {$schedule['date']}, Time: {$schedule['time']}, Seats: {$schedule['num_seats']}, Room: {$schedule['room']}</u></h5>";
            }
        } else {
            echo "<p>No screenings yet.</p>";
        }

        echo "<h4 class='mb-2'>Add Screening:</h4>";
        echo "<h5>Date: <input type='date' name='new_date'></h5>";
        echo "<h5>Time: <input type='time' name='new_time'></h5>";
        echo "<h5>Seats: <input type='number' name='new_seats'></h5>";
        echo "<h5>Room: <input type='number' name='new_room'></h5>";

        echo "<button type='submit' class='btn btn-primary'>Update</button>";
        echo "</form>";
        echo "</div>";



        $reservations = $connection->getAllReservations($movie['id']);
        echo "<div class='text-center mx-auto'>";
        if (!empty($reservations)) {

            foreach ($reservations as $reservation) {

                echo "<form method='post' action='../actions/update_schedule.php'>";
                echo "<h3>Reservation request: </h3>";
                echo "<h5>User: {$reservation['user_id']}, Date: {$reservation['date']}, Time: {$reservation['time']}, Seats: {$reservation['num_seats']}, Status: {$reservation['status']}</h5>";
                echo "<input type='hidden' name='reservation_id' value='{$reservation['id']}'>";
                echo "<button type='submit' name='accept_reservation' class='btn btn-primary'>Accept</button>";
                echo "<button type='submit' name='decline_reservation' class='btn btn-danger'>Decline</button>";
                echo "</form>";

            }
        } else {
            echo "<h3>No reservation requests.</h3>";
        }
        echo "</div>";
        echo "</div>";
        echo "</div>";
        echo "<hr>";
    }
    echo footer();
    ?>

</body>

</html>