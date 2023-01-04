<?php

//$ia = $_POST['imagesInfo'];
//echo $ia;
//exit();
$ia = "938796675,3386920464;aa";
$buffer = explode(';', $ia);
$a = array();
while (count($buffer)) {
    $a = array(array_shift($buffer));
}
//var_dump($a);
//exit();
//array_push($a, "4155641386,3614191199");
$manager = new MongoDB\Driver\Manager("mongodb+srv://palisep123:palisep123@cluster0.vykdurh.mongodb.net/test");

$filter = ['image_cont_sign' => ['$in' => $a]];
$options = [

];
$result = array();
$query = new MongoDB\Driver\Query($filter, $options);
$cursor = $manager->executeQuery('testdb.images', $query);


//return $cursor;
foreach ($cursor as $document) {
    array_push($result, $document);
}
echo json_encode($result,JSON_UNESCAPED_UNICODE);
