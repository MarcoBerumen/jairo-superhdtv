<?php
use Aws\Rekognition\RekognitionClient;
use Aws\Exception\AwsException;
use Aws\S3\S3Client;


class hdtv
{
    /**
     * @param $module
     * @param string $debug
     * @param $refid
     * @return void
     */
    public static function storeFilter(){
        $user = $_SESSION['user']['user_id'];
        $profile  = $_SESSION['user']['profile_id'];
        if(Imx\db::rquery("select bo from profiles where profile_id='{$profile}'") =="3"){
        $stores = Imx\db::rquery("select group_concat(store_id) from stores")??"0";
        }
        else
        {
        $stores = Imx\db::rquery("select bo_stores from users where user_id ='{$user}'")??"0";
        }
        return $stores;
}

    public static function indexFace($user) {
        $face = explode(",", Imx\db::rquery("select face from users where user_id='$user'"));
        $params = array(
            'region' => $_ENV['AWS_DEFAULT_REGION'],
            'version' => 'latest',
            'credentials' => [
                'key' => $_ENV['AWS_ACCESS_KEY_ID'],
                'secret' => $_ENV['AWS_SECRET_ACCESS_KEY']
            ],
            'http'    => [
                'verify' => false
            ]
        );


        $client = new RekognitionClient($params);
        $result = $client->indexFaces([
            'CollectionId' => 'hdtvapp', // REQUIRED
            // 'DetectionAttributes' => ['<string>', ...],
            'ExternalImageId' => $user,
            'Image' => [
                'S3Object' => [
                    'Bucket' => $_ENV['AWS_BUCKET'],
                    'Name' => $face[0]
                ]
            ],
            'MaxFaces' => 1,
            'QualityFilter' => 'MEDIUM',
        ]);
        $faceId =   $result->get('FaceRecords')[0]['Face']['FaceId'];
        error_log("Face Id" .$faceId,0);
        Imx\db::iquery("update users set faceid ='{$faceId}' where user_id ='$user'");
        return $faceId;
    }

