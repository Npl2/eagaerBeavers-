<?php
require_once 'vendor/autoload.php'; 

use MongoDB\Client;
use MongoDB\Driver\ServerApi;

class MongoClientDB_CARReviews {
    private $client;
    private $db;

    public function __construct() {
        $connectionString = "mongodb+srv://root:root@sysintg.ycowwfe.mongodb.net/?retryWrites=true&w=majority&appName=SysIntg";
        $apiVersion = new ServerApi(ServerApi::V1);
        $this->client = new Client($connectionString, [], ['serverApi' => $apiVersion]);
        $this->db = $this->client->selectDatabase('carDeal');
    }

    public function addCarReview($carMake, $carModel, $carYear, $reviewText, $username) {
        $collection = $this->db->selectCollection('carReviews');

        $document = [
            'username' => $username,
            'car_make' => $carMake,
            'car_model' => $carModel,
            'car_year' => $carYear,
            'review_text' => $reviewText,
            'created_at' => new \MongoDB\BSON\UTCDateTime()
        ];

        $result = $collection->insertOne($document);
        return ['success' => $result->getInsertedCount() == 1];
    }
}


?>