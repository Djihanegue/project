<?php
ob_start();

session_start();

if (isset($_SESSION['username'])) {
    $pageTitle = 'Manage Events';
    include 'init.php';
    if (isset($_GET['do'])) {
        $do = $_GET['do'];
    } else {
        $do = 'ManageE';
    }

    // Start main page
    if ($do == 'ManageE') {
        // Event page
        //select all events 
        $stmt = $con->prepare("SELECT * FROM events");
        $stmt->execute();
        $rows = $stmt->fetchAll();
        ?>

        <div class="about manage-page">
            <h1 class="text-center">Manage Scientific Events</h1>

            <div class="table-responsive">
                <table class="main-table text-center table table-bordered">
                    <tr>

                        <td>Event</td>
                        <td>Information</td>
                        <td>Control</td>
                    </tr>
                    <?php
                    foreach ($rows as $row) {
                        echo "<tr>";
                        echo "<td>".$row['nomevent']."</td>";
                        echo "<td>".$row['information']."</td>";
                        echo "<td>
                            <a href='event.php?do=Edit&idevent=".$row['idevent']."' class='btn btn-success'> Edit</a>
                            <a href='event.php?do=Delete&idevent=".$row['idevent']."' class='btn btn-danger confirm' onclick='return confirmDelete()'> Delete</a>
                        </td>";
                        echo "</tr>";
                    }
                    ?>
                </table>
            </div>
            <a href="event.php?do=Add" class="btn btn-primary"><i class="fa fa-plus"></i> New Event</a>
        </div>

    <?php
    }

    elseif ($do == 'Edit') {
        // Edit page

        // Check if the 'id' parameter is numeric and get its integer value
        $id = !empty($_GET['idevent']) && is_numeric($_GET['idevent']) ? intval($_GET['idevent']) : 0;

        // Select all data depending on this 'id'
        $stmt = $con->prepare("SELECT * FROM events WHERE idevent = ? LIMIT 1");

        // Execute the query
        $stmt->execute(array($id));

        // Fetch the data
        $row = $stmt->fetch();

        // Check if the id exists
        if ($stmt->rowCount() > 0) {
            ?>
            <div class="about manage-page">
                <h1 class="text-center">Edit Event</h1>

                <form class="form-horizontal" action="?do=update" method="POST">
                    <input type="hidden" name="idevent" value="<?php echo $id ?>" />
                    <!-- Start eventname field -->
                    <div class="input-group mb-3 input-group-lg">
                        <span class="input-group-text" id="addon-wrapping">Name</span>
                        <input type="text" value="<?php echo $row['nomevent'] ?? '' ?>" class="form-control" id="validationCustomUsername" name="nomevent" aria-describedby="inputGroupPrepend" required>
                        <div class="invalid-feedback">
                            Name must be at least 3 letters long and contain only letters.
                        </div>
                    </div>
                    <!-- End eventname field -->

                    <!-- Start information field -->
                    <div class="input-group mb-3 input-group-lg">
                        <span class="input-group-text" id="addon-wrapping">Information</span>
                        <input type="text" name="information" value="<?php echo $row['information'] ?? '' ?>" class="form-control" id="validationCustomUsername" aria-describedby="inputGroupPrepend">
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
            $theMsg = "<div class='alert alert-danger text-center'>There is no event with the specified ID.</div>";
            redirectHome($theMsg,'event.php?do=ManageE');
        }
    } elseif ($do == 'update') {
        // Update page
        echo "<div class='about manage-page'>";

        echo "<h1 class='text-center'>Update Scientific Events</h1>";
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Get variables from the form

            $event = $_POST['nomevent'] ?? '';
            $information = $_POST['information'] ?? '';
            $id = $_POST['idevent'] ?? '';

            // Update the database with this information
            $stmt = $con->prepare("UPDATE events SET nomevent=?, information=? WHERE idevent=?");
            $stmt->execute(array($event, $information, $id));

            // Check if any record was updated
            if ($stmt->rowCount() > 0) {
                $theMsg = "<div class='alert alert-success text-center'>Record updated successfully.</div>";
                redirectHome($theMsg,'event.php?do=ManageE');
            } else {
                $theMsg = "<div class='alert alert-danger text-center'>No changes were made.</div>";
                redirectHome($theMsg,'event.php?do=ManageE');
            }
        } else {
            $theMsg = "<div class='alert alert-danger text-center'>Sorry, you can't browse this page directly.</div>";
            redirectHome($theMsg);
        }
        echo "</div>";
    } elseif ($do == 'Add') {
        // Add event page
        ?>
        <div class="about manage-page">
            <h1 class="text-center">Add New Scientific Event</h1>

            <form class="form-horizontal" action="?do=insert" method="POST">

                <!-- Start namevent field -->
                <div class="input-group mb-3 input-group-lg">
                    <span class="input-group-text" id="addon-wrapping">Name</span>
                    <input type="text" class="form-control" id="validationCustomUsername" name="nomevent" aria-describedby="inputGroupPrepend" placeholder="Name of scientific event" required>
                    <div class="valid-feedback">
                        Looks good!
                    </div>
                </div>
                <!-- End nameevent field -->

                <!-- Start information field -->
                <div class="input-group mb-3 input-group-lg">
                    <span class="input-group-text" id="addon-wrapping">Information</span>
                    <input type="text" name="information" class="form-control" id="validationCustomUsername" aria-describedby="inputGroupPrepend" placeholder="Information of scientific event">
                </div>
                <!-- End information field -->

                <!-- Start button field -->
                <div class="col-12">
                    <button class="btn btn-primary" type="submit">Add Event</button>
                </div>
                <!-- End button field -->
            </form>
        </div>
    <?php

    } elseif ($do == 'insert') {
        // Insert page

        echo "<div class='about manage-page'>";
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            echo "<h1 class='text-center'>Insert Scientific Event</h1>";

            // Get variables from the form
            $name = $_POST['nomevent'] ?? '';
            $information = $_POST['information'] ?? '';

            // Check if the eventname already exists
            $stmt = $con->prepare("SELECT * FROM events WHERE nomevent = ?");
            $stmt->execute(array($name));
            if ($stmt->rowCount() > 0) {
                $theMsg = "<div class='alert alert-danger text-center'>Event name already exists. Please choose a different name.</div>";
                redirectHome($theMsg,'event.php?do=Add');
            } else {
                // Insert into the database with this information
                $stmt = $con->prepare("INSERT INTO events (nomevent,information) VALUES (?, ?)");
                $stmt->execute(array($name, $information));

                // Echo success message
                $theMsg = "<div class='alert alert-success text-center'>" . $stmt->rowCount() . " Record inserted</div>";
                redirectHome($theMsg,'event.php?do=ManageE');
            }
        } else {
            $theMsg = "<div class='alert alert-danger text-center'>Sorry, you can't browse this page directly.</div>";
            redirectHome($theMsg);
        }
        echo "</div>";

    } elseif ($do == 'Delete') {
        // Delete event page
        echo "<div class='about manage-page'>";

        echo "<h1 class='text-center'>Delete Scientific Event</h1>";

        // Check if the 'id' parameter is numeric and get its integer value
        $id = !empty($_GET['idevent']) && is_numeric($_GET['idevent']) ? intval($_GET['idevent']) : 0;

        // Select all data depending on this 'id'
        $stmt = $con->prepare("SELECT * FROM events WHERE idevent = ? LIMIT 1");

        // Execute the query
        $stmt->execute(array($id));

        // Check if the id exists
        if ($stmt->rowCount() > 0) {
            $stmt = $con->prepare("DELETE FROM events WHERE idevent=:id");

            // Bind the id attribute with idevent
            $stmt->bindParam(":id", $id);
            $stmt->execute();

            $theMsg = "<div class='alert alert-success text-center'>" . $stmt->rowCount() . ' Record deleted</div>';
            redirectHome($theMsg,'event.php?do=ManageE');

        } else {
            $theMsg = "<div class='alert alert-danger text-center'>This id does not exist</div>";
            redirectHome($theMsg,'event.php?do=ManageE');
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