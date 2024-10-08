<?php include 'config.php' ?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bootstrap Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/ae360af17e.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/admin.css">
</head>

<body>
    <div class="wrapper">
        <aside id="sidebar" class="js-sidebar">
            <div class="h-100">
                <div class="sidebar-logo">
                    <a href="#">Pet Lover</a>
                </div>
                <ul class="sidebar-nav">
                    <li class="sidebar-header">Admin Elements</li>
                    <li class="sidebar-item">
                        <a href="#" class="sidebar-link" data-section="dashboard">
                            <i class="fa-solid fa-list pe-2"></i>
                            Dashboard
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="#" class="sidebar-link" data-section="appointment">
                            <i class="fa-solid fa-calendar-check pe-2"></i>
                            Appointment
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="#" class="sidebar-link" data-section="today">
                            <i class="fa-solid fa-user pe-2"></i>
                            Today appoinment
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="#" class="sidebar-link" data-section="customer">
                            <i class="fa-solid fa-user pe-2"></i>
                            Customers
                        </a>
                    </li>
                </ul>
            </div>
        </aside>
        <div class="main">
            <nav class="navbar navbar-expand px-3 border-bottom">
                <button class="btn" id="sidebar-toggle" type="button">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="navbar-collapse navbar">
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a href="#" data-bs-toggle="dropdown" class="nav-icon pe-md-0">
                                <img src="image/profile.jpg" class="avatar img-fluid rounded" alt="">
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="#" class="dropdown-item">Profile</a>
                                <a href="#" class="dropdown-item">Setting</a>
                                <a href="#" class="dropdown-item">Logout</a>
                                <a href="#" class=" theme-toggle">
                                    <i class="fa-regular fa-moon"></i>
                                    <i class="fa-regular fa-sun"></i>
                                </a>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
            <main class="content px-3 py-2">
                <div class="container-fluid">
                    <div class="mb-3">
                        <h4>Admin Dashboard</h4>
                    </div>
                    <!-- Dashboard Section -->
                    <div id="dashboard-section">
                        <h4>Dashboard</h4>
                        <p>Welcome to the admin dashboard! Here you can manage your appointments and view statistics.</p>
                        <?php
                        // Count total customers
                        $customerCountQuery = "SELECT COUNT(*) AS totalCustomers FROM Customer";
                        $customerCountResult = $conn->query($customerCountQuery);
                        $totalCustomers = $customerCountResult->fetch_assoc()['totalCustomers'];

                        // Count total pets
                        $petCountQuery = "SELECT COUNT(*) AS totalPets FROM Pets";
                        $petCountResult = $conn->query($petCountQuery);
                        $totalPets = $petCountResult->fetch_assoc()['totalPets'];

                        // Count appointments this week
                        $appointmentThisWeekQuery = "
                            SELECT COUNT(*) AS appointmentsThisWeek 
                            FROM bookings 
                            WHERE WEEK(bookingDate) = WEEK(CURDATE()) 
                            AND YEAR(bookingDate) = YEAR(CURDATE())
                        ";
                        $appointmentThisWeekResult = $conn->query($appointmentThisWeekQuery);
                        $appointmentsThisWeek = $appointmentThisWeekResult->fetch_assoc()['appointmentsThisWeek'];

                        // Count pending appointments
                        $pendingAppointmentsQuery = "SELECT COUNT(*) AS pendingAppointments FROM bookings WHERE BookingStatus = 'Pending'";
                        $pendingAppointmentsResult = $conn->query($pendingAppointmentsQuery);
                        $pendingAppointments = $pendingAppointmentsResult->fetch_assoc()['pendingAppointments'];
                        ?>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="card text-white bg-primary mb-3">
                                    <div class="card-header">Total Customers</div>
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo $totalCustomers; ?></h5>
                                        <p class="card-text">Number of registered customers.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-white bg-success mb-3">
                                    <div class="card-header">Total Pets</div>
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo $totalPets; ?></h5>
                                        <p class="card-text">Number of registered pets.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-white bg-warning mb-3">
                                    <div class="card-header">Appointments This Week</div>
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo $appointmentsThisWeek; ?></h5>
                                        <p class="card-text">Appointments scheduled for this week.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-white bg-danger mb-3">
                                    <div class="card-header">Pending Appointments</div>
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo $pendingAppointments; ?></h5>
                                        <p class="card-text">Appointments awaiting confirmation.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Appointment Section -->
                    <div id="appointment-section" class="d-none">
                        <h4>View Appointments</h4>
                        <div class="row">
                            <div class="col-12">
                                <div class="card border-0">
                                    <div class="card-header">
                                        <h5 class="card-title">Filter Appointments</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="bookingStatusFilter" class="form-label">Booking Status</label>
                                            <select id="bookingStatusFilter" class="form-select">
                                                <option value="">All</option>
                                                <option value="Booked">Booked</option>
                                                <option value="On Hold">On Hold</option>
                                                <option value="Cancelled">Cancelled</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="paymentStatusFilter" class="form-label">Payment Status</label>
                                            <select id="paymentStatusFilter" class="form-select">
                                                <option value="">All</option>
                                                <option value="Paid">Paid</option>
                                                <option value="Pending">Pending</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="searchInput" class="form-label">Search</label>
                                            <input type="text" class="form-control" id="searchInput" placeholder="Search by Booking ID, Pet Name, or Service Name">
                                        </div>
                                        <button id="filterBtn" class="btn btn-primary">Filter</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card border-0">
                                    <div class="card-header">
                                        <h5 class="card-title">Appointments</h5>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Booking ID</th>
                                                    <th>Pet Name</th>
                                                    <th>Service Name</th>
                                                    <th>Booking Date</th>
                                                    <th>Booking Time</th>
                                                    <th>Booking Status</th>
                                                    <th>Payment Status</th>
                                                </tr>
                                            </thead>
                                            <tbody id="appointment-table-body">
                                                <!-- Appointments will be displayed here -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Today Appointment Section -->
                    <div id="today-section" class="d-none">
                        <h4>View Appointments</h4>
                        <div class="row">
                            <div class="col-12">
                                <div class="card border-0">
                                    <div class="card-header">
                                        <h5 class="card-title">Filter Appointments</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="todaybookingStatusFilter" class="form-label">Booking Status</label>
                                            <select id="todaybookingStatusFilter" class="form-select">
                                                <option value="">All</option>
                                                <option value="Booked">Booked</option>
                                                <option value="On Hold">On Hold</option>
                                                <option value="Cancelled">Cancelled</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="todaypaymentStatusFilter" class="form-label">Payment Status</label>
                                            <select id="todaypaymentStatusFilter" class="form-select">
                                                <option value="">All</option>
                                                <option value="Paid">Paid</option>
                                                <option value="Pending">Pending</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="todaysearchInput" class="form-label">Search</label>
                                            <input type="text" class="form-control" id="todaysearchInput" placeholder="Search by Booking ID, Pet Name, or Service Name">
                                        </div>
                                        <button id="todayfilterBtn" class="btn btn-primary">Filter</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card border-0">
                                    <div class="card-header">
                                        <h5 class="card-title">Appointments</h5>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Booking ID</th>
                                                    <th>Pet Name</th>
                                                    <th>Service Name</th>
                                                    <th>Booking Date</th>
                                                    <th>Booking Time</th>
                                                    <th>Booking Status</th>
                                                    <th>Payment Status</th>
                                                    <th>Done</th>
                                                    <th>Cash payment</th>
                                                </tr>
                                            </thead>
                                            <tbody id="today-appointment-table-body">
                                                <!-- Appointments will be displayed here -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Customer Table Section -->
                    <div id="customer-section" class="d-none">
                        <h4>Manage Customers</h4>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Customer ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone Number</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $customerQuery = "SELECT * FROM customer";
                                $customerResult = $conn->query($customerQuery);

                                if ($customerResult->num_rows > 0) {
                                    while ($customer = $customerResult->fetch_assoc()) {
                                        echo "
                                        <tr>
                                            <td>{$customer['CustomerID']}</td>
                                            <td>{$customer['CustomerName']}</td>
                                            <td>{$customer['Email']}</td>
                                            <td>{$customer['PhoneNumber']}</td>
                                            <td>
                                                <button class='btn btn-primary edit-btn' data-customer-id='{$customer['CustomerID']}'>Edit</button>
                                                <button class='btn btn-danger delete-btn' data-customer-id='{$customer['CustomerID']}'>Delete</button>
                                            </td>
                                        </tr>
                                        ";
                                    }
                                } else {
                                    echo "<tr><td colspan='5' class='text-center'>No customers found.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Edit Customer Modal -->
                    <div class="modal fade" id="editCustomerModal" tabindex="-1" aria-labelledby="editCustomerModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editCustomerModalLabel">Edit Customer</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="editCustomerForm">
                                        <input type="hidden" id="editCustomerId" name="CustomerID">
                                        <div class="mb-3">
                                            <label for="editCustomerName" class="form-label">Customer Name</label>
                                            <input type="text" class="form-control" id="editCustomerName" name="CustomerName" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="editCustomerEmail" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="editCustomerEmail" name="Email" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="editCustomerPhone" class="form-label">Phone Number</label>
                                            <input type="text" class="form-control" id="editCustomerPhone" name="PhoneNumber" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="editCustomerPassword" class="form-label">Password</label>
                                            <input type="text" class="form-control" id="editCustomerPassword" name="Password" required>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-success">Save Changes</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>



                </div>
            </main>
            
            <footer class="footer">
                <div class="container-fluid">
                    <div class="row text-muted">
                        <div class="col-6 text-start">
                            <p class="mb-0">
                                <a href="#" class="text-muted">
                                    <strong>Pet Lover</strong>
                                </a>
                            </p>
                        </div>
                        <div class="col-6 text-end">
                            <ul class="list-inline">
                                <li class="list-inline-item">
                                    <a href="#" class="text-muted">Contact</a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="#" class="text-muted">About Us</a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="#" class="text-muted">Terms</a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="#" class="text-muted">Booking</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
    <script>
       // JavaScript to switch between sections
        document.querySelectorAll('.sidebar-link').forEach(link => {
            link.addEventListener('click', function (event) {
                event.preventDefault(); // Prevent default anchor behavior
                const section = this.getAttribute('data-section');

                // Hide all sections
                document.getElementById('dashboard-section').classList.add('d-none');
                document.getElementById('appointment-section').classList.add('d-none');
                document.getElementById('customer-section').classList.add('d-none');
                document.getElementById('today-section').classList.add('d-none');
                // Show the selected section
                if (section === 'dashboard') {
                    document.getElementById('dashboard-section').classList.remove('d-none');
                } else if (section === 'appointment') {
                    document.getElementById('appointment-section').classList.remove('d-none');
                    fetchAppointments();
                } else if (section === 'customer') {
                    document.getElementById('customer-section').classList.remove('d-none');
                    fetchCustomers(); // Fetch customer data when navigating to the customer section
                } else if (section === 'today') {
                    document.getElementById('today-section').classList.remove('d-none');
                    fetchTodayAppointments();
                }
            });
        });
        document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function() {
            const customerId = this.getAttribute('data-customer-id');
            // Fetch customer data using AJAX
            fetch(`get_customer.php?CustomerID=${customerId}`)
                        .then(response => response.json())
                        .then(customer => {
                            // Populate modal fields with customer data
                            document.getElementById('editCustomerId').value = customer.CustomerID;
                            document.getElementById('editCustomerName').value = customer.CustomerName;
                            document.getElementById('editCustomerEmail').value = customer.Email;
                            document.getElementById('editCustomerPhone').value = customer.PhoneNumber;
                            document.getElementById('editCustomerPassword').value = '***';
                            // Show the modal
                            var editModal = new bootstrap.Modal(document.getElementById('editCustomerModal'));
                            editModal.show();
                        })
                        .catch(error => console.error('Error fetching customer data:', error));
            });
        });
        
        // Handle form submission
        document.getElementById('editCustomerForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);

                // Submit updated customer data via AJAX
                fetch('update_customer.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(result => {
                    alert(result); // You can show a success message here
                    location.reload(); // Reload page after success
                })
                .catch(error => console.error('Error updating customer:', error));
        });
        
        // Function to fetch customer data
        function fetchCustomers() {
            fetch('fetch_customers.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            })
            .then(response => response.text())
            .then(data => {
                document.getElementById('customer-table-body').innerHTML = data;
            })
            .catch(error => console.error('Error fetching customers:', error));
        }

        // Function to fetch appointments
        function fetchAppointments(bookingStatus = '', paymentStatus = '', searchTerm = '') {
            fetch('fetch_appointments.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `bookingStatus=${bookingStatus}&paymentStatus=${paymentStatus}&searchTerm=${searchTerm}`
            })
            .then(response => response.text())
            .then(data => {
                document.getElementById('appointment-table-body').innerHTML = data;
            })
            .catch(error => console.error('Error fetching appointments:', error));
        }

        // Function to fetch appointments based on filters
        function fetchFilteredAppointments() {
            const bookingStatus = document.getElementById('bookingStatusFilter').value;
            const paymentStatus = document.getElementById('paymentStatusFilter').value;
            const searchTerm = document.getElementById('searchInput').value;
            
            fetchAppointments(bookingStatus, paymentStatus, searchTerm); // Fetch filtered appointments
        }

        // Function to fetch appointments
        function fetchTodayAppointments(bookingStatus = '', paymentStatus = '', searchTerm = '') {
            fetch('fetch_today_appointments.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `bookingStatus=${bookingStatus}&paymentStatus=${paymentStatus}&searchTerm=${searchTerm}`
            })
            .then(response => response.text())
            .then(data => {
                document.getElementById('today-appointment-table-body').innerHTML = data;
            })
            .catch(error => console.error('Error fetching appointments:', error));
        }
        // Function to fetch appointments based on filters
        function fetchFilteredTodayAppointments() {
            const bookingStatus = document.getElementById('todaybookingStatusFilter').value;
            const paymentStatus = document.getElementById('todaypaymentStatusFilter').value;
            const searchTerm = document.getElementById('todaysearchInput').value;
            
            fetchTodayAppointments(bookingStatus, paymentStatus, searchTerm); // Fetch filtered appointments
        }
        // Event listener for Appointments
        document.getElementById('filterBtn').addEventListener('click', fetchFilteredAppointments);
        document.getElementById('bookingStatusFilter').addEventListener('change', fetchFilteredAppointments);
        document.getElementById('paymentStatusFilter').addEventListener('change', fetchFilteredAppointments);
        document.getElementById('searchInput').addEventListener('input', fetchFilteredAppointments);
        // Event listener for Today Appointments
        document.getElementById('todayfilterBtn').addEventListener('click', fetchFilteredTodayAppointments);
        document.getElementById('todaybookingStatusFilter').addEventListener('change', fetchFilteredTodayAppointments);
        document.getElementById('todaypaymentStatusFilter').addEventListener('change', fetchFilteredTodayAppointments);
        document.getElementById('todaysearchInput').addEventListener('input', fetchFilteredTodayAppointments);

    </script>
</body>

</html>
