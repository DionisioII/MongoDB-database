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
$result = $mysqli->query(' SELECT p.*, g.TipoGiudice FROM  personefisiche AS p 

inner join giudici as g ON p.CodEnte = g.CodEnte'
);

if(!$result) {
    die('Database error: ' . $mysqli->error);
}

$data = array();

while($row = $result->fetch_assoc()) { 
   
   $recapiti = $mysqli->query("SELECT TipoRecapito,Descrizione,Recapito FROM recapiti where CodEnte='".$row['CodEnte']."'");
   if($recapiti)
   {
       $contacts=array();
       while($recapito = $recapiti->fetch_assoc()){
           $contacts[] = $recapito;
       }
       $row['recapiti'] = $contacts;
   }

   $tribunali = $mysqli->query("SELECT t.CodTribunale,t.Luogo FROM tribunali as t
   inner join lavorain as lv ON t.CodTribunale = lv.CodTribunale
    where lv.CodEnte='".$row['CodEnte']."'");
   
   if($tribunali)
   {
       $data_to_add=array();
       while ($tribunale = $tribunali->fetch_assoc()){
           $data_to_add[] = $tribunale;
       }
       $row['tribunali in cui lavora'] = $data_to_add;
   }
   
    $data []= $row;

    
}

echo json_encode($data);



?>
