<html>
<?php
require_once '../includes/db_config.php';
require_once '../includes/bootstrap.php';
require_once '../includes/footer.php';
require_once '../includes/header.php';
echo bootstrap() ?>

<body class="image">
    <?php
    echo pageHeader();
    //displaying the email or username in the welcome page
    session_start();
    if (isset($_SESSION["logged_in"]) && isset($_SESSION["emailorusername"])) {
        $emailOrUsername = $_SESSION["emailorusername"];
        $connection = new Connection();
        $userId = $connection->getUserIdByEmailOrUsername($emailOrUsername);
        $_SESSION['user_id'] = $userId;
        echo "<h1>Welcome, $emailOrUsername!</h1>";
        echo '<a href="../actions/logout.php">Log out</a>';
    } else {
        header("Location: index.php");
        exit();
    }
    echo "</div>";
    echo "</div>";
    echo "</header>";
    ?>

    <div class="container mt-4">
        <h2 class="mb-3 text-center text-black fw-bold bg-white">Available movies</h2>
        <?php
        //movie information
        $connection = new Connection();
        $movies = $connection->getAllMovies();

        foreach ($movies as $movie) {
            echo "<div class='mb-4 text-center img-thumbnail'>";
            echo "<h3 class='fw-bold bg-primary text-center'>{$movie['title']}</h3>";
            echo "<div class='img-fluid d-inline-flex flex-column'>";
            echo "<img src='data:image/jpeg;base64," . base64_encode($movie['image']) . "'>";
            echo "</div>";

            //reservation form
            echo "<form method='POST' action='../actions/reserve.php'>";
            echo "<input type='hidden' name='movie_id' value='{$movie['id']}'>";
            echo "<div class='text-center'>";
            echo "<h3 class='fw-bold bg-secondary mt-2'>Make a reservation:</h3>";
            echo "<p>Select Screening: ";
            echo "<select name='screening_date' required>";

            //fetching all the screening dates for each movie
            $screeningDates = $connection->getUniqueScreeningDates($movie['id']);

            foreach ($screeningDates as $screening) {
                $datetime = "{$screening['date']} {$screening['time']}";
                echo "<option value='{$datetime}'>{$datetime}</option>";
            }

            echo "</select></p>";
            echo "<p>Number of Seats: ";
            echo "<input type='number' name='num_seats' min='1' max='10' required>";
            echo "</p>";
            echo "<input type='submit' value='Reserve Seats'>";
            echo "</form>";
            echo "</div>";
            echo "<h3 class='fw-bold bg-secondary mt-2'>Your reservation requests:</h3>";
            $reservations = $connection->getReservations($movie['id'], $userId);
            if (!empty($reservations)) {

                foreach ($reservations as $reservation) {

                    echo "<form method='post' action='../actions/update_schedule.php'>";
                    echo "<h5>Date: {$reservation['date']}, Time: {$reservation['time']}, Seats: {$reservation['num_seats']}, Status: {$reservation['status']}</h5>";
                    echo "<input type='hidden' name='reservation_id' value='{$reservation['id']}'>";
                    echo "</form>";

                }
            } else {
                echo "<h3>No reservation requests.</h3>";
            }
        }
        ?>
    </div>
    </div>
    </div>
    </div>
    <?php
    echo footer();
    ?>
</body>

</html>