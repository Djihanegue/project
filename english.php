<?php
function lang( $phrase ){
    static $lang = array (
        //dashboard word
        'accueil'   =>'Home',
        'a propos' =>'About',
        'participant' =>'Speakers',
        'conferences' =>'Conferences',
        'salles' =>'Rooms',
        'username'  =>'Djihane',
        'member'   =>'Edite Mambers',
        'gerer'=>'Manage',
        'addS'=>'Manage Speakrs',
        'addE'=>'Manage Events',
        'addR'=>'Manage Rooms',
        'parametter'=>'Settings',
        'deconneter' =>'Logout',
        );
        return $lang[$phrase];
}