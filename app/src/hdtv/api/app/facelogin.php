<?php

use Aws\Rekognition\RekognitionClient;
use Aws\Exception\AwsException;
use Aws\S3\S3Client;

dispatch('/api/public/face/:user/index', function ($user) {
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
});
dispatch('/api/public/face/:user/remove', function ($user) {
    $faceid = Imx\db::rquery("select faceid from users where user_id='$user'");
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
    $result = $client->deleteFaces([
        'CollectionId' => 'hdtvapp', // REQUIRED
        'FaceIds' => [$faceid]
    ]);
    return $result;
});

dispatch_post('/api/public/face/get', function ($user) {
    $data =  file_get_contents('php://input');
    error_log($data, 0);

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
    Imx\db::iquery("update users set faceid ='{$faceId}' where user_id ='$user'");
});

dispatch_post("/api/app/facelogin/", function () {
    header('Content-Type: application/json; charset=utf-8');
    $image =  json_decode(file_get_contents('php://input'), true)['image'] ?? "";
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

    error_log(substr($image, 0, 10), 0);
    error_log(substr($image, -10), 0);
    $image = base64_decode($image);
    $client = new RekognitionClient($params);
    // $image = file_get_contents("'../../../foto.jpg");
    // $image = base64_encode($image);
    // $image = base64_decode($image);
    // echo base64_encode($image);
    // exit;
    try {

        $result = $client->searchFacesByImage([
            'CollectionId' => 'hdtvapp', // REQUIRED
            'FaceMatchThreshold' => 95,
            'Image' => [ // REQUIRED
                'Bytes' => $image,
            ],
            'MaxFaces' => 1,
            'QualityFilter' => 'MEDIUM',
        ]);
    } catch (Exception $e) {
        header('HTTP/1.0 401 Unauthorized');

//        error_log("Error \n ", $e->getMessage(), 0);
        return "error";
    }
    $matches = $result->get('FaceMatches');

    if (count($matches)) {
        $faceid = $matches[0]['Face']['FaceId'];
        $user = Imx\db::dataQuery("select user_id,name,email,store_id,face,(select profiles.name from profiles where profiles.profile_id = users.profile_id) as profile from users where faceid ='{$faceid}'");
        if (count($user)) {
            $token = Imx\utils::get_guid();
            $user['token'] = $token;
            Imx\db::iquery("update users set token ='$token' where faceid ='$faceid'");
            // Create attendance record 
            $location = getallheaders()['app-location'] ?? "";
            hdtv::attendance($user['user_id'], $user['store_id'], 'FaceLogin', $location, "2");
            error_log("Facelogin Token $token", 0);
            // now we validate the user location
            error_log("Location login : $location", 0);
            $latitude = explode(",", $location)[0];
            $longitude = explode(",", $location)[1];
            $store = Imx\db::dataQuery("select lat,lng , maximum_valid_distance from stores where store_id in (select store_id from users where token='$token')");
            $meters = Imx\location::get_meters_between_points($latitude, $longitude, $store['lat'] ?? 0, $store['lng'] ?? 0);
            header('app-meters: ' . $meters);
            if($user['profile']=="Superadmin"){
            $meters = 1;
            }
            if ($meters > $store['maximum_valid_distance']) {
                Imx\headers::unauthorized();
                $meters = intval($meters);
                // echo "$latitude,$longitude  {$store['lat']},{$store['lng']} \n";
                error_log(" $latitude,$longitude  {$store['lat']},{$store['lng']} Distance from store {$store['name']} exceed by " . ($meters - $store['maximum_valid_distance']) . " mt.", 0);
                $response = json_encode(["error" => "{$user['profile']} Distance from store {$store['name']} exceed by " . ($meters - $store['maximum_valid_distance']) . " mt."]);
                die($response);
            }


            return json_encode(["token" => $token]);
        }
    }
    Imx\headers::unauthorized();
    return "Error"; // 401
    error_log(json_encode($matches), 0);
    return $result;
});
