<?php

$server = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'studio_legale_big';
$mysqli = new mysqli($server, $user, $pass, $dbname);
//$con = mysql_connect($server, $user, $pass) or die("Can't connect");
//mysql_select_db($dbname);
if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') '
            . $mysqli->connect_error);
}
$result = $mysqli->query(' SELECT c.*  FROM  cause AS c '
);

if(!$result) {
    die('Database error: ' . $mysqli->error);
}

$data = array();

while($row = $result->fetch_assoc()) { 

   // per ogni causa aggiungiamo il tribunale o i tribunali

   $tribunali = $mysqli->query("SELECT CodTribunale,Luogo as luogoTribunale,TipoTribunale FROM tribunali where CodTribunale='".$row['CodTribunale']."'");
   if($tribunali)
   { 
       $tribunaleToAdd=array();
       while($tribunale = $tribunali->fetch_assoc()){
           $tribunaleToAdd[] = $tribunale;
       }
       $row['tribunale'] = $tribunaleToAdd;
   }
// per ogni causa aggiungiamo i giudici che l'hanno presieduta
$giudici = $mysqli->query("SELECT p.CodEnte,p.Nome,p.Cognome FROM personefisiche as p
                            inner join giudici as g ON g.CodEnte = p.CodEnte
                             where p.CodEnte='".$row['CodGiudice']."'");

if($giudici)
{
    $giudiciToAdd = array();
    while($giudice = $giudici->fetch_assoc()){
        $giudiciToAdd[] = $giudice;
    }
    $row['giudice'] = $giudiciToAdd;
}

// aggiungiamo le udienze per ogni causa
$udienze =$mysqli->query("SELECT u.CodUdienza,u.La_Data,u.Esito FROM udienze as u
                          where u.CodCausa='".$row['CodCausa']."'"); 
if($udienze)
{
    $udienzeToAdd=array();
    while($udienza = $udienze->fetch_assoc()){
        //per ogni udienza aggiungiamo i collaboratori
            $collaboratori = $mysqli->query("SELECT p.CodEnte as CodCollaboratore,p.Nome as nomeCollaboratore,p.Cognome as cognomeCollaboratore FROM personefisiche as p
                                            inner join collaboratori as c ON c.CodEnte = p.CodEnte
                                            inner join discussada as d ON d.CodEnte = c.CodEnte
                                            where d.CodUdienza='".$udienza['CodUdienza']."'"); 
            if($collaboratori)
            {
                $collaboratoriToAdd= array();
                while($collaboratore = $collaboratori->fetch_assoc()){
                    $collaboratoriToAdd[] = $collaboratore;
                }
                $udienza['Collaboratori'] = $collaboratoriToAdd;
            }

        $udienzeToAdd[] = $udienza;

    }
    $row['udienze'] = $udienzeToAdd;
}

// aggiungiamo le persone coinvolte per ogni causa
$personeCoinvolte= $mysqli->query("SELECT p.CodEnte as CodEnte ,p.Nome as nome,p.Cognome as cognome,c.Cliente
                                            FROM coinvoltoin as c
                                            inner join personefisiche as p ON c.CodEnte = p.CodEnte
                                            
                                            where c.CodCausa='".$row['CodCausa']."'");
if($personeCoinvolte)
{
    $personeCoinvolteToAdd = array();
    while ($personeCoinvolta = $personeCoinvolte->fetch_assoc()){
        $personeCoinvolteToAdd[] = $personeCoinvolta;
    }
    $row['entita coinvolte'] = $personeCoinvolteToAdd;
}

$societàCoinvolte= $mysqli->query("SELECT s.CodEnte as CodEnte ,s.Nome as nome,c.Cliente
                                    FROM coinvoltoin as c
                                    inner join societa as s ON c.CodEnte = s.CodEnte

                                    where c.CodCausa='".$row['CodCausa']."'");

if($societàCoinvolte)
{
    $societàCoinvolteToAdd = array();
    while ($societàCoinvolta = $societàCoinvolte->fetch_assoc()){
        $societàCoinvolteToAdd[] = $societàCoinvolta;
    }
    if(count($societàCoinvolteToAdd)>0)
    $row['entita coinvolte'][] = $societàCoinvolteToAdd;
}

$istituzioniCoinvolte= $mysqli->query("SELECT i.CodEnte as CodEnte ,i.Nome as nome,i.TipoIstituzione, c.Cliente
FROM coinvoltoin as c
inner join istituzioni as i ON c.CodEnte = i.CodEnte

where c.CodCausa='".$row['CodCausa']."'");

if($istituzioniCoinvolte)
{
$istituzioniCoinvolteToAdd = array();
while ($istituzioniCoinvolta = $istituzioniCoinvolte->fetch_assoc()){
$istituzioniCoinvolteToAdd[] = $istituzioniCoinvolta;
}
if(count($istituzioniCoinvolteToAdd)>0)
$row['entita coinvolte'][] = $istituzioniCoinvolteToAdd;
}

   
   
   
$data []= $row;

    
}
echo json_encode($data);
//echo bin2hex( MongoDB\BSON\fromPHP($data));



?>
