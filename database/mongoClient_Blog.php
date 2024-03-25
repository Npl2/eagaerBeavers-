<?php
use MongoDB\Driver\ServerApi;
require_once __DIR__ . '/vendor/autoload.php';

class MongoClientDB_BLOG {
    public function __construct() {
        $connectionString = "mongodb+srv://root:root@sysintg.ycowwfe.mongodb.net/?retryWrites=true&w=majority&appName=SysIntg";
        $apiVersion = new ServerApi(ServerApi::V1);
        $this->client = new MongoDB\Client($connectionString, [], ['serverApi' => $apiVersion]);
        $this->db = $this->client->selectDatabase('carDeal');
    }


    // Add a blog post
    public function insertBlogPost($username, $title, $content) {
        $collection = $this->db->selectCollection('blogposts');
        $result = $collection->insertOne([
            'author' => $username,
            'title' => $title,
            'content' => $content,
            'created_at' => new MongoDB\BSON\UTCDateTime(),
        ]);
        
        if ($result->getInsertedCount() == 1) {
            return ['success' => true, 'message' => 'Blog post successfully added.'];
        } else {
            return ['success' => false, 'message' => 'Failed to add blog post.'];
        }
    }

    // Add a comment to a blog post
    public function addComment($postId, $username, $comment) {
        $collection = $this->db->selectCollection('comments');
        $result = $collection->insertOne([
            'postId' => new MongoDB\BSON\ObjectId($postId),
            'username' => $username,
            'comment' => $comment,
            'created_at' => new MongoDB\BSON\UTCDateTime(),
        ]);
        if ($result->getInsertedCount() == 1) {
            return ['success' => true, 'message' => 'Comment successfully added.'];
        } else {
            return ['success' => false, 'message' => 'Failed to add comment.'];
        }
    }

    // get list of blog posts
    public function listBlogPosts() {
        $collection = $this->db->selectCollection('blogposts');
        $posts = $collection->find([]);
        return iterator_to_array($posts);
    }


    // get selected blog post
    public function getBlogPostWithComments($postId) {
        $postCollection = $this->db->selectCollection('blogposts');
        $commentsCollection = $this->db->selectCollection('comments');
        
        $post = $postCollection->findOne(['_id' => new MongoDB\BSON\ObjectId($postId)]);
        if (!$post) {
            return ['success' => false, 'message' => 'Blog post not found.'];
        }
        $postArray = $post->getArrayCopy();
        $comments = $commentsCollection->find(['postId' => new MongoDB\BSON\ObjectId($postId)]);
        $commentsArray = iterator_to_array($comments);
        $postArray['comments'] = $commentsArray;
        return ['success' => true, 'post' => $postArray];
    }



}


