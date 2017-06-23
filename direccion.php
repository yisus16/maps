<?php 
	require('vendor/autoload.php');

	$geo = [];
	$origen  = ['lat' => $_POST['O_lat'],'lng' => $_POST['O_lng']];
	$destino = ['lat' => $_POST['D_lat'],'lng' => $_POST['D_lng']];

	$direccion =  \GeometryLibrary\SphericalUtil::computeHeading($origen, $destino);
    
    if ($direccion >= -45 && $direccion <= 45) {
		$left  =  \GeometryLibrary\SphericalUtil::computeOffset(['lat' => $origen['lat'], 'lng' => $origen['lng']], 80, $direccion -45);
		$right =  \GeometryLibrary\SphericalUtil::computeOffset(['lat' => $origen['lat'], 'lng' => $origen['lng']], 80, $direccion +45);
    } else if ($direccion >= 45 && $direccion <= 135) {
		$left  =  \GeometryLibrary\SphericalUtil::computeOffset(['lat' => $origen['lat'], 'lng' => $origen['lng']], 80, $direccion -90);
		$right =  \GeometryLibrary\SphericalUtil::computeOffset(['lat' => $origen['lat'], 'lng' => $origen['lng']], 80, $direccion +90);
    } else if ($direccion >= 135  && $direccion <= 179.999) {
		$left  =  \GeometryLibrary\SphericalUtil::computeOffset(['lat' => $origen['lat'], 'lng' => $origen['lng']], 80, $direccion -45);
		$right =  \GeometryLibrary\SphericalUtil::computeOffset(['lat' => $origen['lat'], 'lng' => $origen['lng']], 80, $direccion +45);
    } else if ($direccion <= -45  && $direccion >= -135) {
		$left  =  \GeometryLibrary\SphericalUtil::computeOffset(['lat' => $origen['lat'], 'lng' => $origen['lng']], 80, $direccion -90);
		$right =  \GeometryLibrary\SphericalUtil::computeOffset(['lat' => $origen['lat'], 'lng' => $origen['lng']], 80, $direccion +90);
    } else if ($direccion <= -135  && $direccion >= -179.999) {
		$left  =  \GeometryLibrary\SphericalUtil::computeOffset(['lat' => $origen['lat'], 'lng' => $origen['lng']], 80, $direccion -45);
		$right =  \GeometryLibrary\SphericalUtil::computeOffset(['lat' => $origen['lat'], 'lng' => $origen['lng']], 80, $direccion +45);
    } 

	$geo[0] = $left;
	$geo[1] = $right;
  	echo json_encode($geo);
?>