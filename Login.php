
<?php

session_start();
$pageTitle = 'Login';
$errorMessage = '';

if (isset($_SESSION['user'])) {
   // header('Location: index.php');
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
        $stmt = $con->prepare("SELECT  username, password FROM users WHERE username = ? AND password = ?  ");
        $stmt->execute(array($username, $hashedpass));
        $count = $stmt->rowCount();

        // If count > 0, this means the database contains a record for this username
        if ($count > 0) {
            $_SESSION['user'] = $username;
            header('Location: index.php');
            exit;
        } else {
            $errorMessage = 'Invalid username or password.';
        }
    }
}
?>

<div class="cont">
    <div class="form-box">
    <h1 class="text_center">
        <span class="selected" data-class="login">Login</span> | <span data-class="signup">Signup</span>
    </h1>
    <form class="login" action="<?php echo $_SERVER['PHP_SELF']?>" method="POST">
    <?php if (!empty($errorMessage)) : ?>
        <div class="alert alert-danger" role="alert"><?php echo $errorMessage; ?></div>
    <?php endif; ?>
    <input class="form-control" type="text" name="user" placeholder="Username" autocomplete="off"/>
    <input class="form-control" type="password" name="pass" placeholder="Password" autocomplete="new-password"/>
    <button class="btn btn-primary btn-block" type="submit">Login</button>
</form>

<form class="signup" action="<?php echo $_SERVER['PHP_SELF']?>" method="POST">

    <?php if (!empty($errorMessage)) : ?>
        <div class="alert alert-danger" role="alert"><?php echo $errorMessage; ?></div>
    <?php endif; ?>
    <input type="text" class="form-control" id="validationCustomUsername" name="username" autocomplete="off" aria-describedby="inputGroupPrepend" placeholder="Username to login " required>
    <input type="password" name="password" class="form-control" id="validationCustomPassword" autocomplete="new-password" aria-describedby="inputGroupPrepend" placeholder="Password must be complex" required>
    <input type="password" name="password2" class="form-control" id="validationCustomPassword" autocomplete="new-password" aria-describedby="inputGroupPrepend" placeholder="Confirm Password" required>
   
    <input type="email" name="email" class="form-control" id="validationCustomEmail" aria-describedby="inputGroupPrepend" placeholder="Email" required>
    <div class="invalid-feedback">
        Please choose a valid email.
    </div>
    <input type="text" name="fullname" class="form-control" id="validationCustomFullName" aria-describedby="inputGroupPrepend" placeholder="Full name" required>
    <div class="invalid-feedback">
        Please enter your full name.
    </div>
    <button class="btn btn-primary btn-block" type="submit">Signup</button>
</form>
    
    </div>
</div>

<?php include $tpl . 'footer.php'; ?>
<script>
    $('.cont h1 span').click(function() {
        $(this).addClass('selected').siblings().removeClass('selected');
        $('.cont form').hide();
        $('.' + $(this).data('class')).fadeIn(100);
    });
</script>