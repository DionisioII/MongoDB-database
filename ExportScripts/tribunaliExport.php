<?php

$server = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'studio_legale';
$mysqli = new mysqli($server, $user, $pass, $dbname);

if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') '
            . $mysqli->connect_error);
}
$result = $mysqli->query(' SELECT t.* FROM  tribunali AS t' 
    
    
);

if(!$result) {
    die('Database error: ' . $mysqli->error);
}

$data = array();


while($row = $result->fetch_assoc()) { 
   
    $giudici = $mysqli->query("SELECT p.CodEnte,p.Nome,p.Cognome FROM personefisiche as p
                                inner join lavorain as lv ON lv.CodEnte=p.CodEnte
                                 where lv.CodTribunale='".$row['CodTribunale']."'");
    if($giudici)
    {
        $giudiciToAdd = array();
        while($giudice = $giudici->fetch_assoc()){
            $giudiciToAdd[] = $giudice;
        }
        $row['giudici'] = $giudiciToAdd;
        
     }
    
     $data []= $row;
    
}

echo json_encode($data);



?>
