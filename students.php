<?php
// students.php
session_start();
include 'school_db_connect.php';
include 'school_header.php';

// Handle form submission for adding new student
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_student'])) {
    $first_name = $conn->real_escape_string($_POST['first_name']);
    $last_name = $conn->real_escape_string($_POST['last_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $dob = $conn->real_escape_string($_POST['date_of_birth']);
    $gender = $conn->real_escape_string($_POST['gender']);
    $address = $conn->real_escape_string($_POST['address']);
    $enrollment_date = $conn->real_escape_string($_POST['enrollment_date']);
    $department_id = intval($_POST['department_id']);
    
    $sql = "INSERT INTO students (first_name, last_name, email, phone, date_of_birth, gender, address, enrollment_date, department_id)
            VALUES ('$first_name', '$last_name', '$email', '$phone', '$dob', '$gender', '$address', '$enrollment_date', $department_id)";
    
    if ($conn->query($sql)) {
        echo '<div class="alert success">Student added successfully!</div>';
    } else {
        echo '<div class="alert error">Error adding student: ' . $conn->error . '</div>';
    }
}

// Handle student deletion
if (isset($_GET['delete'])) {
    $student_id = intval($_GET['delete']);
    $conn->query("DELETE FROM students WHERE student_id = $student_id");
    echo '<div class="alert success">Student deleted successfully!</div>';
}
?>

<h2>Students Management</h2>

<!-- Add Student Form -->
<div class="card">
    <h3><i class="fas fa-user-plus"></i> Add New Student</h3>
    <form method="POST">
        <input type="hidden" name="add_student" value="1">
        <div class="form-row">
            <div class="form-group">
                <label for="first_name">First Name:</label>
                <input type="text" id="first_name" name="first_name" required>
            </div>
            <div class="form-group">
                <label for="last_name">Last Name:</label>
                <input type="text" id="last_name" name="last_name" required>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone:</label>
                <input type="tel" id="phone" name="phone">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="date_of_birth">Date of Birth:</label>
                <input type="date" id="date_of_birth" name="date_of_birth" required>
            </div>
            <div class="form-group">
                <label for="gender">Gender:</label>
                <select id="gender" name="gender" required>
                    <option value="">Select Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="enrollment_date">Enrollment Date:</label>
                <input type="date" id="enrollment_date" name="enrollment_date" required>
            </div>
            <div class="form-group">
                <label for="department_id">Department:</label>
                <select id="department_id" name="department_id">
                    <option value="">Select Department</option>
                    <?php
                    $departments = $conn->query("SELECT * FROM departments ORDER BY department_name");
                    while($dept = $departments->fetch_assoc()) {
                        echo '<option value="' . $dept['department_id'] . '">' . $dept['department_name'] . '</option>';
                    }
                    ?>
                </select>
            </div>
        </div>
        
        <div class="form-group">
            <label for="address">Address:</label>
            <textarea id="address" name="address" rows="3"></textarea>
        </div>
        
        <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Add Student</button>
    </form>
</div>

<!-- Students List -->
<div class="card">
    <h3><i class="fas fa-users"></i> All Students</h3>
    
    <!-- Search Form -->
    <div class="search-box">
        <form method="GET" action="">
            <input type="text" name="search" placeholder="Search students..." 
                   value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <button type="submit"><i class="fas fa-search"></i></button>
        </form>
    </div>
    
    <?php
    // Build search query
    $search_condition = "";
    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $conn->real_escape_string($_GET['search']);
        $search_condition = "WHERE s.first_name LIKE '%$search%' 
                            OR s.last_name LIKE '%$search%' 
                            OR s.email LIKE '%$search%' 
                            OR d.department_name LIKE '%$search%'";
    }
    
    $students = $conn->query("SELECT s.*, d.department_name 
                             FROM students s 
                             LEFT JOIN departments d ON s.department_id = d.department_id
                             $search_condition
                             ORDER BY s.last_name, s.first_name");
    
    if ($students->num_rows > 0) {
        echo '<table class="data-table">';
        echo '<thead>';
        echo '<tr>';
        echo '<th>ID</th>';
        echo '<th>Name</th>';
        echo '<th>Email</th>';
        echo '<th>Phone</th>';
        echo '<th>Department</th>';
        echo '<th>Enrollment Date</th>';
        echo '<th>Actions</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        
        while($row = $students->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $row['student_id'] . '</td>';
            echo '<td>' . $row['first_name'] . ' ' . $row['last_name'] . '</td>';
            echo '<td>' . $row['email'] . '</td>';
            echo '<td>' . $row['phone'] . '</td>';
            echo '<td>' . ($row['department_name'] ?: 'N/A') . '</td>';
            echo '<td>' . $row['enrollment_date'] . '</td>';
            echo '<td class="actions">';
            echo '<a href="student_details.php?id=' . $row['student_id'] . '" class="btn-view" title="View Details"><i class="fas fa-eye"></i></a>';
            echo '<a href="edit_student.php?id=' . $row['student_id'] . '" class="btn-edit" title="Edit"><i class="fas fa-edit"></i></a>';
            echo '<a href="?delete=' . $row['student_id'] . '" class="btn-delete" title="Delete" onclick="return confirm(\'Are you sure?\')"><i class="fas fa-trash"></i></a>';
            echo '</td>';
            echo '</tr>';
        }
        
        echo '</tbody>';
        echo '</table>';
    } else {
        echo '<p class="no-data">No students found.</p>';
    }
    ?>
</div>

<?php include 'school_footer.php'; ?>