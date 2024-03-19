<?php
if (!isset($_COOKIE['username'])) {
    header('Location: index.php');
    exit();
}
?>

<?php
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Check if the necessary data is provided
    if (isset($_POST['discussionTitle']) && isset($_POST['discussionContent'])) {
        // Extract and sanitize the input data
        $title = htmlspecialchars($_POST['discussionTitle']);
        $content = htmlspecialchars($_POST['discussionContent']);

        // Here, you can perform any necessary operations, such as storing the discussion in a database

        // Redirect the user back to the forum page after creating the discussion
        header("Location: forum.php");
        exit();
    } else {
        // Handle case where required data is not provided
        echo "Error: Discussion title and content are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Discussion - Discussion Forum</title>
    <link href="css/forum.css" rel="stylesheet"> 
    <link href="css/header.css" rel="stylesheet">
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="discussionBox">
        <div class="sideBar-topics">
            <h2>Categories</h2>
            <ul>
                <li><a href="#">Cars</a></li>
                <li><a href="#">Makes</a></li>
                <li><a href="#">Models</a></li>
            </ul>
        </div>
        <div class="user-discussion">
            <h1>Create a Discussion</h1>
            
            <!-- Form to create a discussion -->
            <form id="createDiscussionForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label for="discussionTitle">Discussion Title:</label>
                    <input type="text" id="discussionTitle" name="discussionTitle" placeholder="Enter the title of your discussion" required>
                </div>
                <div class="form-group">
                    <label for="discussionContent">Discussion Content:</label>
                    <textarea id="discussionContent" name="discussionContent" rows="4" placeholder="Enter the content of your discussion" required></textarea>
                </div>
                <div class="form-group">
                    <label for="discussionType">Discussion Type:</label>
                    <select id="discussionType" name="discussionType">
                        <option value="car">Car</option>
                        <option value="make">Make</option>
                        <option value="model">Model</option>
                    </select>
                </div>
                <button class="createDiscussionButton" type="submit">Create Discussion</button>
            </form>
        </div>
    </div>

    <!-- Your scripts and additional content here -->
</body>
</html>