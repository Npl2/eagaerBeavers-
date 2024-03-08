<html>
<head>
    <title>Login Page</title>
</head>
<body>
    <h1>Login working??? page</h1>
    <div id="loginFormBox">
        <form id="loginForm" onsubmit="return false;">
            <label for="username">Username:</label>
            <input type="text" id="un" name="username"><br><br>
            <label for="password">Password:</label>
            <input type="password" id="pw" name="password"><br><br>
            <label for="email">Email:</label>
            <input type="email" id="mail" name="email"><br><br>
            <label for="firstname">First Name:</label>
            <input type="text" id="fn" name="firstname"><br><br>
            <label for="lastname">Last Name:</label>
            <input type="text" id="ln" name="lastname"><br><br>
            <button onclick="SendLoginRequest()">Login</button>
        </form>
    </div>
    <script>
        function SendLoginRequest() {
            const username = document.getElementById("un").value;
            const password = document.getElementById("pw").value;
            const email = document.getElementById("mail").value;
            const firstname = document.getElementById("fn").value;
            const lastname = document.getElementById("ln").value;

            const requestData = {
                username: username,
                password: password,
                email: email,
                firstname: firstname,
                lastname: lastname
            };

            fetch('login.php', { // Make sure this URL points to your login.php file correctly
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(requestData)
            })
            .then(response => response.json())
            .then(data => {
                console.log(data);
                handleLoginResponse(data); // Make sure this function is defined to handle the response
            })
            .catch(error => {
                console.error('Error sending login request:', error);
            });
        }


        function handleLoginResponse(response) {
            try {
                document.getElementById("textResponse").innerHTML = "Response: " + JSON.stringify(response);
                
                // Check if login was successful
                if (response.success) {
                    console.log('Login succeeded!');
                } else {
                    console.log('Login failed:', response.error);
                }
            } catch (error) {
                console.error('Error parsing login response:', error);
                document.getElementById("textResponse").innerHTML = "Error: Failed to parse response.";
            }
        }   
    </script>
</body>
</html>
