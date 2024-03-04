#!/usr/bin/php
<?php
use MongoDB\Driver\ServerApi;
require_once __DIR__ . '/vendor/autoload.php';

class MongoClientDB {
    private $client;
    private $db;

    public function __construct() {
        $connectionString = "mongodb+srv://hp548:Winter2022@systemintg.aahdnzc.mongodb.net/?retryWrites=true&w=majority&appName=SystemIntg";
        $apiVersion = new ServerApi(ServerApi::V1);
        $this->client = new MongoDB\Client($connectionString, [], ['serverApi' => $apiVersion]);
        $this->db = $this->client->selectDatabase('carDeal');
    }

    // find user
    public function findUserByUsername($username) {
        $collection = $this->db->selectCollection('users');
        return $collection->findOne(['username' => $username]);
    }

    // inserting user
    public function insertUser($username, $password) {
      $collection = $this->db->selectCollection('users');
      $existingUser = $collection->findOne(['username' => $username]);
      if ($existingUser) {
          return ['success' => false, 'message' => 'User already exists.'];
      }
      
      $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
      $result = $collection->insertOne([
          'username' => $username,
          'password' => $hashedPassword,
      ]);
      
      if ($result->getInsertedCount() == 1) {
          return ['success' => true, 'message' => 'User successfully inserted.'];
      } else {
          return ['success' => false, 'message' => 'Failed to insert user.'];
      }
    }
  
    // checking
    public function isDatabaseConnected() {
        try {
            $this->db->listCollections();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

}
?>
