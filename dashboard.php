<?php
   ob_start(); //output buffering start

session_start();
if(isset($_SESSION['username'])){
    $pageTitle='Dashboard';
    include 'init.php';
?>
<div class="contain text-center">
    <b><h1>Dashboard</h1></b>
    
    <div class="row justify-content-center">
        <div class="col-md-3">
            <div class="stat card text-white  speaker">
                Total Speakers
                <span><a href="user.php?do=ManageS"><?php echo checkconf('regstatus','users',1) ?></a></span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat card text-white  event">
                Total Events
                <span><a href="event.php?do=ManageE"><?php echo countconfs('events', 'idevent'); ?></a></span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat card text-white room">
                Total Rooms
                <span><span><a href="room.php?do=ManageR"><?php echo countconfs('salle', 'idsalle'); ?></a></span></span>
            </div>
        </div>
</div>
</div>


<?php
include $tpl.'footer.php';
} else {
    header('Location: index.php');
    exit();
}

   ob_end_flush();

?>