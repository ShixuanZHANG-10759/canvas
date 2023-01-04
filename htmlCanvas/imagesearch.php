<?php
class imagesearch {
    const API_KEY = "MtYao5MikoGH3vgIOOGzuXcm";
    const SECRET_KEY = "FVS59VCD8RFY0TU8XkuK9yqxOTH51mCl";

    public function run($imagedata) {
//        print_r($imagedata);
//        exit();
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://aip.baidubce.com/rest/2.0/image-classify/v1/realtime_search/similar/search?access_token={$this->getAccessToken()}",
            CURLOPT_TIMEOUT => 30,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            // image 可以通过 $this.getFileBase64Content("C:\fakepath\1 ere Autreau.jpg") 方法获取
            CURLOPT_POSTFIELDS => 'image='. $imagedata,
    
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),

        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }
    
    /**
     * 获取文件base64编码
     * @param string  $path 文件路径
     * @return string base64编码信息，不带文件头
     */
    private function getFileContentAsBase64($path){
        return base64_encode(file_get_contents($path));
    }
    
    /**
     * 使用 AK，SK 生成鉴权签名（Access Token）
     * @return string 鉴权签名信息（Access Token）
     */
    private function getAccessToken(){
        $curl = curl_init();
        $postData = array(
            'grant_type' => 'client_credentials',
            'client_id' => self::API_KEY,
            'client_secret' => self::SECRET_KEY
        );
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://aip.baidubce.com/oauth/2.0/token',
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => http_build_query($postData)
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $rtn = json_decode($response);
        return $rtn->access_token;
    }
}

//print_r($_POST['imgdata']);
//exit();
$imgData = urlencode(str_replace(" ", "+", str_replace("data:image/jpeg;base64,", "", trim($_POST['imgdata']))));

$rtn = (new imagesearch())->run($imgData);
$jsonR = json_decode($rtn,true);
//$ia = "938796675,3386920464;aa";
$jsonArray = $jsonR['result'];
$a = array();
foreach($jsonArray as $item) { //foreach element in $arr
    $contSign = $item['cont_sign']; //etc
    $score = $item['score'];
    if($score > 0.6){
        array_push($a, $item['cont_sign']);
    }
}
//sort($a);
//print_r($a);
//exit();
//$ia =
//$buffer = explode(';', $ia);
//
//while (count($buffer)) {
//    $a = array(array_shift($buffer));
//}
//var_dump($a);
//exit();
//array_push($a, "4155641386,3614191199");
$manager = new MongoDB\Driver\Manager("mongodb+srv://palisep123:palisep123@cluster0.vykdurh.mongodb.net/test");

$filter = ['image_cont_sign' => ['$in' => $a]];
$options = [
    'limit' => 3
];
$result = array();
$query = new MongoDB\Driver\Query($filter, $options);
$cursor = $manager->executeQuery('testdb.images', $query);


//return $cursor;
foreach ($cursor as $document) {
    array_push($result, $document);
}
echo json_encode($result,JSON_UNESCAPED_UNICODE);


//print_r($rtn);