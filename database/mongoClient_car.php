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

    public function insertRecallTodos($username, $make, $model, $year, $recalls) {
        $existingTodos = $this->client->db->recallTodos->find([
            'username' => $username,
        ])->toArray();
    
        $existingCampaignNumbers = array_column($existingTodos, 'nhtsaCampaignNumber');
    
        $todos = array_filter(array_map(function ($recall) use ($username, $make, $model, $year, $existingCampaignNumbers) {
            $campaignNumber = $recall['NHTSACampaignNumber'];
            
            if (in_array($campaignNumber, $existingCampaignNumbers)) {
                return null;
            }
    
            $component = $recall['Component'] ?? 'Unknown component';
            $summary = $recall['Summary'] ?? 'No summary provided';
            $consequence = $recall['Consequence'] ?? 'No consequence provided';
            $remedy = $recall['Remedy'] ?? 'Contact dealer for more information on the remedy.';
            $notes = $recall['Notes'] ?? 'No additional notes available';
            
            return [
                'username' => $username,
                'make' => $make,
                'model' => $model,
                'year' => $year,
                'component' => $component,
                'summary' => $summary,
                'remedy' => $remedy,
                'notes' => $notes,
                'nhtsaCampaignNumber' => $campaignNumber,
                'created_at' => new MongoDB\BSON\UTCDateTime(),
                'status' => 'pending'
            ];
        }, $recalls), function($todo) {
            return !is_null($todo); 
        });
    
        if (!empty($todos)) {
            $result = $this->client->db->recallTodos->insertMany($todos);
            return $result->getInsertedCount() == count($todos);
        }
    
        return true;
    }
    
    
    public function getRecallTodosByUsername($username) {
        $query = ['username' => $username];
        $todosCursor = $this->client->db->recallTodos->find($query);
        $todos = $todosCursor->toArray();

        return $todos;
    }

    public function updateRecallTodoStatus($todoId, $newStatus) {
        $objectId = new MongoDB\BSON\ObjectId($todoId);
    
        $result = $this->client->db->recallTodos->updateOne(
            ['_id' => $objectId],
            ['$set' => ['status' => $newStatus]]
        );
    
        return $result->getModifiedCount() == 1;
    }


    // user update car status
    public function updateCarSaleStatus($carId, $onSale, $salePrice) {
        $objectId = new MongoDB\BSON\ObjectId($carId);
        
        $result = $this->client->db->carRegistrations->updateOne(
            ['_id' => $objectId],
            ['$set' => ['on_sale' => $onSale, 'sale_price' => $salePrice]]
        );
        
        return $result->getModifiedCount() == 1;
    }
    
    
    // cars on sale
    public function listCarsOnSale() {
        return $this->client->db->carRegistrations->find(['on_sale' => true])->toArray();
    }

    // search the cars
    public function searchCarsOnSale($make = null, $model = null, $year = null) {
        $query = ['on_sale' => true];
        
        if ($make) {
            $query['make'] = new MongoDB\BSON\Regex('^' . preg_quote($make) . '$', 'i');
        }
        if ($model) {
            $query['model'] = new MongoDB\BSON\Regex('^' . preg_quote($model) . '$', 'i');
        }
        if ($year) {
            $query['year'] = (string) $year;
        }
        
        return $this->client->db->carRegistrations->find($query)->toArray();
    }
    
 
    ////////////////////////////////////////// Bids ////////////////////////////
    // placing bids
    public function placeBid($carId, $username, $bidAmount) {
        $carObjectId = new MongoDB\BSON\ObjectId($carId);
        $carDetails = $this->client->db->carRegistrations->findOne(['_id' => $carObjectId]);
    
        if ($carDetails === null) {
            return ['success' => false, 'message' => "Car not found."];
        }
    
        if ($carDetails['username'] === $username) {
            return ['success' => false, 'message' => "Owner cannot bid on their own car."];
        }
    
        $result = $this->client->db->bids->insertOne([
            'carId' => $carObjectId,
            'username' => $username,
            'bidAmount' => $bidAmount,
            'bidTime' => new MongoDB\BSON\UTCDateTime()
        ]);
    
        return ['success' => $result->getInsertedCount() == 1];
    }
    

    // get bids for specific car
    public function getCarDetailsWithBids($carId) {
        $carObjectId = new MongoDB\BSON\ObjectId($carId);

        // Fetch car details
        $carDetails = $this->client->db->carRegistrations->findOne(['_id' => $carObjectId]);

        // Fetch bids for the car
        $bidsCursor = $this->client->db->bids->find(['carId' => $carObjectId]);
        $bids = $bidsCursor->toArray();

        return [
            'carDetails' => $carDetails,
            'bids' => $bids,
        ];
    }

    
    
}


