<header class="w-full fixed lg:sticky lg:top-0 bg-blue-800 z-50">
        <nav class="m-auto w-11/12 flex items-center justify-between py-3">
            <h1 class="font-bold text-white text-2xl">EagerDrivers</h1>

            <span id="menu" class="inline-block lg:hidden text-white" data-feather="menu"></span>

            <!-- desktop menu -->
            <div class="hidden lg:flex items-center text-white">
                <ul class="flex items-center">
                    <li>
                        <a class="hover:bg-blue-700 hover:font-bold rounded px-2 py-2 transition duration-200" href="seachVehicleReviews.php">Vehicle Reviews</a>
                    </li>
                    <li>
                        <a class="hover:bg-blue-700 hover:font-bold rounded px-2 py-2 transition duration-200" href="forum.php">Discussion Forum</a>
                    </li>
                    <li>
                        <a class="hover:bg-blue-700 hover:font-bold rounded px-2 py-2 transition duration-200" href="registerVehicle.php">Register Vehicle</a>
                    </li>
                    <li>
                        <a class="hover:bg-blue-700 hover:font-bold rounded px-2 py-2 transition duration-200" href="recallManagement.php">Recall Management</a>
                    </li>
                    <li>
                        <a class="hover:bg-blue-700 hover:font-bold rounded px-2 py-2 transition duration-200" href="displayRegVehicle.php">User Vehicles</a>
                    </li>
                    <li>
                        <a class="hover:bg-blue-700 hover:font-bold rounded px-2 py-2 transition duration-200" href="search_cars.php">Search Cars</a>
                    </li>
                    <li>
                        <a class="hover:bg-blue-700 hover:font-bold rounded px-2 py-2 transition duration-200" href="toDoDisplay.php">Todo</a>
                    </li>
                </ul>
                <span class="ml-4 text-blue-800 bg-white px-3 py-1 rounded-lg">Welcome, <?= $_COOKIE['username'] ?></span>
                <a class="ml-4 bg-white text-blue-800 font-bold rounded px-3 py-2 transition duration-200" href="logout.php">Logout</a>
            </div>
        </nav>
    </header>

    <!-- mobile menu -->
    <header id="mobileNav" class="fixed w-full top-14 block lg:hidden z-50 hidden">
        <div class="flex flex-col items-start text-white bg-gray-300">
        <p class="capitalize py-3 px-2 border-b border-gray-200 w-full text-center font-medium text-xl text-blue-800">Welcome, <?= $_COOKIE['username'] ?></p>
            <ul class="flex flex-col w-full font-semibold uppercase">
                <li class="py-3 border-b border-gray-200 w-full">
                    <a class="hover:bg-blue-700 hover:font-bold rounded px-2 py-2 transition duration-200" href="seachVehicleReviews.php">Vehicle Reviews</a>
                </li>
                <li class="py-3 border-b border-gray-200 w-full">
                    <a class="hover:bg-blue-700 hover:font-bold rounded px-2 py-2 transition duration-200" href="forum.php">Discussion Forum</a>
                </li>
                <li class="py-3 border-b border-gray-200 w-full">
                    <a class="hover:bg-blue-700 hover:font-bold rounded px-2 py-2 transition duration-200" href="registerVehicle.php">Register Vehicle</a>
                </li>
                <li class="py-3 border-b border-gray-200 w-full">
                    <a class="hover:bg-blue-700 hover:font-bold rounded px-2 py-2 transition duration-200" href="recallManagement.php">Recall Management</a>
                </li>
                <li class="py-3 border-b border-gray-200 w-full">
                    <a class="hover:bg-blue-700 hover:font-bold rounded px-2 py-2 transition duration-200" href="displayRegVehicle.php">User Vehicles</a>
                </li>
                <li class="py-3 border-b border-gray-200 w-full">
                    <a class="hover:bg-blue-700 hover:font-bold rounded px-2 py-2 transition duration-200" href="search_cars.php">Search Cars</a>
                </li>
                <li class="py-3 border-b border-gray-200 w-full">
                    <a class="hover:bg-blue-700 hover:font-bold rounded px-2 py-2 transition duration-200" href="toDoDisplay.php">Todo</a>
                </li>
                <li class="py-3 w-full">
                    <a class="text-blue-800 px-2 py-1 font-bold" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </header>