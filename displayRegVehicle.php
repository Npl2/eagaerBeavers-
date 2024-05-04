<?php
    use PhpAmqpLib\Connection\AMQPStreamConnection;
    use PhpAmqpLib\Message\AMQPMessage;
    require 'vendor/autoload.php';
    require_once('path.inc');
    require_once('get_host_info.inc');
    require_once('rabbitMQLib.inc');
    require_once 'logError.php';

    $client = new rabbitMQClient("testRabbitMQ.ini", "testServer");
    $exchangeName = 'user_auth';
    $routingKey = 'user_management';

    $request = [
        'type' => 'get_user_car_regs',
        'username' => $_COOKIE['username'] ?? null,
    ];

    $response = $client->send_request($request, $exchangeName, $routingKey);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Car Registrations</title>
    <link href="https://cdn.tailwindcss.com" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#checkRecalls").click(function() {
                $(this).text('Checking...').prop('disabled', true);
                $.ajax({
                    url: 'emailSender.php',
                    type: 'POST',
                    data: {
                        username: '<?= $_COOKIE['username'] ?>'
                    },
                    success: function(response) {
                        alert("Check complete: " + response);
                        $("#checkRecalls").text('Check for New Recalls').prop('disabled', false);
                    },
                    error: function() {
                        alert("Error checking for recalls.");
                        $("#checkRecalls").text('Check for New Recalls').prop('disabled', false);
                    }
                });
            });
        });
    </script>
      <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
<?php include 'header.php'; ?>
<div class="relative top-[5rem] lg:mt-0 h-screen flex items-start justify-center">
    <div class="p-5 w-4/5 shadow-md rounded">
        <h4 class="font-bold capitalize text-center text-lg mb-4">Registered Vehicles</h4>

        <?php if ($response && $response['message'] == "Car registrations fetched successfully"): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
                <?php foreach ($response['data'] as $carReg): ?>
                    <div class="p-4 bg-white rounded-md shadow">
                        <div>
                            <p class="mb-2 font-medium capitalize">Make: <span class="font-normal"><?= $carReg['make'] ?></span></p>
                            <p class="mb-2 font-medium capitalize">Model: <span class="font-normal"><?= $carReg['model'] ?></span></p>
                            <p class="mb-2 capitalize">Year: <span class="font-normal"><?= $carReg['year'] ?></span></p>
                            <p class="font-medium capitalize">On Sale: <?= $carReg['on_sale'] ? '<span class="text-green-500">Yes</span>' : '<span class="text-red-500">No</span>' ?></p>
                        </div>
                        <form action="update_car_sale_status.php" method="post" class="mt-4">
                            <input type="hidden" name="carId" value="<?= $carReg['_id']['$oid'] ?>">
                            <div class="mb-2">
                                <label class="block text-sm font-medium text-gray-700">Sale Price</label>
                                <input type="number" name="salePrice" placeholder="Enter sale price" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                            <div class="mt-2">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="onSale" value="true" class="form-radio text-green-500" <?= $carReg['on_sale'] ? 'checked' : '' ?>>
                                    <span class="ml-2">On Sale</span>
                                </label>
                                <label class="inline-flex items-center ml-6">
                                    <input type="radio" name="onSale" value="false" class="form-radio text-red-500" <?= !$carReg['on_sale'] ? 'checked' : '' ?>>
                                    <span class="ml-2">Not on Sale</span>
                                </label>
                            </div>
                            <button type="submit" class="mt-2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Update Sale Status</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
            <button id="checkRecalls" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mt-4">Check for New Recalls</button>
        <?php else: ?>
            <p class="text-center bg-red-500 text-white p-2 rounded">Failed to fetch car registrations.</p>
        <?php endif; ?>
    </div>
</div>

<?php include 'responsiveNavScript.php'; ?>
</body>
</html>