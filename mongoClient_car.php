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
        $todos = array_map(function ($recall) use ($username, $make, $model, $year) {
            $component = $recall['Component'] ?? 'Unknown component';
            $summary = $recall['Summary'] ?? 'No summary provided';
            $consequence = $recall['Consequence'] ?? 'No consequence provided';
            $remedy = $recall['Remedy'] ?? 'Contact dealer for more information on the remedy.';
            $notes = $recall['Notes'] ?? 'No additional notes available';
        
            $taskDescription = "Recall Notice: " . $component . ". \n";
            $taskDescription .= "Summary: " . $summary . ". \n";
            $taskDescription .= "Consequence: " . $consequence . ". \n";
            $taskDescription .= "Remedy: " . $remedy . ". \n";
            $taskDescription .= $notes;

            
            return [
                'username' => $username,
                'make' => $make,
                'model' => $model,
                'year' => $year,
                'task' => $taskDescription,
                'created_at' => new MongoDB\BSON\UTCDateTime(),
                'status' => 'pending'
            ];
        }, $recalls);
    
        $result = $this->client->db->recallTodos->insertMany($todos);
    
        return $result->getInsertedCount() == count($todos);
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
    

}


