<?php
//class for connection to the db
class Connection
{
    private $host = 'localhost';
    private $dbusername = 'root';
    private $dbpassword = "";
    private $dbname = "movie";
    private $connection;

    public function __construct()
    {
        $this->connection = new mysqli($this->host, $this->dbusername, $this->dbpassword, $this->dbname);

        if ($this->connection->connect_error) {
            die("Connection error: " . $this->connection->connect_error);
        }


    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function closeConnection()
    {
        $this->connection->close();
    }
    //method for login of the customer
    public function userLogin($emailOrUsername, $password)
    {
        $loginType = filter_var($emailOrUsername, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $query = "SELECT * FROM users WHERE role = 'customer' AND $loginType = '$emailOrUsername'";
        $result = $this->connection->query($query);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            if (password_verify($password, $row["password"])) {
                return true;
            }
        }
        return false;

    }
    //method for login of admin users
    public function adminLogin($emailOrUsername, $password)
    {
        $loginType = filter_var($emailOrUsername, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $query = "SELECT * FROM users WHERE role = 'admin' AND $loginType = '$emailOrUsername'";
        $result = $this->connection->query($query);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();

            if (password_verify($password, $row["password"])) {
                return true;
            }
        }
        return false;

    }
    //method to fetch movies from the db
    public function getAllMovies()
    {
        $query = "SELECT m.id, m.title, m.image, s.date, s.time, s.num_seats, s.room
    FROM movies m
    LEFT JOIN movie_schedule s ON m.id = s.movie_id
    ORDER BY m.id, s.date, s.time";
        $result = $this->connection->query($query);

        $movies = array();
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $movieId = $row['id'];

                if (!isset($movies[$movieId])) {
                    $movies[$movieId] = array(
                        'id' => $row['id'],
                        'title' => $row['title'],
                        'image' => $row['image'],
                        'schedules' => array(),
                    );
                }
                if (!empty($row['date'])) {
                    $movies[$movieId]['schedules'][] = array(
                        'date' => $row['date'],
                        'time' => $row['time'],
                        'num_seats' => $row['num_seats'],
                        'room' => $row['room'],
                    );
                }
            }
        }
        return $movies;
    }
    //method for adding a new movie schedule
    public function addNewSchedule($movie_id, $date, $time, $num_seats, $room)
    {
        $movie_id = $this->connection->real_escape_string($movie_id);
        $date = $this->connection->real_escape_string($date);
        $time = $this->connection->real_escape_string($time);
        $num_seats = $this->connection->real_escape_string($num_seats);
        $room = $this->connection->real_escape_string($room);

        $query = "INSERT INTO movie_schedule (movie_id, date, time, num_seats, room)
              VALUES ('$movie_id', '$date', '$time', '$num_seats', '$room')";

        return $this->connection->query($query);
    }

    //method for adding the reservations
    public function addReservation($emailOrUsername, $movieId, $date, $time, $numSeats, $status)
    {
        $userId = $this->getUserIdByEmailOrUsername($emailOrUsername);

        if ($userId === null) {
            return false;
        }

        $query = "INSERT INTO reservations (user_id, movie_id, date, time, num_seats, status) VALUES (?, ?, ?, ?, ?, ?)";
        $statement = $this->connection->prepare($query);
        $statement->bind_param("iissis", $userId, $movieId, $date, $time, $numSeats, $status);
        $success = $statement->execute();
        $statement->close();
        return $success;
    }

    //method for getting the userId from db
    public function getUserIdByEmailOrUsername($emailOrUsername): int
    {
        $query = "SELECT id FROM users WHERE email = ? OR username = ?";
        $statement = $this->connection->prepare($query);
        $statement->bind_param("ss", $emailOrUsername, $emailOrUsername);
        $statement->execute();
        $statement->bind_result($userId);
        $statement->fetch();
        $statement->close();

        return $userId;
    }

    //method for getting the reservations from db
    public function getReservations($movieId, $userId)
    {
        $query = "SELECT * FROM reservations WHERE movie_id = ? AND user_id = ?";
        $statement = $this->connection->prepare($query);
        $statement->bind_param("ii", $movieId, $userId);
        $statement->execute();
        $result = $statement->get_result();
        $reservations = array();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $reservations[] = $row;
            }
        }
        $statement->close();
        return $reservations;
    }
    public function getAllReservations($movieId)
    {
        $query = "SELECT * FROM reservations WHERE movie_id = ?";
        $statement = $this->connection->prepare($query);
        $statement->bind_param("i", $movieId);
        $statement->execute();
        $result = $statement->get_result();
        $reservations = array();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $reservations[] = $row;
            }
        }
        $statement->close();
        return $reservations;
    }

    //reservation status
    public function updateReservationStatus($reservationId, $status)
    {
        $query = "UPDATE reservations SET status = ? WHERE id = ?";
        $statement = $this->connection->prepare($query);
        $statement->bind_param("si", $status, $reservationId, );
        $success = $statement->execute();
        $statement->close();
        return $success;
    }

    //method for getting the schedules from the db
    public function getUniqueScreeningDates($movieId)
    {
        $query = "SELECT DISTINCT date, time FROM movie_schedule WHERE movie_id = ?";
        $statement = $this->connection->prepare($query);
        $statement->bind_param("i", $movieId);
        $statement->execute();
        $result = $statement->get_result();

        $screenings = array();

        while ($row = $result->fetch_assoc()) {
            $screenings[] = array('date' => $row['date'], 'time' => $row['time']);
        }

        $statement->close();
        return $screenings;
    }

    //method for updating the movie schedules
    public function updateMovie($movie_id, $movie_dates, $movie_times, $available_seats, $auditorium)
    {
        $deleteQuery = "DELETE FROM movie_schedule WHERE movie_id = ?";
        $deleteStatement = $this->connection->prepare($deleteQuery);
        $deleteStatement->bind_param("i", $movie_id);
        $deleteStatement->execute();

        $insertQuery = "INSERT INTO movie_schedule (movie_id, date, time, num_seats, room) VALUES (?, ?, ?, ?, ?)";
        $insertStatement = $this->connection->prepare($insertQuery);

        for ($i = 0; $i < count($movie_dates); $i++) {
            $date = $movie_dates[$i];
            $time = $movie_times[$i];
            $seats = $available_seats;
            $room = $auditorium;

            $insertStatement->bind_param("isssi", $movie_id, $date, $time, $seats, $room);
            $insertStatement->execute();
        }

        $deleteStatement->close();
        $insertStatement->close();

        return true;
    }
}



//class for new registrations
class Register
{
    private $connection;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    public function register($reg_email, $reg_username, $reg_password)
    {
        $hashedPassword = password_hash($reg_password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (email, username, password) VALUES ('$reg_email', '$reg_username', '$hashedPassword')";

        if ($this->connection->query($sql) === TRUE) {
            return true;
        } else {
            return false;
        }

    }



}

?>