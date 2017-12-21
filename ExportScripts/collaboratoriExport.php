<?php

$server = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'studio_legale';
$mysqli = new mysqli($server, $user, $pass, $dbname);
//$con = mysql_connect($server, $user, $pass) or die("Can't connect");
//mysql_select_db($dbname);
if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') '
            . $mysqli->connect_error);
}
$result = $mysqli->query(' SELECT p.*,c.Qualifica,c.IdBadge FROM  personefisiche AS p 
    
    inner join collaboratori as c ON p.CodEnte = c.CodEnte'
);

if(!$result) {
    die('Database error: ' . $mysqli->error);
}

$data = array();
// use fetch_array instead of fetch_assoc as the column
while($row = $result->fetch_assoc()) { 
   
    $recapiti = $mysqli->query("SELECT TipoRecapito,Descrizione,Recapito FROM recapiti where CodEnte='".$row['CodEnte']."'");
    if($recapiti)
    {
        $recapitiToAdd = array();
        while($recapito = $recapiti->fetch_assoc()){
            $recapitiToAdd[] = $recapito;
        }
        $row['recapiti'] = $recapitiToAdd;
        
     }
    
     $data []= $row;
    
}

echo json_encode($data);



?>
