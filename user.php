<?php
ob_start();

session_start();

if (isset($_SESSION['username'])) {
    $pageTitle = 'Manage Members';
    include 'init.php';
    if (isset($_GET['do'])) {
        $do = $_GET['do'];
    } else {
        $do = 'ManageS';
    }

    // Start main page
    if ($do == 'ManageS') {
        // Member page
        
        $query='';
        if(isset($_GET['page'])&& $_GET['page']=='panding')
         { $query = 'AND regstatus=0';}  

        //select all users except admn
        $stmt = $con->prepare("SELECT * FROM users WHERE id != 1 $query");
$stmt->execute();
$rows = $stmt->fetchAll();
?>

<div class="about manage-page">
    <h1 class="text-center">Manage Members</h1>

    <div class="table-responsive">
        <table class="main-table text-center table table-bordered">
            <tr>
                <td>ID</td>
                <td>Username</td>
                <td>Email</td>
                <td>Full name</td>
                <td>Registration Date</td>
                <td>Control</td>
            </tr>
            <?php
            foreach ($rows as $row) {
    echo "<tr>";
    echo "<td>".$row['id']."</td>";
    echo "<td>".$row['username']."</td>";
    echo "<td>".$row['email']."</td>";
    echo "<td>".$row['fullname']."</td>";
    echo "<td>".$row['date']."</td>"; // Move this line inside the loop
    echo "<td>
        <a href='user.php?do=Edit&id=".$row['id']."' class='btn btn-success'> Edit</a>
        <a href='user.php?do=Delete&id=".$row['id']."' class='btn btn-danger confirm' onclick='return confirmDelete()'> Delete</a>
    </td>";
    echo "</tr>";
}
            ?>
        </table>
    </div>
    <a href="user.php?do=Add" class="btn btn-primary"><i class="fa fa-plus"></i> new user</a>
</div>
    
    <?php
    }
    elseif ($do == 'Edit') {
        // Edit page

        // Check if the 'id' parameter is numeric and get its integer value
        $id = !empty($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : 0;

        // Select all data depending on this 'id'
        $stmt = $con->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");

        // Execute the query
        $stmt->execute(array($id));

        // Fetch the data
        $row = $stmt->fetch();

        // Check if the id exists
        if ($stmt->rowCount() > 0) {
            ?>
            <div class="about manage-page">
            <h1 class="text-center">Edit Profile</h1>
         
                <form class="form-horizontal" action="?do=update" method="POST">
                    <input type="hidden" name="userid" value="<?php echo $id ?>" />
                    <!-- Start username field -->
                    <div class="input-group mb-3 input-group-lg">
                        <span class="input-group-text" id="addon-wrapping">Username</span>
                        <input type="text" value="<?php echo $row['username'] ?? '' ?>" class="form-control" id="validationCustomUsername" name="username" aria-describedby="inputGroupPrepend" required>
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

<!-- Start password confirmation field -->
<div class="input-group mb-3 input-group-lg">
    <span class="input-group-text" id="addon-wrapping">Confirm Password</span>
    <input type="password" name="password2" class="form-control" id="validationCustomPassword2" aria-describedby="inputGroupPrepend" placeholder="Confirm Password">
    <div class="valid-feedback">
        Looks good!
    </div>
    <div class="invalid-feedback">
        Passwords do not match.
    </div>
</div>
<!-- End password confirmation field -->


                    <!-- Start email field -->
                    <div class="input-group mb-3 input-group-lg">
                        <span class="input-group-text" id="addon-wrapping">Email</span>
                        <input type="email" name="email" value="<?php echo $row['email'] ?? '' ?>" class="form-control" id="validationCustomEmail" aria-describedby="inputGroupPrepend" required>
                        <div class="invalid-feedback">
                            Please choose a valid email.
                        </div>
                    </div>
                    <!-- End email field -->

                    <!-- Start full name field -->
                    <div class="input-group mb-3 input-group-lg">
                        <span class="input-group-text" id="addon-wrapping">Full name</span>
                        <input type="text" name="fullname" value="<?php echo $row['fullname'] ?? '' ?>" class="form-control" id="validationCustomFullName" aria-describedby="inputGroupPrepend" required>
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




            
<script>
    // JavaScript code to validate password matching
    var passwordInput = document.getElementById('validationCustomPassword');
    var confirmPasswordInput = document.getElementById('validationCustomPassword2');
    var invalidFeedback = document.querySelector('.invalid-feedback');

    function validatePassword() {
        if (passwordInput.value !== confirmPasswordInput.value) {
            confirmPasswordInput.classList.add('is-invalid');
            invalidFeedback.style.display = 'block';
        } else {
            confirmPasswordInput.classList.remove('is-invalid');
            invalidFeedback.style.display = 'none';
        }
    }

    passwordInput.addEventListener('input', validatePassword);
    confirmPasswordInput.addEventListener('input', validatePassword);
</script>

        <?php
        } else {
            $theMsg = "<div class='alert alert-danger text-center'>There is no user with the specified ID.</div>";
            redirectHome($theMsg,'back');
           
        }
    } elseif ($do == 'update') {
       // Update page
       echo" <div class='about manage-page'>";

       echo "<h1 class='text-center'>Update Member</h1>";
       if ($_SERVER['REQUEST_METHOD'] == 'POST') {
           // Get variables from the form
           $userid = $_POST['userid'] ?? 0;
           $user = $_POST['username'] ?? '';
           $email = $_POST['email'] ?? '';
           $name = $_POST['fullname'] ?? '';
       
           // Password trick
$pass = '';
if (empty($_POST['newpassword'])) {
    // Vérifier si la clé 'oldpassword' existe dans le tableau $_POST
    if (isset($_POST['oldpassword'])) {
        $pass = $_POST['oldpassword'];
    }
} else {
    $pass = sha1($_POST['newpassword']);
}
       
           // Check if the username length is at least 4 letters and free of numbers
           $formErrors = array();
           if (strlen($user) < 4 || !preg_match('/^[a-zA-Z]+$/', $user)) {
               $formErrors[] = 'Username must be at least 4 letters long and contain only letters.';
           }
           if (strlen($name) < 4 || !preg_match('/^[a-zA-Z]+$/', $name)) {
               $formErrors[] = 'Full name must be at least 4 letters long and contain only letters.';
           }
       
           if (!empty($formErrors)) {
               foreach ($formErrors as $error) {
               $theMsg =$error;
                redirectHome($theMsg ,'back');
                  
               }
           } else {
               // Update the database with this information
               $stmt = $con->prepare("UPDATE users SET username=?, email=?, fullname=?, password=? WHERE id=?");
               $stmt->execute(array($user, $email, $name, $pass, $userid));
       
               // Check if any record was updated
               if ($stmt->rowCount() > 0) {
                $theMsg = "<div class='alert alert-success text-center'>Record updated successfully.</div>";
                redirectHome($theMsg,'user.php?do=ManageS');
                  
               } else {
               $therMsg = "<div class='alert alert-danger text-center'>No changes were made.</div>";
                redirectHome($theMsg,'back');
                  
               }
           }
       } else {
          $theMsg= "<div class='alert alert-danger text-center'>Sorry, you can't browse this page directly.</div>";
           redirectHome($theMsg);
       }
echo" </div >";
    }
    elseif($do=='Add'){
       //add members page
      
       ?>
       <div class="about manage-page">
       <h1 class="text-center">Add new member</h1>
       
           <form class="form-horizontal" action="?do=insert" method="POST">
               
               <!-- Start username field -->
               <div class="input-group mb-3 input-group-lg">
                   <span class="input-group-text" id="addon-wrapping">Username</span>
                   <input type="text" class="form-control" id="validationCustomUsername" name="username" aria-describedby="inputGroupPrepend" placeholder="Username to login " required>
                   <div class="valid-feedback">
                       Looks good!.
                   </div>
               </div>
               <!-- End username field -->

              <!-- Start password field -->
<div class="input-group mb-3 input-group-lg">
    <span class="input-group-text" id="addon-wrapping">Password</span>
    <input type="password" name="password" class="form-control" id="validationCustomPassword" aria-describedby="inputGroupPrepend">
    <div class="valid-feedback">
        Looks good!
    </div>
</div>
<!-- End password field -->

<!-- Start password confirmation field -->
<div class="input-group mb-3 input-group-lg">
    <span class="input-group-text" id="addon-wrapping">Confirm Password</span>
    <input type="password" name="password2" class="form-control" id="validationCustomPassword2" aria-describedby="inputGroupPrepend" placeholder="Confirm Password">
    <div class="valid-feedback">
        Looks good!
    </div>
    <div class="invalid-feedback">
        Passwords do not match.
    </div>
</div>

               <!-- Start email field -->
               <div class="input-group mb-3 input-group-lg">
                   <span class="input-group-text" id="addon-wrapping">Email</span>
                   <input type="email" name="email"  class="form-control" id="validationCustomEmail" aria-describedby="inputGroupPrepend" placeholder="Email must be valid" required>
                   <div class="invalid-feedback">
                       Please choose a valid email.
                   </div>
               </div>
               <!-- End email field -->

               <!-- Start full name field -->
               <div class="input-group mb-3 input-group-lg">
                   <span class="input-group-text" id="addon-wrapping">Full name</span>
                   <input type="text" name="fullname"  class="form-control" id="validationCustomFullName" aria-describedby="inputGroupPrepend" placeholder="Full name appear in your profile page" required>
                   <div class="invalid-feedback">
                       Please enter your full name.
                   </div>
               </div>
               <!-- End full name field -->

               <!-- Start button field -->
               <div class="col-12">
                   <button class="btn btn-primary" type="submit">Add member</button>
               </div>
               <!-- End button field -->
           </form>
       </div>


       <script>
    // JavaScript code to validate password matching
    var passwordInput = document.getElementById('validationCustomPassword');
    var confirmPasswordInput = document.getElementById('validationCustomPassword2');
    var invalidFeedback = document.querySelector('.invalid-feedback');

    function validatePassword() {
        if (passwordInput.value !== confirmPasswordInput.value) {
            confirmPasswordInput.classList.add('is-invalid');
            invalidFeedback.style.display = 'block';
        } else {
            confirmPasswordInput.classList.remove('is-invalid');
            invalidFeedback.style.display = 'none';
        }
    }

    passwordInput.addEventListener('input', validatePassword);
    confirmPasswordInput.addEventListener('input', validatePassword);
</script>

   <?php

    }elseif($do=='insert'){
      // Insert page
    
      echo" <div class='about manage-page'>";
      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        echo "<h1 class='text-center'>Insert member</h1>";

        // Get variables from the form
        $user = $_POST['username'] ?? '';
        $pass = $_POST['password'] ?? '';
        $email = $_POST['email'] ?? '';
        $name = $_POST['fullname'] ?? '';

        $hashpass=sha1($_POST['password']);

        // Validate and sanitize inputs
        $formErrors = array();
        if (strlen($user) < 4 || preg_match('/\d/', $user)) {
            $formErrors[] = 'Username must be at least 4 letters long and free of numbers.';
        }
        if (strlen($name) < 4 || preg_match('/\d/', $name)) {
            $formErrors[] = 'Full name must be at least 4 letters long and free of numbers.';
        }

        // Check for other validation rules (e.g., email, password complexity)

        if (empty($formErrors)) {
          // Check if the username already exists
$stmt = $con->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute(array($user));
if ($stmt->rowCount() > 0) {
    $theMsg= "<div class='alert alert-danger text-center'>Username already exists. Please choose a different username.</div>";
    redirectHome($theMsg,'user.php?do=Add');
} else {
    // Insert into the database with this information
    $stmt = $con->prepare("INSERT INTO users (username, email, fullname, password,date,regstatus) VALUES (?, ?, ?, ?,CURDATE(),?)");
    $stmt->execute(array($user, $email, $name, $hashpass,1));

    // Echo success message
    $theMsg = "<div class='alert alert-success text-center'>" . $stmt->rowCount() . " Record insert</div>";
    redirectHome($theMsg,'user.php?do=ManageS');

}
        } else {
            foreach ($formErrors as $error) {
                $theMsg = "<div class='alert alert-danger text-center'>" . $error . "</div>";
                redirectHome($theMsg,'user.php?do=Add');
           
            }
        }
           
        

    } else {
        $theMsg = "<div class='alert alert-danger text-center'>Sorry, you can't browse this page directly.</div>";
        redirectHome($theMsg);
    }
    echo" </div >";

    }elseif($do=='Delete'){
        // delete member page
        echo" <div class='about manage-page'>";

        echo "<h1 class='text-center'>Delete Member</h1>";

        // Check if the 'id' parameter is numeric and get its integer value
        $id = !empty($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : 0;

        // Select all data depending on this 'id'
        $stmt = $con->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");

        // Execute the query
        $stmt->execute(array($id));

        // Check if the id exists
        if ($stmt->rowCount() > 0) {
            $stmt= $con->prepare("DELETE FROM users WHERE id=:userid");
            
            //to bind (lier) the id attribute with userid
            $stmt->bindParam(":userid",$id);
            $stmt->execute();

            $theMsg= "<div class='alert alert-success text-center'>" . $stmt->rowCount() . ' Record deleted</div>';
            redirectHome($theMsg,'user.php?do=ManageS');

        }else {$errorMsg = "<div class='alert alert-danger text-center'>This id is not exist</div>";
            redirectHome($errorMsg,'user.php?do=ManageS');}
           
        echo"</div>";
    }
    

    include $tpl . 'footer.php';
} else {
    header('Location: index.php');
    exit();
}

ob_end_flush();

?>