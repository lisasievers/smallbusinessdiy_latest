<?php 
$path = 'assets/site/bootstrap/css/bootstrap.css';
$type = pathinfo($path, PATHINFO_EXTENSION);
$data = file_get_contents($path);
$data = preg_replace("#font-family:.*?;#si", "", $data);
echo "<style>".$data."</style>";

$path = 'assets/site/css/style.css';
$type = pathinfo($path, PATHINFO_EXTENSION);
$data = file_get_contents($path);
$data = preg_replace("#font-family:.*?;#si", "", $data);
echo "<style>".$data."</style>";

$path = 'assets/site/css/component.css';
$type = pathinfo($path, PATHINFO_EXTENSION);
$data = file_get_contents($path);
$data = preg_replace("#font-family:.*?;#si", "", $data);
echo "<style>".$data."</style>";


$path = 'assets/site/css/custom.css';
$type = pathinfo($path, PATHINFO_EXTENSION);
$data = file_get_contents($path);
$data = preg_replace("#font-family:.*?;#si", "", $data);
echo "<style>".$data."</style>";
?>

