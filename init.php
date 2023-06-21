<?php
include 'dbconnnecte.php';
//routes
$tpl ='includes/tampletes/';
$css ='layout/css/';
$js = 'layout/js/';
$lang='includes/language/';
$func='includes/fonctions/';

//Include the important file

include $func.'functions.php';
include $lang.'english.php';

include $tpl.'header.php';

//include navbar on all pagers expect the one with $navbar vairable
if (!isset($nonavbar)){include $tpl.'navbar.php';}
