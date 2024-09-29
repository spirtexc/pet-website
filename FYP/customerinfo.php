<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['ID'])) {
    header("Location: login.php");
    exit();
}

// Fetch customer details
$stmt = $conn->prepare("SELECT CustomerName, Email, PhoneNumber, Password FROM Customer WHERE CustomerID = ?");
$stmt->bind_param("i", $_SESSION['ID']);
$stmt->execute();
$result = $stmt->get_result();
$customer = $result->fetch_assoc();
$stmt->close();

// Fetch pet details
$stmt = $conn->prepare("SELECT PetID, PetName, Species, DateOfBirth, Weight, Allergies FROM Pets WHERE CustomerID = ?");
$stmt->bind_param("i", $_SESSION['ID']);
$stmt->execute();
$petResult = $stmt->get_result();
$pets = $petResult->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Update customer info if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $customerName = $_POST['username'];
    $email = $_POST['email'];
    $phoneNumber = $_POST['phone'];
    $newPassword = $_POST['new_password'];

    // Prepare to update customer info
    $updateSql = "UPDATE Customer SET CustomerName=?, Email=?, PhoneNumber=?";
    if (!empty($newPassword)) {
        $updateSql .= ", Password=?";
    }
    $updateSql .= " WHERE CustomerID=?";

    $updateStmt = $conn->prepare($updateSql);
    if (!empty($newPassword)) {
        $newPassword = password_hash($newPassword, PASSWORD_DEFAULT); // Hash the password
        $updateStmt->bind_param("ssssi", $customerName, $email, $phoneNumber, $newPassword, $_SESSION['ID']);
    } else {
        $updateStmt->bind_param("sssi", $customerName, $email, $phoneNumber, $_SESSION['ID']);
    }

    if ($updateStmt->execute()) {
        echo "<script>alert('Your information has been updated successfully.');</script>";
    } else {
        echo "Error: " . $conn->error;
    }
    $updateStmt->close();
}

// Update pet info if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_pet'])) {
    foreach ($_POST['pets'] as $pet) {
        $petID = $pet['id'];
        $petName = $pet['name'];
        $species = $pet['species'];
        $dob = $pet['dob'];
        $weight = $pet['weight'];
        $allergies = isset($pet['allergies']) ? $pet['allergies'] : '';

        // Prepare SQL statement to update pet data
        $stmt = $conn->prepare("UPDATE Pets SET PetName=?, Species=?, DateOfBirth=?, Weight=?, Allergies=? WHERE PetID=? AND CustomerID=?");
        $stmt->bind_param("sssdssi", $petName, $species, $dob, $weight, $allergies, $petID, $_SESSION['ID']);
        $stmt->execute();
    }

    echo "<script>alert('Pet information updated successfully!');</script>";
    $stmt->close();
}

// Add new pets
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_pet'])) {
    foreach ($_POST['pets'] as $pet) {
        $petName = $pet['name'];
        $species = $pet['species'];
        $dob = $pet['dob'];
        $weight = $pet['weight'];
        $allergies = isset($pet['allergies']) ? $pet['allergies'] : '';

        // Prepare SQL statement to insert pet data
        $stmt = $conn->prepare("INSERT INTO Pets (CustomerID, PetName, Species, DateOfBirth, Weight, Allergies) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssis", $_SESSION['ID'], $petName, $species, $dob, $weight, $allergies);
        $stmt->execute();
    }

    // Redirect to refresh the page after adding pets
    echo "<script>alert('Pets added successfully!'); window.location.href='customerinfo.php';</script>";
    $stmt->close();
}

