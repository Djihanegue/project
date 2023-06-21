<?php
ob_start();

session_start();

if (isset($_SESSION['username'])){
    $pageTitle = 'Search Conferences';
    include 'init.php';
?>

<div class="card manage-page">
    <h1 class="text-center">Search Conferences</h1>

    <div class="row justify-content-center"> <!-- Add justify-content-center class to center the row -->
        <?php
        // Check if the search query is present
        if (isset($_GET['search'])) {
            $search = $_GET['search'];

            try {
                $stmt = $con->prepare("SELECT conference.*, users.username, events.nomevent, salle.name, salle.nomville 
                                      FROM conference 
                                      INNER JOIN users ON users.id = conference.id 
                                      INNER JOIN events ON events.idevent = conference.idevent 
                                      LEFT JOIN salle ON salle.idsalle = conference.idsalle
                                      WHERE events.nomevent LIKE :search");
                $stmt->bindValue(':search', '%' . $search . '%');
                $stmt->execute();
                $conferences = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($conferences) > 0) {
                    foreach ($conferences as $conference) {
                        echo '<div class="col-12 col-md-4 speaker">
                                <div class="card text-white text-center bg-dark pb-2">
                                    <div class="card-body">
                                        <h3 class="event-name">' . $conference['nomevent'] . '</h3>
                                        <div class="speaker-info">
                                            <p class="speaker-details">Theme: ' . $conference['theme'] . '<br>
                                            Speaker: ' . $conference['username'] . '<br>
                                            Type: ' . $conference['type'] . '<br>
                                            Date: ' . $conference['date'] . '</p>';

                        if ($conference['type'] == 'presentiel') {
                            echo '<p class="salle-details">Room: ' . $conference['name'] . '<br>
                                  City: ' . $conference['nomville'] . '</p>';
                        } else {
                            echo '<a href="onlineconference.php" class="btn btn-primary">Online</a>';
                        }

                        echo '</div>
                                </div>
                            </div>';
                    }
                } else {
                    echo '<p class="text-center">No conferences found.</p>';
                }
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        } else {
            echo '<p class="text-center">No search query specified.</p>';
        }
        ?>
    </div> 

</div>

<?php
} else {
    header('Location: Login.php');
    exit();
}

ob_end_flush();

?>