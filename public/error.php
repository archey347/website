<?php

include("../functions.php");


$error_code = isset($_GET['c']) ? $_GET['c'] : 400;

if(!is_numeric($error_code)) {
	$error_code = 400;
}

if (!(100 <= $error_code && $error_code <= 600)) {
	$error_code = 404;
}

genPage("../templates/error.php", "Archey Barrell | Error " . $error_code, $error_code);


?>

