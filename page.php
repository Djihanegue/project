<?php
//if no order

$do ='';
if(isset($_GET['do'])){
    $do = $_GET ['do'];
}else{
    $do = 'dashboard';
}

//if the page is main page

if ($do == 'dashboard')
{

    header('Location:dashboard.php');

}else{
    if($do == 'Edit')
    {

    }
    else{
        if($do == 'about'){

        }else{
            if($do == 'ManageS'){

           }else{
            if($do == 'event'){

            }else{
                if($do == 'room'){

                }}}
}}}