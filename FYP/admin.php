<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>

    <div class="navbar">
        <h1>Admin Dashboard</h1>
    </div>

    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <ul>
                <li><a href="#" onclick="showSection('dashboard')">Dashboard</a></li>
                <li><a href="#" onclick="showSection('users')">Users</a></li>
                <li><a href="#" onclick="showSection('checkAppointment')">Check Appointment</a></li>
                <li><a href="#" onclick="showSection('products')">Products</a></li>
                <li><a href="#" onclick="showSection('reports')">Reports</a></li>
                <li><a href="#" onclick="showSection('settings')">Settings</a></li>
                <li><a href="homepage.php">Logout</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Dashboard Section -->
            <div id="dashboard" class="card">
                <h2>Dashboard Overview</h2>
                <p>Welcome to the admin dashboard! Here, you can manage users, products, orders, and view reports.</p>
            </div>

            <!-- Users Section -->
            <div id="users" class="card" style="display: none;">
                <h2>Manage Users</h2>
                <p>User Management section: Add, update, or remove users here.</p>
                
            </div>

            <!-- Orders Section -->
            <div id="checkAppointment" class="card" style="display: none;">
                <h2>Appointment</h2>
                <p>Order Management section: View and process customer orders here.</p>
                <?php include 'appointmentHistory.php'; ?>
            </div>

            <!-- Products Section -->
            <div id="products" class="card" style="display: none;">
                <h2>Manage Products</h2>
                <p>Product Management section: Add new products, update or remove products.</p>
            </div>

            <!-- Reports Section -->
            <div id="reports" class="card" style="display: none;">
                <h2>Reports</h2>
                <p>View sales reports, user statistics, and other important data.</p>
            </div>

            <!-- Settings Section -->
            <div id="settings" class="card" style="display: none;">
                <h2>Settings</h2>
                <p>Configure the system settings here.</p>
            </div>
        </div>
    </div>

    <!-- Script to handle section display -->
    <script>
          // Define showSection globally
          function showSection(sectionId) {
            const sections = ['dashboard', 'users', 'checkAppointment', 'products', 'reports', 'settings'];
            sections.forEach(function(id) {
                document.getElementById(id).style.display = (id === sectionId) ? 'block' : 'none';
            });
        }

        document.addEventListener("DOMContentLoaded", function() {
            // Show the default section on page load
            showSection('dashboard');
        });
    </script>

</body>
</html>
