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

    public function getCsvData($filePath){
        $handle = fopen( $filePath, "rb" );
        $data = [];
        while (!feof($handle)) {
            $data[] = fgetcsv($handle);
        }
        fclose($handle);
        $data = eval('return ' . mb_convert_encoding(var_export($data, true), 'UTF-8', 'UTF-8') . ';');//字符转码操作
        return $data;
    }

    public function insertImage($imageFile){
        $rtn = $this->run($imageFile);
        $jsonR = json_decode($rtn,true);
        if(!isset($jsonR['error_code'])){
            $mongo = new MongoDB\Driver\Manager("mongodb://localhost:27017");
            $bulk = new MongoDB\Driver\BulkWrite();
            $id1 = $bulk->insert([
                'image_id'        => $jsonR["log_id"],
                'image_json'      => $rtn,
                'image_cont_sign'      => $jsonR["cont_sign"],
                'image_file'     => $imageFile,
                'created_at' => new MongoDB\BSON\UTCDateTime(),
            ]);
            try {
                $result = $mongo->executeBulkWrite('testdb.images', $bulk);
                return ($result->getInsertedCount());
            } catch (MongoDB\Driver\Exception\BulkWriteException $e) {
                var_dump($e->getWriteResult()->getWriteErrors());
            }
        } else {
            return 0;
        }
    }
}



$csvFilePath = $_FILES["csvfile"]["tmp_name"];
$uploadImage = new UploadImage();
$data = $uploadImage->getCsvData($csvFilePath);
$for_arr_len = count($data);
for($i = 0; $i < $for_arr_len -1; $i++) {
    if($i != 0){
        $path1 = $data[$i];
        if(!is_null($path1)){
            print_r($i);
            print_r('<br>');
            $path = $data[$i][1];
            if(!is_null($path)){
                $count = (new UploadImage())-> insertImage($path);
                if($count > 0){
                    print_r("第" . $i . "条成功");
                    print_r('<br>');
                } else {
                    print_r("第" . $i . "条失败、或者百度图片已经存在");
                    print_r('<br>');
                }
            }
        }
    }
}
