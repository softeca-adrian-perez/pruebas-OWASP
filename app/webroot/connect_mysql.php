<?php

require(dirname(__FILE__) . '/../Lib/Texto.php');
require(dirname(__FILE__) . '/../Config/configuration.php');

try {
	$conn = new PDO(
		"mysql:host=" . @$_SERVER['GNMAAG_DATABASE_HOST'] . ";port=3306;dbname=" . @$_SERVER['GNMAAG_DATABASE_DATABASE'],
		@$_SERVER['GNMAAG_DATABASE_LOGIN'],
		Texto::encryptDecryptText(@$_SERVER['GNMAAG_DATABASE_PASSWORD'], false),
		array()
	);

	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$stmt = $conn->prepare("SELECT * FROM monitor");
	$stmt->execute();
	$stmt->setFetchMode(PDO::FETCH_ASSOC);
	$data = $stmt->fetchAll();

	if (count($data) == 1) {
		echo 'OK';
	} else {
		returnError();
	}
} catch (PDOException $e) {
	returnError();
}

function returnError()
{
	header(@$_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
	exit(0);
}
