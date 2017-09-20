<?php
$url = isset($_GET['url']) ? (string)rawurldecode($_GET['url']) : null;
header("Location: ". $url);
exit(0);