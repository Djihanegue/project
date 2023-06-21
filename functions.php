<?php
/*Title function that echo the page title*/
function getTitle(){
    global $pageTitle;
    
    if(isset($pageTitle))
    {
        echo $pageTitle;
    }
    else{echo 'Default';}

}

/*home redirect function */

function redirectHome($theMsg,$url=null,$seconds = 10) {
    if($url===null){
        $url='index.php';

    }else{
        if(isset($_SERVER['HTTP_REFERER'])&& $_SERVER['HTTP_REFERER']!==''){
             
            $url=$_SERVER['HTTP_REFERER'];
        }else{
            $url='index.php';
        }
             
    }
    echo $theMsg;
    echo "<div class='alert alert-info'>You will be return directly after $seconds seconds.</div>";
    header("refresh: $seconds; url=$url");
    exit();
}


    /*
    *get lastest records function 
    *function to get latest items from database[users,events,rooms]
    *$select=field to select
    *$table= the table to choose from
     */
    function getLatest($select,$table,$order){
       
        global $con;
        $getStmt=$con->prepare("SELECT $select FROM $table ORDER BY $order DESC ");
        $getStmt->execute();
        $rows= $getStmt->fetchAll();

       return $rows;
    }
    
    
    function countconfs($from, $select)
{
    global $con;
    $statement = $con->prepare("SELECT COUNT($select) FROM $from");
    $statement->execute();
    $count = $statement->fetchColumn();
    return $count;
}

    function checkconf($select, $from, $value) {
        global $con;
        $statement = $con->prepare("SELECT $select FROM $from WHERE $select = ?");
        $statement->execute([$value]);
        $count = $statement->rowCount();
        return $count;
    }

    

    