// Delete pet if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_pet'])) {
    $petID = $_POST['pet_id'];

    $deleteStmt = $conn->prepare("DELETE FROM Pets WHERE PetID=? AND CustomerID=?");
    $deleteStmt->bind_param("ii", $petID, $_SESSION['ID']);
    if ($deleteStmt->execute()) {
        echo "<script>alert('Pet deleted successfully!'); window.location.href='customerinfo.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
    $deleteStmt->close();
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Info</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/customerinfo.css">
    <style>
        .collapsible {
            background-color: #777;
            color: white;
            cursor: pointer;
            padding: 10px;
            width: 100%;
            border: none;
            text-align: left;
            outline: none;
            font-size: 15px;
        }

        .active, .collapsible:hover {
            background-color: #555;
        }

        .content {
            padding: 0 18px;
            display: none;
            overflow: hidden;
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Update Your Information</h1>
    <a onclick="javascript:history.go(-1)" class="btn btn-secondary mb-3">Return</a>
    
    <form method="POST">
        <div class="form-group">
            <label for="username">Name</label>
            <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($customer['CustomerName']); ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($customer['Email']); ?>" required>
        </div>
        <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($customer['PhoneNumber']); ?>" required>
        </div>
        <div class="form-group">
            <label for="new_password">Password</label>
            <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Leave blank to keep current password">
            <small class="form-text text-muted">Change the password if needed.</small>
        </div>
        <button type="submit" name="update" class="btn btn-primary">Update Info</button>
    </form>

    <h2>Your Pets</h2>
<div id="pets-list">
    <form method="POST">
        <?php if (!empty($pets)): ?>
            <?php foreach ($pets as $pet): ?>
                <div class="collapsible">
                    <strong><?php echo htmlspecialchars($pet['PetName']); ?></strong>
                </div>
                <div class="content">
                    <input type="hidden" name="pets[<?php echo $pet['PetID']; ?>][id]" value="<?php echo $pet['PetID']; ?>">
                    <div class="form-group">
                        <label for="name_<?php echo $pet['PetID']; ?>">Pet Name</label>
                        <input type="text" class="form-control" id="name_<?php echo $pet['PetID']; ?>" name="pets[<?php echo $pet['PetID']; ?>][name]" value="<?php echo htmlspecialchars($pet['PetName']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="species_<?php echo $pet['PetID']; ?>">Species</label>
                        <input type="text" class="form-control" id="species_<?php echo $pet['PetID']; ?>" name="pets[<?php echo $pet['PetID']; ?>][species]" value="<?php echo htmlspecialchars($pet['Species']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="dob_<?php echo $pet['PetID']; ?>">Date of Birth</label>
                        <input type="date" class="form-control" id="dob_<?php echo $pet['PetID']; ?>" name="pets[<?php echo $pet['PetID']; ?>][dob]" value="<?php echo htmlspecialchars($pet['DateOfBirth']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="weight_<?php echo $pet['PetID']; ?>">Weight</label>
                        <input type="number" step="0.01" class="form-control" id="weight_<?php echo $pet['PetID']; ?>" name="pets[<?php echo $pet['PetID']; ?>][weight]" value="<?php echo htmlspecialchars($pet['Weight']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="allergies_<?php echo $pet['PetID']; ?>">Allergies</label>
                        <input type="text" class="form-control" id="allergies_<?php echo $pet['PetID']; ?>" name="pets[<?php echo $pet['PetID']; ?>][allergies]" value="<?php echo htmlspecialchars($pet['Allergies']); ?>" placeholder="Allergies (optional)">
                    </div>
                    <button type="button" class="btn btn-danger" onclick="confirmDelete(<?php echo $pet['PetID']; ?>)">Delete Pet</button>
                </div>
            <?php endforeach; ?>
            <button type="submit" name="update_pet" class="btn btn-primary">Update Pets</button>
        <?php else: ?>
            <p>No pets found. Add your first pet below.</p>
        <?php endif; ?>
    </form>
</div>
    <h2>Add New Pets</h2>
    <form method="POST">
        <div id="new-pet-forms">
            <div class="form-group">
                <label for="petName">Pet Name</label>
                <input type="text" class="form-control" id="petName" name="pets[0][name]" required>
            </div>
            <div class="form-group">
                <label for="species">Species</label>
                <input type="text" class="form-control" id="species" name="pets[0][species]" required>
            </div>
            <div class="form-group">
                <label for="dob">Date of Birth</label>
                <input type="date" class="form-control" id="dob" name="pets[0][dob]" required>
            </div>
            <div class="form-group">
                <label for="weight">Weight</label>
                <input type="number" class="form-control" id="weight" name="pets[0][weight]" required>
            </div>
            <div class="form-group">
                <label for="allergies">Allergies</label>
                <input type="text" class="form-control" id="allergies" name="pets[0][allergies]">
            </div>
        </div>
        <button type="button" class="btn btn-secondary" onclick="addPet()">Add Another Pet</button>
        <button type="submit" name="add_pet" class="btn btn-primary">Submit Pets</button>
    </form>
</div>

<script>
    let petIndex = 1;
    function previouspage(){
        header("location:javascript://history.go(-1)");
    }
    function addPet() {
        const newPetForms = document.getElementById('new-pet-forms');
        const petForm = `
            <div class="form-group">
                <label for="petName${petIndex}">Pet Name</label>
                <input type="text" class="form-control" id="petName${petIndex}" name="pets[${petIndex}][name]" required>
            </div>
            <div class="form-group">
                <label for="species${petIndex}">Species</label>
                <input type="text" class="form-control" id="species${petIndex}" name="pets[${petIndex}][species]" required>
            </div>
            <div class="form-group">
                <label for="dob${petIndex}">Date of Birth</label>
                <input type="date" class="form-control" id="dob${petIndex}" name="pets[${petIndex}][dob]" required>
            </div>
            <div class="form-group">
                <label for="weight${petIndex}">Weight</label>
                <input type="number" class="form-control" id="weight${petIndex}" name="pets[${petIndex}][weight]" required>
            </div>
            <div class="form-group">
                <label for="allergies${petIndex}">Allergies</label>
                <input type="text" class="form-control" id="allergies${petIndex}" name="pets[${petIndex}][allergies]">
            </div>
        `;
        newPetForms.insertAdjacentHTML('beforeend', petForm);
        petIndex++;
    }

    function confirmDelete(petID) {
        if (confirm("Are you sure you want to delete this pet?")) {
            const form = document.createElement("form");
            form.method = "POST";
            const input = document.createElement("input");
            input.type = "hidden";
            input.name = "pet_id";
            input.value = petID;
            const deleteInput = document.createElement("input");
            deleteInput.type = "hidden";
            deleteInput.name = "delete_pet";
            form.appendChild(input);
            form.appendChild(deleteInput);
            document.body.appendChild(form);
            form.submit();
        }
    }

    const collapsibleButtons = document.querySelectorAll('.collapsible');
    collapsibleButtons.forEach(button => {
        button.addEventListener('click', function() {
            this.classList.toggle('active');
            let content = this.nextElementSibling;
            content.style.display = (content.style.display === 'block') ? 'none' : 'block';
        });
    });
</script>
</body>
</html>
