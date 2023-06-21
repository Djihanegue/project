<?php
ob_start();
session_start();

$pageTitle = 'Profile';

include 'init.php';

if (isset($_SESSION['user'])) {
    $getuser = $con->prepare("SELECT * FROM users WHERE username=?");
    $getuser->execute(array($_SESSION['user']));
    $info = $getuser->fetch();

    if ($info) {
        if (isset($_GET['do'])) {
            $do = $_GET['do'];
        } else {
            $do = 'Managep';
        }

        // Start main page
        if ($do == 'Managep') {
            ?>

            <div class="card manage-page">
                <h1 class="text-center">My Profile</h1>
                <div class="information block">
                    <div class="card">
                        <div class="card-header bg-primary text-white">My information</div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                <li class="odd">
                                    <span class="icon"><i class="fa fa-lock fa-fw"></i></span>
                                    <span class="label"><strong>Name:</strong></span> <?php echo $info['username'] ?>
                                </li>
                                <li>
                                    <span class="icon"><i class="fa fa-envelope-o fa-fw"></i></span>
                                    <span class="label"><strong>Email:</strong></span> <?php echo $info['email'] ?>
                                </li>
                                <li class="odd">
                                    <span class="icon"><i class="fa fa-phone fa-fw"></i></span>
                                    <span class="label"><strong>Phone:</strong></span> <?php echo $info['telephone'] ?>
                                </li>
                                <li>
                                    <span class="icon"><i class="fa fa-user fa-fw"></i></span>
                                    <span class="label"><strong>Fullname:</strong></span> <?php echo $info['fullname'] ?>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <a href="profile.php?do=Edit" class='btn btn-primary'>Edit</a>
                </div>
            </div>

            <?php
        } elseif ($do == 'Edit') {
            // Edit page

            ?>

            <div class="about manage-page">
                <h1 class="text-center">Edit Profile</h1>

                <form class="form-horizontal" action="?do=Update" method="POST">
                    <!-- Start username field -->
                    <div class="input-group mb-3 input-group-lg">
                        <span class="input-group-text" id="addon-wrapping">Username</span>
                        <input type="text" value="<?php echo $info['username'] ?>" class="form-control" id="validationCustomUsername" name="username" aria-describedby="inputGroupPrepend" required>
                        <div class="invalid-feedback">
                            Username must be at least 3 letters long and free of numbers.
                        </div>
                    </div>
                    <!-- End username field -->

                    <!-- Start password field -->
                    <div class="input-group mb-3 input-group-lg">
                        <span class="input-group-text" id="addon-wrapping">Password</span>
                        <input type="password" name="newpassword" class="form-control" id="validationCustomPassword" aria-describedby="inputGroupPrepend">
                        <div class="valid-feedback">
                            Looks good!
                        </div>
                    </div>
                    <!-- End password field -->

                    <!-- Start email field -->
                    <div class="input-group mb-3 input-group-lg">
                        <span class="input-group-text" id="addon-wrapping">Email</span>
                        <input type="email" name="email" value="<?php echo $info['email'] ?>" class="form-control" id="validationCustomEmail" aria-describedby="inputGroupPrepend" required>
                        <div class="invalid-feedback">
                            Please choose a valid email.
                        </div>
                    </div>
                    <!-- End email field -->

                    <!-- Start phone number field -->
<div class="input-group mb-3 input-group-lg">
    <span class="input-group-text" id="addon-wrapping">Phone number</span>
    <input type="text" name="phone" value="<?php echo $info['telephone'] ?>" class="form-control" id="validationCustomPhone" aria-describedby="inputGroupPrepend" required>
    <div class="invalid-feedback">
        Please enter a valid phone number.
    </div>
</div>
<!-- End phone number field -->

                    <!-- Start full name field -->
                    <div class="input-group mb-3 input-group-lg">
                        <span class="input-group-text" id="addon-wrapping">Full name</span>
                        <input type="text" name="fullname" value="<?php echo $info['fullname'] ?>" class="form-control" id="validationCustomFullName" aria-describedby="inputGroupPrepend" required>
                        <div class="invalid-feedback">
                            Please enter your full name.
                        </div>
                    </div>
                    <!-- End full name field -->

                    <!-- Start button field -->
                    <div class="col-12">
                        <button class="btn btn-primary" type="submit">Save</button>
                    </div>
                    <!-- End button field -->
                </form>
            </div>

            <?php
        } elseif ($do == 'Update') {
            // Update page

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                // Get variables from the form
                $userid = $info['id'];
                $user = $_POST['username'];
                $email = $_POST['email'];
                $name = $_POST['fullname'];
                $phone = $_POST['phone'];

                // Password trick
                $pass = '';

                if (!empty($_POST['newpassword'])) {
                    $pass = sha1($_POST['newpassword']);
                } else {
                    // If the password field is empty, keep the existing password
                    $pass = $info['password'];
                }

                $formErrors = array();

                // Check if the phone number is valid
                  if (!preg_match('/^[0-9]{10}$/', $phone)) {
                     $formErrors[] = 'Please enter a valid 10-digit phone number.';
                     header('Refresh: 5; url=profile.php?do=Managep');
                 }

                // Check if the username length is at least 4 letters and free of numbers

                if (strlen($user) < 4 || !preg_match('/^[a-zA-Z]+$/', $user)) {
                    $formErrors[] = 'Username must be at least 4 letters long and contain only letters.';
                    header('Refresh: 5; url=profile.php?do=Managep');
                }

                if (strlen($name) < 4 || !preg_match('/^[a-zA-Z]+$/', $name)) {
                    $formErrors[] = 'Full name must be at least 4 letters long and contain only letters.';
                    header('Refresh: 5; url=profile.php?do=Managep');
                }

                if (!empty($formErrors)) {
                    foreach ($formErrors as $error) {
                        echo "<div class='alert alert-danger text-center'>" . $error . "</div>";
                        header('Refresh: 5; url=profile.php?do=Managep');
                    }
                } else {
                   // Update the database with this information
$stmt = $con->prepare("UPDATE users SET username=?, email=?, fullname=?, telephone=?, password=? WHERE id=?");
$stmt->execute(array($user, $email, $name, $phone, $pass, $userid));

                    // Check if any record was updated
                    if ($stmt->rowCount() > 0) {
                        echo "<div class='alert alert-success text-center'>Profile updated successfully.</div>";
                        header('Refresh: 5; url=profile.php?do=Managep');
                        exit();
                    } else {
                        echo "<div class='alert alert-danger text-center'>No changes were made.</div>";
                        header('Refresh: 5; url=profile.php?do=Managep');
                    }
                }
            } else {
                echo "<div class='alert alert-danger text-center'>Sorry, you can't browse this page directly.</div>";
                header('Refresh: 5; url=index.php');
            }
        }
    } else {
        header('Location: login.php');
        exit();
    }
} else {
    header('Location: login.php');
    exit();
}

include $tpl . 'footer.php';
?>