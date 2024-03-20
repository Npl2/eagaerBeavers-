<?php
use MongoDB\Driver\ServerApi;
require_once __DIR__ . '/vendor/autoload.php';

class MongoClientDB_CAR {
    public function __construct() {
        $connectionString = "mongodb+srv://root:root@sysintg.ycowwfe.mongodb.net/?retryWrites=true&w=majority&appName=SysIntg";
        $apiVersion = new ServerApi(ServerApi::V1);
        $this->client = new MongoDB\Client($connectionString, [], ['serverApi' => $apiVersion]);
        $this->db = $this->client->selectDatabase('carDeal');
    }

    
       public function insertCarReg($username, $make, $year, $model, $onSale) {
            $result = $this->client->db->carRegistrations->insertOne([
                'username' => $username,
                'make' => $make,
                'year' => $year,
                'model' => $model,
                'on_sale' => $onSale,
                'created_at' => new MongoDB\BSON\UTCDateTime()
            ]);
        
            return ['success' => $result->getInsertedCount() == 1];
        }
        
    

    public function getCarRegsByUser($username) {
        return $this->client->db->carRegistrations->find(['username' => $username])->toArray();
    }

    public function listAllCarRegs() {
        return $this->client->db->carRegistrations->find()->toArray();
    }

}


