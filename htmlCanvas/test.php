<?php
//phpinfo();
//exit();
//    $mongo = new MongoDB\Driver\Manager("mongodb://localhost:27017");
//    $bulk = new MongoDB\Driver\BulkWrite();
//    $id1 = $bulk->insert([
//        'product_id'        => 101,
//        'product_name'      => '俱乐部全犬种成犬粮天然健康狗粮10kg',
//        'product_price'     => 269.00,
//        'created_at' => new MongoDB\BSON\UTCDateTime(),
//    ]);
//    try {
//        $result = $mongo->executeBulkWrite('testdb.products', $bulk);
//        var_dump($result->getInsertedCount());
//    } catch (MongoDB\Driver\Exception\BulkWriteException $e) {
//        var_dump($e->getWriteResult()->getWriteErrors());
//    }
$a = array();
array_push($a, 0.9);
array_push($a, 1.1);
array_push($a, 0.4);
array_push($a, 0.5);
var_dump($a);
var_dump(arsort($a));
var_dump($a);