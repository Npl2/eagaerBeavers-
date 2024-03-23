<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recall Management</title>
    <link href="css/recallManagement.css" rel="stylesheet"> 
    <!-- <link href="css/header.css" rel="stylesheet"> -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <?php include 'header.php'; ?>

    <div id="car-registrations">

    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>

        function getCarRegistrations() {
            $.ajax({
                url: 'carRegRecall.php',
                method: 'GET',
                success: function(data) {

                    $('#car-registrations').html(data);
                },
                error: function() {

                    console.log('Failed to fetch car registrations.');
                }
            });
        }

        function getRecalls(make, model, year, index) {
            $.ajax({
                url: 'getRecall.php',
                method: 'POST',
                data: { make: make, model: model, year: year },
                success: function(data) {
                    $('#recall-info-' + index).html(data); 
                    console.log(index);
                },
                error: function() {

                    console.log('Failed to fetch recalls.');
                }
            });
        }



        $(document).ready(function() {
            getCarRegistrations();
        });
    </script>
</body>
</html>
