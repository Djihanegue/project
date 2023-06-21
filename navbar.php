<nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container">
    <a class="navbar-brand" href="dashboard.php"><b><span class="text-warning">My</span>Conference</b></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="dashboard.php"><?php echo lang('accueil')?></a>
        </li>
        <li class="nav-item"><a class="nav-link" href="user.php?do=ManageS"><?php echo lang('addS')?></a></li>
        <li class="nav-item"><a class="nav-link" href="event.php?do=ManageE"><?php echo lang('addE')?></a></li>
        <li class="nav-item"><a class="nav-link" href="room.php?do=ManageR"><?php echo lang('addR')?></a></li>
        <li class="nav-item"><a class="nav-link" href="conference.php?do=ManageC">Conferences</a></li>

      </ul>
      <ul class="navbar-nav ms-auto">
        <li class="nav-item dropdown">
          <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo lang('username')?></a>
          <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="../index.php">Visit Conference</a></li>
                <li><a class="dropdown-item" href="user.php?do=Edit&id=<?php echo $_SESSION['id'] ?>"><?php echo lang('member')?></a></li>
                <li><a class="dropdown-item" href="logout.php"><?php echo lang('deconneter')?></a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>