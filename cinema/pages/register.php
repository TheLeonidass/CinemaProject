<?php require '../includes/bootstrap.php';
require_once '../includes/footer.php'; ?>

<html>
<?php echo bootstrap() ?>

<body class="image">
    <div class="d-flex text-center align-items-center flex-column vh-100 justify-content-around container-xl">
        <form action="../actions/insert.php" method="POST" autocomplete="off">
            <div class="login">
                <h2>Register</h2>
            </div>
            <div class="mb-3">
                <input type="email" name="email" class="form-control" placeholder="Enter email" required>
            </div>
            <div class="mb-3">
                <input type="text" class="form-control" name="username" placeholder="Enter username" required>
            </div>
            <div class="mb-3">
                <input type="password" class="form-control" name="password" placeholder="Enter password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100" name="submit">Submit</button>
            <a href="index.php">Login</a>
        </form>
    </div>
    </div>
    <?php
    echo footer();
    ?>
</body>

</html>