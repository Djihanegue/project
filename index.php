<?php
session_start();
$nonavbar = '';
$pageTitle = 'Login';
$errorMessage = '';

if (isset($_SESSION['username'])) {
    header('Location: dashboard.php');
    exit();
}

include 'init.php';

// Check if the user is coming from an HTTP post request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['user'];
    $password = $_POST['pass'];

    if (empty($username) || empty($password)) {
        $errorMessage = 'Please enter both username and password.';
    } else {
        $hashedpass = sha1($password);

        // Check if the user exists in the database
        $stmt = $con->prepare("SELECT id, username, password FROM users WHERE username = ? AND password = ? AND groupid = 1 LIMIT 1");
        $stmt->execute(array($username, $hashedpass));
        $row = $stmt->fetch();
        $count = $stmt->rowCount();

        // If count > 0, this means the database contains a record for this username
        if ($count > 0) {
            $_SESSION['username'] = $username;
            $_SESSION['id'] = $row['id'];
            header('Location: dashboard.php');
            exit;
        } else {
            $errorMessage = 'Invalid username or password.';
        }
    }
}
?>

<div class="cont">
    <div class="form-box">
        <form class="login" action="<?php echo $_SERVER['PHP_SELF']?>" method="POST">
            <h1 class="text_center">Admin login</h1>
            <?php if (!empty($errorMessage)) : ?>
                <div class="alert alert-danger" role="alert"><?php echo $errorMessage; ?></div>
            <?php endif; ?>
            <input class="form-control" type="text" name="user" placeholder="Username" autocomplete="off"/>
            <input class="form-control" type="password" name="pass" placeholder="Password" autocomplete="new-password"/>
            <input>
            <button class="btn btn-primary btn-block" type="submit">Login</button>
            </input>
        </form>
    </div>
</div>

<?php include $tpl . 'footer.php'; ?>