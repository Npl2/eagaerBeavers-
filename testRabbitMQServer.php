#!/usr/bin/php
  <?php
  require_once('path.inc');
  require_once('get_host_info.inc');
  require_once('rabbitMQLib.inc');
  require_once('mongoClient.php');
  require_once('mongoClient_Blog.php');
  require_once('mongoClient_car.php');
  require_once('mongoClient_carReviews.php');

  use PhpAmqpLib\Connection\AMQPStreamConnection;
  use PhpAmqpLib\Message\AMQPMessage;

  include '/home/npl2/ip.php';

  function setupMessaging($channel) {
      $exchangeName = 'user_auth';
      $queueNameUser = 'testQueue';
      $routingKeyUser = 'user_management';

      // Declare the exchange
      $channel->exchange_declare($exchangeName, 'direct', false, true, false);

      // Declare and bind the user management queue
      $channel->queue_declare($queueNameUser, false, true, false, false);
      $channel->queue_bind($queueNameUser, $exchangeName, $routingKeyUser);
  }


  /*
  function createQueues(){
    $connection = new AMQPStreamConnection('172.28.222.209', 5672, 'test', 'test', 'testHost');
    $channel = $connection->channel();

    $channel->queue_declare('frontend_login_queue', false, true, false, false);

    $channel->close();
    $connection->close();
  }
  */
  /*
  function connectRabbitMQ(){
    return new AMQPStreamConnection('172.28.222.209', 5672, 'test', 'test', 'testHost');
  }
  */
  function doLogin($username, $password) {
    $mongoClientDB = new MongoClientDB();
    $user = $mongoClientDB->findUserByUsername($username);

    if ($user !== null && password_verify($password, $user['password'])) {
        echo "Login successful";
        return true;
    } else {
        echo "Login failed";
        //http_response_code(500);
        return false;
    }
  }

  function insertUser($username, $password){
    $mongoClientDB = new MongoClientDB();
    if (!$mongoClientDB->isDatabaseConnected()) {
        echo "Failed to connect to the database.";
        return false;
    }
    $result = $mongoClientDB->insertUser($username, $password);
    
    if ($result['success']) {
        echo "User successfully inserted.";
        
        return true;
    } else {
        echo "Failed to insert user: " . $result['message'];
        // http_response_code(500);
        return false;
    }
  }

  function requestProcessor($request)
  {
    echo "received request".PHP_EOL;
    var_dump($request);
    $mongoClientDB_BLOG = new MongoClientDB_BLOG();
    $carRegistration = new MongoClientDB_CAR();
    $carReviews = new MongoClientDB_CARReviews();
  
    /* new code
    $connection = connectRabbitMQ();
    $channel = $connection->channel();
    $channel->queue_decalre('frontend_login_queue', false, true, false, false);
    */

    if(!isset($request['type']))
    {
      return "ERROR: unsupported message type";
    }
    switch ($request['type'])
    {
      case "login":
        return array('message' => doLogin($request['username'],$request['password']));
      case "signup":
        return array('message' => insertUser($request['username'],$request['password']));
      case "add_blog_post":
        if (!isset($request['username']) || !isset($request['title']) || !isset($request['content'])) {
            return ["returnCode" => '0', 'message' => "Missing information for adding blog post"];
        }
        $result = $mongoClientDB_BLOG->insertBlogPost($request['username'], $request['title'], $request['content']);
        return ['message' => $result['message']];
      case "add_comment_post":
        if (!isset($request['postId']) || !isset($request['username']) || !isset($request['comment'])) {
          return ["returnCode" => '0', 'message' => "Missing information for adding blog post"];
      }
      $result = $mongoClientDB_BLOG->addComment($request['postId'], $request['username'], $request['comment']);
      return $result;
      case "list_blog_posts":
          $posts = $mongoClientDB_BLOG->listBlogPosts();
          return ['message' => "Blog posts fetched successfully", 'data' => $posts];
      case "get_blog_post_with_comments":
          if (!isset($request['postId'])) {
              return ["returnCode" => '0', 'message' => "No post ID provided"];
          }
          $postWithComments = $mongoClientDB_BLOG->getBlogPostWithComments($request['postId']);
          return ['message' => "Blog post with comments fetched successfully", 'data' => $postWithComments];
      
      case "register_car":
        if (!isset($request['username']) || !isset($request['make']) || !isset($request['year']) || !isset($request['model']) || !isset($request['on_sale'])) {
            return ["returnCode" => '0', 'message' => "Missing information for car registration"];
        }
        $result = $carRegistration->insertCarReg($request['username'], $request['make'], $request['year'], $request['model'], $request['on_sale']);
        return ['message' => $result['success'] ? "Car registration successful." : "Failed to register car."];
    
      case "get_user_car_regs":
          if (!isset($request['username'])) {
              return ["returnCode" => '0', 'message' => "No username provided"];
          }
          $cars = $carRegistration->getCarRegsByUser($request['username']);
          return ['message' => "Car registrations fetched successfully", 'data' => $cars];
      
      case "list_all_car_regs":
          $cars = $carRegistration->listAllCarRegs();
          return ['message' => "All car registrations fetched successfully", 'data' => $cars];
      
      case "insert_recall_todos":
        if (!isset($request['username'], $request['make'], $request['model'], $request['year'], $request['recalls'])) {
            return ["returnCode" => '0', 'message' => "Missing information for inserting recall todos"];
        }
        $success = $carRegistration->insertRecallTodos($request['username'], $request['make'], $request['model'], $request['year'], $request['recalls']);
        return ['message' => $success ? "Recall todos inserted successfully." : "Failed to insert recall todos."];

      case "get_recall_todos_by_username":
        if (!isset($request['username'])) {
            return ["returnCode" => '0', 'message' => "No username provided"];
        }
        $todos = $carRegistration->getRecallTodosByUsername($request['username']);
        return ['message' => "Recall todos fetched successfully", 'data' => $todos];

      case "update_recall_todo_status":
        if (!isset($request['todoId'], $request['newStatus'])) {
            return ["returnCode" => '0', 'message' => "Missing information for updating recall todo status"];
        }
        $success = $carRegistration->updateRecallTodoStatus($request['todoId'], $request['newStatus']);
        return ['message' => $success ? "Recall todo status updated successfully." : "Failed to update recall todo status."];
      
      case "add_car_review":
        if (!isset($request['username']) || !isset($request['make']) || !isset($request['year']) || !isset($request['model']) || !isset($request['review_text'])){
          return ["returnCode" => '0', 'message' => "Missing information for car Reviews"];
        }

        $success = $carReviews->addCarReview($request['make'], $request['model'], $request['year'], $request['review_text'], $request['username']);
        return ['message' => $success ? "Car review added successfully" : "Failed to add car review"];        
    
      case "list_all_reviews":
        $carz = $carReviews->getCarReviews();
        return ['message' => "All car reviews fetched successfully", 'data' => $carz];
    
      case "search_car_reviews":
          $make = $request['make'] ?? null;
          $model = $request['model'] ?? null;
          $year = $request['year'] ?? null;
        $reviews = $carReviews->searchCarReviews($make, $model, $year);
        return ['message' => "Car reviews fetched successfully", 'data' => $reviews];

      case "display_car_details_with_reviews":
          if (!isset($request['make']) || !isset($request['model']) || !isset($request['year'])) {
              return ["returnCode" => '0', 'message' => "Missing information for displaying car details with reviews"];
          }
          $carDetailsWithReviews = $carReviews->getCarDetailsWithReviews($request['make'], $request['model'], $request['year']);
          return ['message' => "Car details with reviews fetched successfully", 'data' => $carDetailsWithReviews];

      case "update_car_sale_status":
        if (!isset($request['carId'], $request['onSale'], $request['salePrice'])) {
            return ["returnCode" => '0', 'message' => "Missing information for updating car sale status"];
        }
        echo "The car status is: " . ($request['onSale'] ? "On Sale" : "Not on Sale") . PHP_EOL;
        $success = $carRegistration->updateCarSaleStatus($request['carId'], $request['onSale'],$request['salePrice']);
        return ['message' => $success ? "Car sale status updated successfully." : "Failed to update car sale status. The car status is same as the provided one"];

      case "list_cars_on_sale":
          $cars = $carRegistration->listCarsOnSale();
          return ['message' => "Cars on sale fetched successfully", 'data' => $cars];

      case "search_cars_on_sale":
          $make = $request['make'] ?? null;
          $model = $request['model'] ?? null;
          $year = $request['year'] ?? null;
          $cars = $carRegistration->searchCarsOnSale($make, $model, $year);
          return ['message' => "Search results fetched successfully", 'data' => $cars];
      
      case "place_bid":
        if (!isset($request['carId'], $request['username'], $request['bidAmount'])) {
            return ["returnCode" => '0', 'message' => "Missing information for placing a bid"];
        }
        $result = $carRegistration->placeBid($request['carId'], $request['username'], $request['bidAmount']);
        return ['message' => $result['success'] ? "Bid placed successfully." : "Failed to place bid."];

        case "get_car_details_and_bids":
          if (!isset($request['carId'])) {
              return ["returnCode" => '0', 'message' => "No car ID provided"];
          }
          $data = $carRegistration->getCarDetailsWithBids($request['carId']);
          
          if ($data) {
              if (isset($data['carDetails'])) {
                  return ['message' => "Car details and bids fetched successfully", 'data' => $data];
              } else {
                  return ["returnCode" => '0', 'message' => "No car found with the provided ID"];
              }
          } else {
              return ["returnCode" => '0', 'message' => "Failed to fetch car details and bids"];
          }      
        
      case "validate_session":
        return doValidate($request['sessionId']);
    }
    return array("returnCode" => '0', 'message'=>"Server received request and processed");
  }

  $connection = new AMQPStreamConnection($rabbitmq, 5672, 'test', 'test', 'testHost');
  $channel = $connection->channel();

  setupMessaging($channel);

  echo "User Management Server ready to receive messages".PHP_EOL;

  $callback = function($msg) use ($channel) {
    $request = json_decode($msg->body, true);
    $response = requestProcessor($request);
    
    // Prepare the response message
    $responseMsg = new AMQPMessage(
        json_encode($response),
        array('correlation_id' => $msg->get('correlation_id'))
    );
    
    // Publish the response message to the queue specified in the reply_to header
    $channel->basic_publish(
        $responseMsg, 
        '', 
        $msg->get('reply_to')
    );

    echo 'Processed request and sent response: ', json_encode($response), "\n";
  };

  $channel->basic_consume('testQueue', '', false, true, false, false, $callback);

  while($channel->is_consuming()) {
      $channel->wait();
  }

  $channel->close();
  $connection->close();
  ?>

