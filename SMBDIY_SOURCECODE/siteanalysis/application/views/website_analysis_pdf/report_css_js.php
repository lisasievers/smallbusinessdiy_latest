<?php 
$path = 'bootstrap/css/bootstrap.css';
$type = pathinfo($path, PATHINFO_EXTENSION);
$data = file_get_contents($path);
$data = preg_replace("#font-family:.*?;#si", "", $data);
echo "<style>".$data."</style>";

$path = 'assets/pdf/css/component.css';
$type = pathinfo($path, PATHINFO_EXTENSION);
$data = file_get_contents($path);
$data = preg_replace("#font-family:.*?;#si", "", $data);
echo "<style>".$data."</style>";


$path = 'assets/pdf/css/custom.css';
$type = pathinfo($path, PATHINFO_EXTENSION);
$data = file_get_contents($path);
$data = preg_replace("#font-family:.*?;#si", "", $data);
echo "<style>".$data."</style>";
?>
