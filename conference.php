<?php
ob_start();

session_start();

if (isset($_SESSION['username'])) {
    $pageTitle = 'Manage Conference';
    include 'init.php';
    if (isset($_GET['do'])) {
        $do = $_GET['do'];
    } else {
        $do = 'ManageC';
    }

    // Start main page
    if ($do == 'ManageC') {
       
        $stmt = $con->prepare("SELECT conference.*, users.username, events.nomevent, salle.name, salle.nomville 
        FROM conference 
        INNER JOIN users ON users.id = conference.id 
        INNER JOIN events ON events.idevent = conference.idevent 
        LEFT JOIN salle ON salle.idsalle = conference.idsalle");
        $stmt->execute();
        $confs = $stmt->fetchAll();
        ?>

        <div class="about manage-page">
            <h1 class="text-center">Manage Conference</h1>

            <div class="table-responsive">
                <table class="main-table text-center table table-bordered">
                <tr>
                               <td>ID</td>
                               <td>Theme</td>
                               <td>Date</td>
                               <td>Type</td>
                               <td>Room</td>
                               <td>Event</td>
                               <td>Speaker</td>
                               <td>Control</td>
                </tr>
                    <?php
                    foreach ($confs as $conf) {
                        echo "<tr>";
                        echo "<td>".$conf['idconf']."</td>";
                        echo "<td>".$conf['theme']."</td>";
                        echo "<td>".$conf['date']."</td>";
                        echo "<td>".$conf['type']."</td>";
                        echo "<td>".$conf['idsalle']."</td>";
                        echo "<td>".$conf['idevent']."</td>";
                        echo "<td>".$conf['id']."</td>";
                        echo "<td>
                            <a href='conference.php?do=Edit&idc=".$conf['idconf']."' class='btn btn-success'> Edit</a>
                            <a href='conference.php?do=Delete&idc=".$conf['idconf']."' class='btn btn-danger confirm' onclick='return confirmDelete()'> Delete</a>
                        </td>";
                        echo "</tr>";
                    }
                    ?>
                </table>
            </div>
            <a href="conference.php?do=Add" class="btn btn-primary"><i class="fa fa-plus"></i> New Conference</a>
        </div>

    <?php
    }

    elseif ($do == 'Edit') {
        // Edit page
    
        // Check if the 'id' parameter is numeric and get its integer value
        $id = !empty($_GET['idc']) && is_numeric($_GET['idc']) ? intval($_GET['idc']) : 0;
    
        // Select all data depending on this 'id'
        $stmt = $con->prepare("SELECT * FROM conference WHERE idconf = ? LIMIT 1");
    
        // Execute the query
        $stmt->execute(array($id));
    
        // Fetch the data
        $conf = $stmt->fetch();
    
        // Check if the id exists
        if ($stmt->rowCount() > 0) {
            ?>
            <div class="about manage-page">
                <h1 class="text-center">Edit Conference</h1>
    
                <form class="form-horizontal" action="?do=update" method="POST">
                    <input type="hidden" name="idconf" value="<?php echo $id ?>" />
                    <!-- Start nameconference field -->
                    <div class="input-group mb-3 input-group-lg">
                        <span class="input-group-text" id="addon-wrapping">Name</span>
                        <select class="form-select" name="event" aria-label="Select event" required>
                            <option selected disabled>Event name...</option>
                            <?php
                            $stmt = $con->prepare("SELECT * FROM events");
                            $stmt->execute();
                            $events = $stmt->fetchAll();
                            foreach ($events as $event) {
                                $selected = ($conf['idevent'] == $event['idevent']) ? 'selected' : '';
                                echo "<option value='" . $event['idevent'] . "' $selected>" . $event['nomevent'] . "</option>";
                            }
                            ?>
    
                        </select>
                        <div class="valid-feedback">
                            Looks good!
                        </div>
                    </div>
                    <!-- End nameconference field -->
    
                    <!-- Start theme field -->
                    <div class="input-group mb-3 input-group-lg">
                        <span class="input-group-text" id="addon-wrapping">Theme</span>
                        <input type="text" class="form-control" id="validationCustomConferenceTheme" name="theme" aria-describedby="inputGroupPrepend" placeholder="Theme of the conference" value="<?php echo $conf['theme'] ?>" required>
                        <div class="valid-feedback">
                            Looks good!
                        </div>
                    </div>
                    <!-- End theme field -->
    
                    <!-- Start date field -->
                    <div class="input-group mb-3 input-group-lg">
                        <span class="input-group-text" id="addon-wrapping">Date</span>
                        <?php
                        $dateValue = isset($conf['date']) ? date('Y-m-d', strtotime($conf['date'])) : '';
                        ?>
                        <input type="text" class="form-control" id="validationCustomConferenceDate" name="date" aria-describedby="inputGroupPrepend" value="<?php echo $dateValue; ?>" placeholder="YYYY-MM-DD" required pattern="\d{4}-\d{2}-\d{2}">
                        <div class="valid-feedback">
                            Looks good!
                        </div>
                    </div>
                    <!-- End date field -->
    
                   <!-- Start type field -->
<div class="input-group mb-3 input-group-lg">
    <span class="input-group-text" id="addon-wrapping">Type</span>
    <select class="form-select" name="type" aria-label="Select conference type" onchange="updateRoomOptions(this)" required>
        <option selected disabled>Choose...</option>
        <option value="presential" <?php if (isset($conf['Type']) && $conf['Type'] == 'presential') {
            echo 'selected';
        } ?>>Presential</option>
        <option value="online" <?php if (isset($conf['Type']) && $conf['Type'] == 'Online') {
            echo 'selected';
        } ?>>Online</option>
    </select>
    <div class="valid-feedback">
        Looks good!
    </div>
    <div class="invalid-feedback">
        Please select a conference type.
    </div>
</div>
<!-- End type field -->

<script>
    // Validation function
    function validateTypeField() {
        var selectElement = document.querySelector('select[name="type"]');
        var selectedValue = selectElement.value;

        if (selectedValue === "Choose...") {
            selectElement.setCustomValidity("Please select a conference type.");
        } else {
            selectElement.setCustomValidity("");
        }
    }

    // Add event listener for validation
    var selectField = document.querySelector('select[name="type"]');
    selectField.addEventListener('change', validateTypeField);
</script>
    
                    <!-- Start nameroom field -->
                    <div class="input-group mb-3 input-group-lg">
                        <span class="input-group-text" id="addon-wrapping">Room</span>
                        <select class="form-select" name="salle" aria-label="Conference room" >
                            <option selected disabled>Choose a conference room...</option>
                            <?php
                            $stmt2 = $con->prepare("SELECT * FROM salle");
                            $stmt2->execute();
                            $rooms = $stmt2->fetchAll();
                            foreach ($rooms as $room) {
                                echo "<option value='" . $room['idsalle'] . "'>" . $room['name'] . "</option>";
                            }
                            ?>
                        </select>
                        <div class="valid-feedback">
                            Looks good!
                        </div>
              
                    </div>
                    <!-- End nameroom field -->
    
                    <!-- Start nameSpeaker field -->
                    <div class="input-group mb-3 input-group-lg">
                        <span class="input-group-text" id="addon-wrapping">Speaker</span>
                        <select class="form-select" name="user" aria-label="Speakers" required>
                            <option selected disabled>Speaker name...</option>
                            <?php
                            $stmt3 = $con->prepare("SELECT * FROM users");
                            $stmt3->execute();
                            $users = $stmt3->fetchAll();
                            foreach ($users as $user) {
                                $selected = ($conf['id'] == $user['id']) ? 'selected' : '';
                                echo "<option value='" . $user['id'] . "' $selected>" . $user['username'] . "</option>";
                            }
                            ?>
                        </select>
                        <div class="valid-feedback">
                            Looks good!
                        </div>
                    </div>
                    <!-- End nameconference field -->
    
                    <!-- Start button field -->
                    <div class="col-12">
                        <button class="btn btn-primary btn-sm" type="submit">Save conference</button>
                    </div>
                    <!-- End button field -->
                </form>
            </div>
    
            <script>
                function updateRoomOptions(select) {
                    var roomSelect = document.querySelector('select[name="salle"]');
                    roomSelect.innerHTML = ''; // Clear previous options
    
                    if (select.value.toLowerCase() === 'online') { // Case-insensitive comparison
                        // Add a disabled option for online conferences
                        var option = document.createElement('option');
                        option.value = '';
                        option.text = 'Online conference';
                        roomSelect.appendChild(option);
                        roomSelect.disabled = true; // Disable the room select
                    } else {
                        roomSelect.disabled = false; // Enable the room select
    
                        // Populate options from the database for other types
                        <?php
                        foreach ($rooms as $room) {
                            echo "var option = document.createElement('option');";
                            echo "option.value = '" . $room['idsalle'] . "';";
                            echo "option.text = '" . $room['name'] . "';";
                            echo "roomSelect.appendChild(option);";
                        }
                        ?>
                    }
                }
            </script>
            <?php
        } else {
            $theMsg = "<div class='alert alert-danger text-center'>There is no Conference with the specified ID.</div>";
            redirectHome($theMsg, 'conference.php?do=ManageC');
        }
    }
    
    elseif ($do == 'update') {
        // Update page
        echo "<div class='about manage-page'>";
        echo "<h1 class='text-center'>Update Conference</h1>";
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Get variables from the form
            $name = $_POST['event'] ?? '';
            $theme = $_POST['theme'] ?? '';
            $date = $_POST['date'] ?? '';
            $type = $_POST['type'] ?? '';
            $salle = $_POST['salle'] ?? '';
            $username = $_POST['user'] ?? '';
            $id = $_POST['idconf'] ?? '';
            
            // Check if the required fields are filled
            if (empty($type) || ($type === 'présential' && empty($salle))) {
                $theMsg = "<div class='alert alert-danger text-center'>Please select a conference type and a conference room.</div>";
                redirectHome($theMsg, 'conference.php?do=ManageC');
            }
            
            $idsalle = ($type === 'présential') ? $salle : NULL;
            
            // Update the database with this information
            $stmt = $con->prepare("UPDATE conference SET idevent=?, theme=?, date=?, type=?, idsalle=?,id=? WHERE idconf=?");
            $stmt->execute(array($name, $theme, $date, $type, $idsalle,$username, $id));
    
            // Check if any record was updated
            if ($stmt->rowCount() > 0) {
                $theMsg = "<div class='alert alert-success text-center'>Record updated successfully.</div>";
                redirectHome($theMsg, 'conference.php?do=ManageC');
            } else {
                $theMsg = "<div class='alert alert-danger text-center'>No changes were made.</div>";
                redirectHome($theMsg, 'conference.php?do=ManageC');
            }
        } else {
            $theMsg = "<div class='alert alert-danger text-center'>Sorry, you can't browse this page directly.</div>";
            redirectHome($theMsg);
        }
        echo "</div>";
    }elseif ($do == 'Add') {
    // Add event page
    ?>
    <div class="about manage-page">
        <h1 class="text-center">Add New Conference</h1>

        <form class="form-horizontal" action="?do=insert" method="POST">

            <!-- Start nameconference field -->
            <div class="input-group mb-3 input-group-lg">
                <span class="input-group-text" id="addon-wrapping">Name</span>
                <select class="form-select" name="event" aria-label="Select event" required>
                    <option selected disabled>Select event name...</option>
                    <?php
                    $stmt = $con->prepare("SELECT * FROM events");
                    $stmt->execute();
                    $events = $stmt->fetchAll();
                    foreach ($events as $event) {
                        echo "<option value='" . $event['idevent'] . "'>" . $event['nomevent'] . "</option>";
                    }
                    ?>

                </select>
                <div class="valid-feedback">
                    Looks good!
                </div>
            </div>
            <!-- End nameconference field -->

            <!-- Start theme field -->
            <div class="input-group mb-3 input-group-lg">
                <span class="input-group-text" id="addon-wrapping">Theme</span>
                <input type="text" class="form-control" id="validationCustomConferenceTheme" name="theme" aria-describedby="inputGroupPrepend" placeholder="Theme of the conference" required>
                <div class="valid-feedback">
                    Looks good!
                </div>
            </div>
            <!-- End theme field -->

           <!-- Start date field -->
            <div class="input-group mb-3 input-group-lg">
                <span class="input-group-text" id="addon-wrapping">Date</span>
                <input type="text" class="form-control" id="validationCustomConferenceDate" name="date" aria-describedby="inputGroupPrepend" placeholder="YYYY-MM-DD" required pattern="\d{4}-\d{2}-\d{2}">
                <div class="valid-feedback">
                    Looks good!
                </div>
            </div>
            <!-- End date field -->

            <!-- Start type field -->
            <div class="input-group mb-3 input-group-lg">
                <span class="input-group-text" id="addon-wrapping">Type</span>
                <select class="form-select" name="type" aria-label="Select conference type" required onchange="updateRoomOptions(this)">
                    <option selected disabled>Choose...</option>
                    <option value="presential">Presential</option>
                    <option value="online">Online</option>
                </select>
                <div class="valid-feedback">
                    Looks good!
                </div>
            </div>
            <!-- End type field -->

          <!-- Start nameroom field -->
<div class="input-group mb-3 input-group-lg">
    <span class="input-group-text" id="addon-wrapping">Room</span>
    <select class="form-select" name="salle" aria-label="Select conference room"<?php if ($type === 'online') echo ' disabled'; ?>>
        <option selected disabled>Select conference room...</option>
        <!-- Options will be dynamically populated based on the selected type -->
    </select>
    <input type="hidden" name="idsalle" value="<?php if ($type === 'online') echo ''; ?>">
    <div class="valid-feedback">
        Looks good!
    </div>
</div>
<!-- End nameroom field -->

            <!-- Start nameconference field -->
            <div class="input-group mb-3 input-group-lg">
                <span class="input-group-text" id="addon-wrapping">Speaker</span>
                <select class="form-select" name="user" aria-label="Select Speakers" required>
                    <option selected disabled>Select speaker name...</option>
                    <?php
                    $stmt3 = $con->prepare("SELECT * FROM users");
                    $stmt3->execute();
                    $users = $stmt3->fetchAll();
                    foreach ($users as $user) {
                        echo "<option value='" . $user['id'] . "'>" . $user['username'] . "</option>";
                    }
                    ?>

                </select>
                <div class="valid-feedback">
                    Looks good!
                </div>
            </div>
            <!-- End nameconference field -->

            <!-- Start button field -->
            <div class="col-12">
                <button class="btn btn-primary btn-sm" type="submit">Add Conference</button>
            </div>
            <!-- End button field -->
        </form>
    </div>

    <script>
        function updateRoomOptions(select) {
            var roomSelect = document.querySelector('select[name="salle"]');
            roomSelect.innerHTML = ''; // Clear previous options

            if (select.value.toLowerCase() === 'online') { // Case-insensitive comparison
                // Add a disabled option for online conferences
                var option = document.createElement('option');
                option.value = '';
                option.text = 'Online conference';
                roomSelect.appendChild(option);
                roomSelect.disabled = true; // Disable the room select
            } else {
                roomSelect.disabled = false; // Enable the room select

                // Populate options from the database for other types
                <?php
                $stmt2 = $con->prepare("SELECT * FROM salle");
                $stmt2->execute();
                $rooms = $stmt2->fetchAll();
                foreach ($rooms as $room) {
                    echo "var option = document.createElement('option');";
                    echo "option.value = '" . $room['idsalle'] . "';";
                    echo "option.text = '" . $room['name'] . "';";
                    echo "roomSelect.appendChild(option);";
                }
                ?>
            }
        }
    </script>
    <?php
}
    
elseif ($do == 'insert') {
    // Insert page

    echo "<div class='about manage-page'>";
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        echo "<h1 class='text-center'>Insert Conference</h1>";

        // Get variables from the form
        $name = $_POST['event'] ?? '';
        $theme = $_POST['theme'] ?? '';
        $date = $_POST['date'] ?? '';
        $type = $_POST['type'] ?? '';
        $salle = $_POST['salle'] ?? '';
        $username = $_POST['user'] ?? '';
        $id = $_POST['idconf'] ?? '';
        
          // Check if the required fields are filled
          if (empty($type) || ($type === 'présential' && empty($salle))) {
            $theMsg = "<div class='alert alert-danger text-center'>Please select a conference type and a conference room.</div>";
            redirectHome($theMsg, 'conference.php?do=ManageC');
        }
        
        $idsalle = ($type === 'présential') ? $salle : NULL;

        // Check if the conference already exists
        $stmt = $con->prepare("SELECT * FROM conference WHERE idconf = ?");
        $stmt->execute(array($id));
        if ($stmt->rowCount() > 0) {
            $theMsg = "<div class='alert alert-danger text-center'>This conference already exists. Please choose a different conference.</div>";
            redirectHome($theMsg, 'conference.php?do=Add');
        } else {
            // Insert into the database with this information
            $stmt = $con->prepare("INSERT INTO conference (idevent, theme, date, type, id, idsalle) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute(array($name, $theme, $date, $type, $username, $idsalle));

            // Echo success message
            $theMsg = "<div class='alert alert-success text-center'>" . $stmt->rowCount() . " Record inserted</div>";
            redirectHome($theMsg, 'conference.php?do=ManageC');
        }
    } else {
        $theMsg = "<div class='alert alert-danger text-center'>Sorry, you can't browse this page directly.</div>";
        redirectHome($theMsg);
    }
    echo "</div>";
}
    elseif ($do == 'Delete') {
        // Delete  page
        echo "<div class='about manage-page'>";
    
        echo "<h1 class='text-center'>Delete Conference</h1>";
    
       // Check if the 'id' parameter is numeric and get its integer value
       $id = !empty($_GET['idc']) && is_numeric($_GET['idc']) ? intval($_GET['idc']) : 0;

       // Select all data depending on this 'id'
       $stmt = $con->prepare("SELECT * FROM conference WHERE idconf = ? LIMIT 1");

       // Execute the query
       $stmt->execute(array($id));

    
        // Check if the id exists
        if ($stmt->rowCount() > 0) {
            $stmt = $con->prepare("DELETE FROM `conference` WHERE `conference`.`idconf` = :id");
    
            // Bind the id attribute
            $stmt->bindParam(":id", $id);
            $stmt->execute();
    
            $theMsg = "<div class='alert alert-success text-center'>" . $stmt->rowCount() . ' Record deleted</div>';
            redirectHome($theMsg, 'conference.php?do=ManageC');
        } else {
            $theMsg = "<div class='alert alert-danger text-center'>This id does not exist</div>";
            redirectHome($theMsg, 'conference.php?do=ManageC');
        }
        echo "</div>";
    }

    include $tpl . 'footer.php';
} else {
    header('Location: index.php');
    exit();
}

ob_end_flush();

?>