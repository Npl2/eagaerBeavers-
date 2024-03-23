<?php
if (!isset($_COOKIE['username'])) {
    header('Location: index.php');
    exit();
}

require_once 'vendor/autoload.php'; 

use MongoDB\Client;
use MongoDB\Driver\ServerApi;

class MongoClientDB_CAR {
    private $client;
    private $db;

    public function __construct() {
        $connectionString = "mongodb+srv://root:root@sysintg.ycowwfe.mongodb.net/?retryWrites=true&w=majority&appName=SysIntg";
        $apiVersion = new ServerApi(ServerApi::V1);
        $this->client = new Client($connectionString, [], ['serverApi' => $apiVersion]);
        $this->db = $this->client->selectDatabase('carDeal');
    }

    public function addVehicleReview($vehicleName, $vehicleModel, $vehicleYear, $reviewText, $username) {
        $collection = $this->db->selectCollection('vehicleReviews');

        $document = [
            'username' => $username,
            'vehicle_name' => $vehicleName,
            'vehicle_model' => $vehicleModel,
            'vehicle_year' => $vehicleYear,
            'review_text' => $reviewText,
            'created_at' => new \MongoDB\BSON\UTCDateTime()
        ];

        $result = $collection->insertOne($document);

        return $result->getInsertedId(); 
    }
}

if ($insertOneResult->getInsertedCount() == 1) {
    echo "Review submitted successfully.";
} else {
    echo "An error occurred.";
}
?><?php
if (!isset($_COOKIE['username'])) {
    header('Location: index.php');
    exit();
}

class MongoClientDB_CAR {
    private $client;
    private $db;

    public function __construct() {
        $connectionString = "mongodb+srv://root:root@sysintg.ycowwfe.mongodb.net/?retryWrites=true&w=majority&appName=SysIntg";
        $apiVersion = new ServerApi(ServerApi::V1);
        $this->client = new Client($connectionString, [], ['serverApi' => $apiVersion]);
        $this->db = $this->client->selectDatabase('carDeal');
    }

    public function addVehicleReview($vehicleName, $vehicleModel, $vehicleYear, $reviewText, $username) {
        $collection = $this->db->selectCollection('vehicleReviews');

        $document = [
            'username' => $username,
            'vehicle_name' => $vehicleName,
            'vehicle_model' => $vehicleModel,
            'vehicle_year' => $vehicleYear,
            'review_text' => $reviewText,
            'created_at' => new \MongoDB\BSON\UTCDateTime()
        ];

        $result = $collection->insertOne($document);

        return $result->getInsertedId(); 
    }
}

if ($insertOneResult->getInsertedCount() == 1) {
    echo "Review submitted successfully.";
} else {
    echo "An error occurred.";
}
?>