    public static function recordLog($module, string $debug = '', string $refid = '')
    {
        $date = date('Y-m-d H:i:s');
        if (isset(array_change_key_case(getallheaders())['authorization'])) {
            // Is app endpoint .
            $data = file_get_contents('php://input');
            $token = array_change_key_case(getallheaders())['authorization'] ?? '';
            $user =  Imx\db::rquery("select count(*) from users where token='$token'") ?? 0;
        } else {
            // its a session
            $user = $_SESSION['user']['user_id'] ?? "";
            $data = json_encode($_POST);
        }
        $ip = $_SERVER['REMOTE_ADDR'];
        $db = Imx\db::mycon();
        $data = $db->real_escape_string($data);
        $q =  "insert into `log` (date,module,user,ipaddress,debug,data,reference_id)
        values (
            '$date','$module','$user','$ip','$debug','$data','$refid'
        )";
//        error_log($q, 0);
        Imx\db::iquery(
            $q
        );
    }
    public static function checkToken()
    {

        error_log("Incoming headers : \n". json_encode(getallheaders()),0);

        $token = array_change_key_case(getallheaders())['authorization'] ?? "";
        $location = array_change_key_case(getallheaders())['app-location'] ?? "";
//        error_log("Location : $location", 0);

//        error_log('Check  token '. $token, 0);
//        error_log("select count(*) from users where token='$token'");
        if (Imx\db::rquery("select count(*) from users where token='$token'") == 0) {
            Imx\headers::unauthorized();
//            error_log('Unauthorized token ' . $token, 0);
            die('Unauthorized token:' . $token);
            return '';
        }
        // Now we validate coordinates
        $latitude = explode(",", $location)[0];
        $longitude = explode(",", $location)[1];
        $store = Imx\db::dataQuery("select lat,lng , maximum_valid_distance from stores where store_id 
                                                              in (select store_id from users where token='$token')");
        $meters = Imx\location::get_meters_between_points(
            $latitude,
            $longitude,
            $store['lat'] ?? 0,
            $store['lng'] ?? 0
        );
        header('app-meters: ' . $meters);
        if ($meters > $store['maximum_valid_distance']) {
            Imx\headers::unauthorized();
            $meters = intval($meters);
            // echo "$latitude,$longitude  {$store['lat']},{$store['lng']} \n";
//            error_log(" $latitude,$longitude  {$store['lat']},{$store['lng']} Distance from store
//             {$store['name']} exceed by " . ($meters - $store['maximum_valid_distance']) . ' mt.', 0);
            $response = json_encode(["error" => "Distance from store {$store['name']} exceed by " .
                ($meters - $store['maximum_valid_distance']) . ' mt.']);
            die($response);
        }
//        error_log('Authorized token ' . $token, 0);

        // echo $meters;
        return $token;
    }

    public static function checkTokenW()
    {

        $token = array_change_key_case(getallheaders())['authorization'] ?? "";

        if (Imx\db::rquery("select count(*) from users where token='$token'") == 0) {
            Imx\headers::unauthorized();
            error_log('Unauthorized token ' . $token, 0);
            die('Unauthorized token:' . $token);
            return '';
        }
    }


    /**
     * @param int $product
     * @param int $store
     * @param int $status
     * @param int $grade
     * @return void
     */
    public static function inventory(int $product, int $store, int $status, int $grade)
    {
        $query = "select count(*) from items  where
        store_id = '{$store}'
        and product_id = '{$product}'
        and status_id = '{$status}' 
        and status = 1 
        and grade_id = '{$grade}'";
        $stock = Imx\db::rquery($query);
        Imx\db::iquery("replace into stock (store_id,product_id,status_id,grade_id,stock)
        values ('{$store}','{$product}','{$status}','{$grade}','{$stock}')");
    }

    public static function product_pricing($product, $price, $min_price, $cost, $price_list, $status, $grade)
    {
        $q = "
        replace into product_pricing
        (
            price,
            min_price,
            cost,
            product_id,
            price_list_id,
            status_id,
            grade_id
            )
            values
            (

        '{$price}',
        '{$min_price}',
        '{$cost}',
        '{$product}',
        '{$price_list}',
        '{$status}',
        '{$grade}'
        )
        ";
        Imx\db::iquery($q);
        return 'Ok';
    }

    public static function getMaxPrice($price_list, $product, $status, $grade)
    {
        $q = "
        select price from product_pricing 
        where 
        price_list_id ={$price_list}
        and product_id = {$product}
        and status_id = {$status}
        and grade_id = {$grade}
        ";
        return Imx\db::rquery($q);
    }
    public static function getMinPrice($store, $product)
    {
        $price_list = Imx\db::rquery("select price_list_id from stores where store_id ='{$store}'");

        $q = "
        select max(price) from product_pricing 
        where 
        price_list_id ={$price_list}
        and product_id = {$product}
        and grade_id in (select grade_id from stock where stock.store_id = '$store' 
                                                    and  stock.product_id = '{$product}')
and status_id in (select status_id from stock where stock.store_id = '$store' 
                                                    and  stock.product_id = '{$product}')
        ";

        return Imx\db::rquery($q);
    }
    /**
     * Comission function
     *
     * return string
     */
    public static function getComission($user, $product, $amount,$grade="",$status="")
    {
        // ! check if there is specific  product comission
        $category = Imx\db::rquery("select category_id from products where product_id ='{$product}'");
        //? if not product comissions default query for  category comissions
        $q = "SELECT max( comission ) as comission,comission_type,`range`,category_id FROM users_comissions WHERE user_id = {$user} AND ({$amount} >= `range` or comission_type ='Fixed') and category_id = '{$category}'  and product_id =''  and grade_id ='' and status_id ='' ";
        // ? only product comission
        if(Imx\db::rquery("select count(*) from users_comissions  WHERE user_id = {$user} AND ({$amount} >= `range` or comission_type ='Fixed') and product_id='{$product}' and   grade_id ='' and status_id =''  ") > 0){
            $q = "SELECT max( comission ) as comission,comission_type,`range`,category_id FROM users_comissions WHERE user_id = {$user} AND ({$amount} >= `range` or comission_type ='Fixed') and category_id = '{$category}'  and product_id ='{$product}' and   grade_id ='' and status_id =''  ";
        }
        // ? only grade comission

        if(Imx\db::rquery("select count(*) from users_comissions  WHERE user_id = {$user} AND ({$amount} >= `range` or comission_type ='Fixed') and product_id='{$product}' and grade_id ='$grade' and status_id ='' and product_id ='' ") > 0){
            $q = "SELECT max( comission ) as comission,comission_type,`range`,category_id FROM users_comissions WHERE user_id = {$user} AND ({$amount} >= `range` or comission_type ='Fixed') and category_id = '{$category}'  and product_id ='{$product}' and grade_id ='$grade' and status_id ='' and product_id =''";
        }
        // ? only grade comission
        if(Imx\db::rquery("select count(*) from users_comissions  WHERE user_id = {$user} AND ({$amount} >= `range` or comission_type ='Fixed') and product_id='{$product}' and status_id ='$status' and product_id ='' and grade_id =''  ") > 0){
            $q = "SELECT max( comission ) as comission,comission_type,`range`,category_id FROM users_comissions WHERE user_id = {$user} AND ({$amount} >= `range` or comission_type ='Fixed') and category_id = '{$category}'  and product_id ='{$product}' and status_id ='$status' and product_id ='' and grade_id ='' ";
        }
        // ? ::  product , grade  and status match
        if(Imx\db::rquery("select count(*) from users_comissions  WHERE user_id = {$user} AND ({$amount} >= `range` or comission_type ='Fixed') and product_id='{$product}'  and grade_id ='$grade' and status_id ='$status'   ") > 0){
            $q = "SELECT max( comission ) as comission,comission_type,`range`,category_id FROM users_comissions WHERE user_id = {$user} AND ({$amount} >= `range` or comission_type ='Fixed') and category_id = '{$category}'  and product_id ='{$product}'  and grade_id ='$grade' and status_id ='$status'  ";
        }
        // ? ::  product &  grade match
        if(Imx\db::rquery("select count(*) from users_comissions  WHERE user_id = {$user} AND ({$amount} >= `range` or comission_type ='Fixed') and product_id='{$product}'  and grade_id ='$grade' and status_id =''   ") > 0){
            $q = "SELECT max( comission ) as comission,comission_type,`range`,category_id FROM users_comissions WHERE user_id = {$user} AND ({$amount} >= `range` or comission_type ='Fixed') and category_id = '{$category}'  and product_id ='{$product}'  and grade_id ='$grade' and status_id =''   ";
        }

        // ? ::  product &  status  match
        if(Imx\db::rquery("select count(*) from users_comissions  WHERE user_id = {$user} AND ({$amount} >= `range` or comission_type ='Fixed') and product_id='{$product}'  and grade_id ='' and status_id ='$status'   ") > 0){
            $q = "SELECT max( comission ) as comission,comission_type,`range`,category_id FROM users_comissions WHERE user_id = {$user} AND ({$amount} >= `range` or comission_type ='Fixed') and category_id = '{$category}'  and product_id ='{$product}'  and grade_id ='' and status_id ='$status'  ";
        }
        // ? ::  product only match
        if(Imx\db::rquery("select count(*) from users_comissions  WHERE user_id = {$user} AND ({$amount} >= `range` or comission_type ='Fixed') and product_id='{$product}'  and (grade_id ='' or grade_id is null  ) and (status_id =''  or status_id is null)   ") > 0){
            $q = "SELECT max( comission ) as comission,comission_type,`range`,category_id FROM users_comissions WHERE user_id = {$user} AND ({$amount} >= `range` or comission_type ='Fixed')  and product_id ='{$product}'  and (grade_id ='' or grade_id is null  ) and (status_id =''  or status_id is null) ";
        }
//        error_log($q, 0);
//        echo $q;
        $data = Imx\db::dataQuery($q);
        if ($data['comission_type'] == "Fixed") {
            // Fixed comission
            return $data['comission'];
        } else {
            // Percentage comission, multiply amount by percentaje divided by 100
            return ($data['comission'] / 100) * $amount;
        }
    }
    public static function tax($store, $amount)
    {
        $tax = Imx\db::rquery("select tax from stores where store_id ='{$store}'");
        // Percentage tax, multiply amount by tax divided by 100
        return ($tax / 100) * $amount;
    }
    /**
     * Attendance function, to store all authentication methods
     */
    public static function attendance($user, $store, $type = 'FaceLogin', $coordinates = "", $status = "2")
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        error_log("Attendance Login from IP {$ip}", 0);
        $q = " insert into attendance(
            user_id,
            store_id,
            coordinates,
            date,
            ipaddress,
            status,
            type)
            values
            (
                '{$user}',
                '{$store}',
                '{$coordinates}',
                now(),
                '{$ip}',
                '{$status}',
                '{$type}'
            )";
//        error_log($q);
        Imx\db::iquery($q);
    }

    public static function updateCredit($customer)
    {
        $credits = Imx\db::rquery("select sum(credit) from credits where customer_id ='$customer'")??0;
        $query = "select sum(sales_payments.amount) + sum(sales_payments.tax)  from  sales_payments
        left join sales on sales.sale_id = sales_payments.sale_id
                   left join payment_methods on payment_methods.payment_method_id = sales_payments.payment_method_id
        where sales.customer_id =  '$customer'
				and payment_methods.credit = 1";
        $debits = Imx\db::rquery($query)??0;

//exit;
//        echo " Credits $credits , Debits $debits ";
        $credit = $credits - $debits;
//        echo "update customers set credit = '$credit' where customer_id = '$customer'";
//        exit;
        Imx\db::iquery("update customers set credit = '$credit' where customer_id = '$customer'");
    }

    public static function kardex(
        int $item,
        int $store,
        String $movement,
        int $reference_id,
        String $date,
        int $user,
        String $info
    ) {
        Imx\db::iquery("insert into kardex 
            (item_id,movement_type,reference_id,store_id,user_id,date,info)
            values
            ('{$item}',)    
            ");
        return $item;
    }
}
