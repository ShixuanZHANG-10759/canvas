<?php
class UploadImage {
    const API_KEY = "MtYao5MikoGH3vgIOOGzuXcm";
    const SECRET_KEY = "FVS59VCD8RFY0TU8XkuK9yqxOTH51mCl";

    public function run($FilePath) {
        $curl = curl_init();
        ///Users/xinghailin/Downloads/images/thierry-3.jpg
//        print_r($FilePath);
//        print($this->getFileContentAsBase64($FilePath));
//        exit();
        $v = urlencode($this->getFileContentAsBase64($FilePath));
        $i = urlencode($FilePath);
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://aip.baidubce.com/rest/2.0/image-classify/v1/realtime_search/similar/add?access_token={$this->getAccessToken()}",
            CURLOPT_TIMEOUT => 30,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            // image 可以通过 $this.getFileBase64Content("C:\fakepath\1 ere Autreau.jpg") 方法获取
            CURLOPT_POSTFIELDS => 'image='.  $v .'&brief='.$i,
    
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
        print $path;
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

$imageFilePath = htmlspecialchars($_POST['imageFilePath']);

$rtn = (new UploadImage())->run($imageFilePath);
$jsonR = json_decode($rtn,true);
//var_dump($jsonR);
//exit();
//返回数据
$mongo = new MongoDB\Driver\Manager("mongodb+srv://palisep123:palisep123@cluster0.vykdurh.mongodb.net/test");
$bulk = new MongoDB\Driver\BulkWrite();
$id1 = $bulk->insert([
    'image_id'        => $jsonR["log_id"],
    'image_json'      => $rtn,
    'image_cont_sign'      => $jsonR["cont_sign"],
    'image_file'     => $imageFilePath,
    'created_at' => new MongoDB\BSON\UTCDateTime(),
]);
try {
    $result = $mongo->executeBulkWrite('testdb.images', $bulk);
    var_dump($result->getInsertedCount());
} catch (MongoDB\Driver\Exception\BulkWriteException $e) {
    var_dump($e->getWriteResult()->getWriteErrors());
}
//print_r($rtn);