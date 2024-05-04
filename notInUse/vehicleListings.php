<?php
if (!isset($_COOKIE['username'])) {
    header('Location: index.php');
    exit();
}

require_once 'logError.php';

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Vehicle Reviews</title>
        <link href="css/vehicleListings.css" rel="stylesheet"> 
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <?php
        include 'header.php';
    ?>
    <body>
    <div class="flex justify-center">
        <div class="mt-5 relative w-full md:w-3/4 lg:w-96 top-[5rem] md:top-[1rem] lg:top-0">
            <form action="submit_review.php" method="post" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="vehicle_name">
                        Vehicle Name
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="vehicle_name" name="vehicle_name" type="text" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="vehicle_model">
                        Vehicle Model
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="vehicle_model" name="vehicle_model" type="text" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="vehicle_year">
                        Vehicle Year
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="vehicle_year" name="vehicle_year" type="number" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="review_text">
                        Review
                    </label>
                    <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="review_text" name="review_text" rows="4" required></textarea>
                </div>
                <div class="flex items-center justify-between">
                    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                        Submit Review
                    </button>
                </div>
            </form>
        </div>
    </div>
            <?php include 'responsiveNavScript.php'; ?>
    </body>
</html>
