<h1 class="siteName">EagerDrivers</h1>
<header>
            <div class="left-menu">
                <nav>
                    <ul>
                        <li><a href="vehicleListings.php">Vehicle Listings</a></li>
                        <li><a href="forum.php">Discussion Forum</a></li>
                        <li><a href="registerVehicle.php">Register Vehicle</a></li>
                        <li><a href="recallManagement.php">Recall Management</a></li>
                        <li><a href="API_test.php">API Test</a></li>
                    </ul>
                </nav>
            </div>
            <div class="right-menu">
                <span class="username">Welcome, <?php echo htmlspecialchars($_COOKIE['username']); ?>!</span>
                <a href="logout.php" class="logout-button">Logout</a>
            </div>
</header>