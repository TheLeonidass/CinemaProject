<?php
require_once '../includes/db_config.php';
// Usage of Connection and Register classes
$database = new Connection();
$connection = $database->getConnection();
$register = new Register($connection);

if (isset($_POST['submit'])) {
$reg_email = $_POST['email'];
$reg_username = $_POST['username'];
$reg_password = $_POST['password'];
$error = array();

if (empty($reg_email) || empty($reg_username) || empty($reg_password)) {
    $error[] = 'All fields are required';
}

}

//DB Connection
if (empty($error)) {
    try {
        $connection;

        // Data insertion
        if ($register->register($reg_email, $reg_username, $reg_password)){
            echo "Registration successful! Please login";
        }
        else {
            echo "Registration failed. Please try again.";
        }

        $database->closeConnection();

    } 
    catch (Exception $e) {
        die("An error occurred: " . $e->getMessage());
    }
    // Redirection back to the register page
} else {
    
    header("Location: ../pages/register.php?error=" . urlencode(implode(',', $error)));
    exit();
}

?>