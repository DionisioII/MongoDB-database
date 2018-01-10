
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
$result = $mysqli->query(' SELECT i.* FROM  societa AS i' 


);

if(!$result) {
    die('Database error: ' . $mysqli->error);
}

$data = array();

while($row = $result->fetch_assoc()) { 
   
   $recapiti = $mysqli->query("SELECT TipoRecapito,Descrizione,Recapito FROM recapiti where CodEnte='".$row['CodEnte']."'");
   if($recapiti)
   {
       $recapitiToAdd= array();
       while($recapito = $recapiti->fetch_assoc()){
           $recapitiToAdd[] = $recapito;
       }
       $row['recapiti'] = $recapitiToAdd;
       
    }
   
    $data []= $row;

    
}

echo json_encode($data);



?>
