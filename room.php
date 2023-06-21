<?php

ob_start();

session_start();

if (isset($_SESSION['username'])) {
    $pageTitle = 'Manage Room';
    include 'init.php';
    if (isset($_GET['do'])) {
        $do = $_GET['do'];
    } else {
        $do = 'ManageR';
    }

    // Start main page
    if ($do == 'ManageR') {
        // Event page
        //select all events 
        $stmt = $con->prepare("SELECT * FROM salle");
$stmt->execute();
$rows = $stmt->fetchAll();
?>

<div class="about manage-page">
    <h1 class="text-center">Manage Rooms</h1>

    <div class="table-responsive">
        <table class="main-table text-center table table-bordered">
            <tr>
                <td>Name</td>
                <td>City</td>
                <td>Information</td>
                <td>Control</td>
            </tr>
            <?php
            foreach ($rows as $row) {
    echo "<tr>";
    echo "<td>".$row['name']."</td>";
    echo "<td>".$row['nomville']."</td>";
    echo "<td>".$row['information']."</td>";
    echo "<td>
        <a href='room.php?do=Edit&idsalle=".$row['idsalle']."' class='btn btn-success'> Edit</a>
        <a href='room.php?do=Delete&idsalle=".$row['idsalle']."' class='btn btn-danger confirm' onclick='return confirmDelete()'> Delete</a>
        
    </td>";
    echo "</tr>";
}
            ?>
        </table>
    </div>
    <a href="room.php?do=Add" class="btn btn-primary"><i class="fa fa-plus"></i> new room</a>
</div>
    
    <?php
    }

    elseif ($do == 'Edit') {
        // Edit page
    
        // Check if the 'idsalle' parameter is numeric and get its integer value
        $id = !empty($_GET['idsalle']) && is_numeric($_GET['idsalle']) ? intval($_GET['idsalle']) : 0;
    
        // Select all data depending on this 'idsalle'
        $stmt = $con->prepare("SELECT * FROM salle WHERE idsalle = ? LIMIT 1");
    
        // Execute the query
        $stmt->execute(array($id));
    
        // Fetch the data
        $row = $stmt->fetch();
    
        // Check if the id exists
        if ($stmt->rowCount() > 0) {
            ?>
            <div class="about manage-page">
            <h1 class="text-center">Edit Room</h1>
    
            <form class="form-horizontal" action="?do=update" method="POST">
                <input type="hidden" name="idsalle" value="<?php echo $id ?>" />
                <!-- Start roomname field -->
                <div class="input-group mb-3 input-group-lg">
                    <span class="input-group-text" id="addon-wrapping">Name</span>
                    <input type="text" value="<?php echo $row['name'] ?? '' ?>" class="form-control" id="validationCustomUsername" name="name" aria-describedby="inputGroupPrepend" required>
                    <div class="invalid-feedback">
                        name must be at least 3 letters long and free of numbers.
                    </div>
                </div>
                <!-- End roomname field -->
    
                <!-- Start Cityname field -->
                <div class="input-group mb-3 input-group-lg">
                    <span class="input-group-text" id="addon-wrapping">City</span>
                    <select class="form-control" name="nomville">
                        <?php
                        // Récupérer les données de la table "ville"
                        $stmt = $con->prepare("SELECT nomville FROM ville");
                        $stmt->execute();
                        $villeData = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
                        // Parcourir les données et créer les options du menu déroulant
                        foreach ($villeData as $villeOption) {
                            echo "<option value='" . $villeOption . "'>" . $villeOption . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <!-- End Cityname field -->
    
                <!-- Start information field -->
                <div class="input-group mb-3 input-group-lg">
                    <span class="input-group-text" id="addon-wrapping">information</span>
                    <input type="text" name="information" value="<?php echo $row['information'] ?? '' ?>" class="form-control" id="validationCustomUsername" aria-describedby="inputGroupPrepend" >
    
                </div>
                <!-- End information field -->
    
                <!-- Start button field -->
                <div class="col-12">
                    <button class="btn btn-primary" type="submit">Save</button>
                </div>
                <!-- End button field -->
            </form>
            </div>
            <?php
        } else {
            $theMsg = "<div class='alert alert-danger text-center'>There is no room with the specified Name.</div>";
            redirectHome($theMsg,'room.php?do=ManageR');
    
        }
    } elseif ($do == 'update') {
        // Update page
        echo "<div class='about manage-page'>";
        echo "<h1 class='text-center'>Update Rooms</h1>";
    
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Get variables from the form
            $id = $_POST['idsalle'] ?? '';
            $name = $_POST['name'] ?? '';
            $city = $_POST['nomville'] ?? '';
            $information = $_POST['information'] ?? '';
    
            $stmt = $con->prepare("SELECT * FROM salle WHERE name=? AND idsalle!=?");
            $stmt->execute(array($name, $id));
            $count = $stmt->rowCount();
            if ($count == 1) {
                $theMsg = "<div class='alert alert-danger text-center'>Sorry, this room name already exists.</div>";
                    redirectHome($theMsg, 'back');
       
            } else {
                // Update the database with this information
                $stmt = $con->prepare("UPDATE salle SET name=?, nomville=?, information=? WHERE idsalle=?");
                $stmt->execute(array($name, $city, $information, $id));
    
                // Check if any record was updated
                if ($stmt->rowCount() > 0) {
                    $theMsg = "<div class='alert alert-success text-center'>Record updated successfully.</div>";
                    redirectHome($theMsg, 'room.php?do=ManageR');
                } else {
                    $theMsg = "<div class='alert alert-danger text-center'>No changes were made.</div>";
                    redirectHome($theMsg, 'back');
                }
            }
        } else {
            
            $theMsg = "<div class='alert alert-danger text-center'>Sorry, you can't browse this page directly.</div>";
            redirectHome($theMsg);
        }
        echo "</div>";
    }
     elseif($do=='Add'){
        //add members page
       
        ?>
        <div class="about manage-page">
        <h1 class="text-center">Add new room</h1>
        
            <form class="form-horizontal" action="?do=insert" method="POST">
                
                <!-- Start nameroom field -->
                <div class="input-group mb-3 input-group-lg">
                    <span class="input-group-text" id="addon-wrapping">Name</span>
                    <input type="text" class="form-control" id="validationCustomUsername" name="name" aria-describedby="inputGroupPrepend" placeholder="room name " required>
                    <div class="valid-feedback">
                        Looks good!.
                    </div>
                </div>
                <!-- End nameroom field -->

               <!-- Start city field -->
            <div class="input-group mb-3 input-group-lg">
                <span class="input-group-text" id="addon-wrapping">City</span>
                <select class="form-control" name="nomville">
                    <?php
                    // Retrieve data from the "ville" table
                    $stmt = $con->prepare("SELECT nomville FROM ville");
                    $stmt->execute();
                    $villeData = $stmt->fetchAll(PDO::FETCH_COLUMN);

                    // Loop through the data and create option tags
                    foreach ($villeData as $villeOption) {
                        echo "<option value='" . $villeOption . "'>" . $villeOption . "</option>";
                    }
                    ?>
                </select>
            </div>
            <!-- End city field -->

                <!-- Start information field -->
                <div class="input-group mb-3 input-group-lg">
                    <span class="input-group-text" id="addon-wrapping">Information</span>
                    <input type="text" name="information"  class="form-control" id="validationCustomUsername"  aria-describedby="inputGroupPrepend" placeholder="room information " >
                    
                </div>
                <!-- End information field -->
 
                <!-- Start button field -->
                <div class="col-12">
                    <button class="btn btn-primary" type="submit">Add room</button>
                </div>
                <!-- End button field -->
            </form>
        </div>
    <?php
 
     }

     elseif($do=='insert'){
        // Insert page
      
        echo" <div class='about manage-page'>";
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
          echo "<h1 class='text-center'>Insert room</h1>";
  
          // Get variables from the form
          $name = $_POST['name'] ?? '';
          $ville = $_POST['nomville'] ?? '';
          $information = $_POST['information'] ?? '';
  
            // Check if the roomname already exists
  $stmt = $con->prepare("SELECT * FROM salle WHERE name = ?");
  $stmt->execute(array($name));
  if ($stmt->rowCount() > 0) {
      $theMsg= "<div class='alert alert-danger text-center'>room name already exists. Please choose a different name.</div>";
      redirectHome($theMsg,'room.php?do=Add');
  } else {
      // Insert into the database with this information
      $stmt = $con->prepare("INSERT INTO salle (name,nomville,information) VALUES (?,?,?)");
      $stmt->execute(array($name,$ville, $information));
  
      // Echo success message
      $theMsg = "<div class='alert alert-success text-center'>" . $stmt->rowCount() . " Record inserted</div>";
      redirectHome($theMsg,'room.php?do=ManageR');
  
  }
           
      } else {
          $theMsg = "<div class='alert alert-danger text-center'>Sorry, you can't browse this page directly.</div>";
          redirectHome($theMsg);
      }
      echo" </div >";
  
      }

      elseif($do=='Delete'){
        // delete event page
        echo" <div class='about manage-page'>";

        echo "<h1 class='text-center'>Delete Room</h1>";

        // Check if the 'id' parameter is numeric and get its integer value
        $id = !empty($_GET['idsalle']) && is_numeric($_GET['idsalle']) ? intval($_GET['idsalle']) : 0;

        // Select all data depending on this 'id'
        $stmt = $con->prepare("SELECT * FROM salle WHERE idsalle = ? LIMIT 1");

        // Execute the query
        $stmt->execute(array($id));

        // Check if the id exists
        if ($stmt->rowCount() > 0) {
            $stmt= $con->prepare("DELETE FROM salle WHERE idsalle=:id");
            
            //to bind (lier) the id attribute with userid
            $stmt->bindParam(":id",$id);
            $stmt->execute();

            $theMsg = "<div class='alert alert-success text-center'>" . $stmt->rowCount() . ' Record deleted</div>';
            redirectHome($theMsg,'room.php?do=ManageR');

        }else {$theMsg = "<div class='alert alert-danger text-center'>This id is not exist</div>";
            redirectHome($theMsg,'room.php?do=ManageR');}
           
        echo"</div>";
    }
    

  
    include $tpl . 'footer.php';
} else {
    header('Location: index.php');
    exit();
}

ob_end_flush();

?>