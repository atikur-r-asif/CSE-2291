<?php
// teachers.php
session_start();
include 'school_db_connect.php';
include 'school_header.php';

// Handle form submission for adding new teacher
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_teacher'])) {
    $first_name = $conn->real_escape_string($_POST['first_name']);
    $last_name = $conn->real_escape_string($_POST['last_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $dob = $conn->real_escape_string($_POST['date_of_birth']);
    $gender = $conn->real_escape_string($_POST['gender']);
    $qualification = $conn->real_escape_string($_POST['qualification']);
    $specialization = $conn->real_escape_string($_POST['specialization']);
    $hire_date = $conn->real_escape_string($_POST['hire_date']);
    $department_id = intval($_POST['department_id']);
    
    $sql = "INSERT INTO teachers (first_name, last_name, email, phone, date_of_birth, gender, 
            qualification, specialization, hire_date, department_id)
            VALUES ('$first_name', '$last_name', '$email', '$phone', '$dob', '$gender', 
            '$qualification', '$specialization', '$hire_date', $department_id)";
    
    if ($conn->query($sql)) {
        echo '<div class="alert success">Teacher added successfully!</div>';
    } else {
        echo '<div class="alert error">Error adding teacher: ' . $conn->error . '</div>';
    }
}

// Handle teacher deletion
if (isset($_GET['delete'])) {
    $teacher_id = intval($_GET['delete']);
    $conn->query("DELETE FROM teachers WHERE teacher_id = $teacher_id");
    echo '<div class="alert success">Teacher deleted successfully!</div>';
}
?>

<h2>Teachers Management</h2>

<!-- Add Teacher Form -->
<div class="card">
    <h3><i class="fas fa-user-plus"></i> Add New Teacher</h3>
    <form method="POST">
        <input type="hidden" name="add_teacher" value="1">
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
                <label for="qualification">Qualification:</label>
                <input type="text" id="qualification" name="qualification" required>
            </div>
            <div class="form-group">
                <label for="specialization">Specialization:</label>
                <input type="text" id="specialization" name="specialization" required>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="hire_date">Hire Date:</label>
                <input type="date" id="hire_date" name="hire_date" required>
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
        
        <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Add Teacher</button>
    </form>
</div>

<!-- Teachers List -->
<div class="card">
    <h3><i class="fas fa-chalkboard-teacher"></i> All Teachers</h3>
    
    <!-- Search Form -->
    <div class="search-box">
        <form method="GET" action="">
            <input type="text" name="search" placeholder="Search teachers..." 
                   value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <button type="submit"><i class="fas fa-search"></i></button>
        </form>
    </div>
    
    <?php
    // Build search query
    $search_condition = "";
    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $conn->real_escape_string($_GET['search']);
        $search_condition = "WHERE t.first_name LIKE '%$search%' 
                            OR t.last_name LIKE '%$search%' 
                            OR t.email LIKE '%$search%' 
                            OR d.department_name LIKE '%$search%'";
    }
    
    $teachers = $conn->query("SELECT t.*, d.department_name,
                             (SELECT COUNT(*) FROM courses WHERE teacher_id = t.teacher_id) as course_count
                             FROM teachers t 
                             LEFT JOIN departments d ON t.department_id = d.department_id
                             $search_condition
                             ORDER BY t.last_name, t.first_name");
    
    if ($teachers->num_rows > 0) {
        echo '<table class="data-table">';
        echo '<thead>';
        echo '<tr>';
        echo '<th>ID</th>';
        echo '<th>Name</th>';
        echo '<th>Email</th>';
        echo '<th>Qualification</th>';
        echo '<th>Specialization</th>';
        echo '<th>Department</th>';
        echo '<th>Courses</th>';
        echo '<th>Actions</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        
        while($row = $teachers->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $row['teacher_id'] . '</td>';
            echo '<td>' . $row['first_name'] . ' ' . $row['last_name'] . '</td>';
            echo '<td>' . $row['email'] . '</td>';
            echo '<td>' . $row['qualification'] . '</td>';
            echo '<td>' . $row['specialization'] . '</td>';
            echo '<td>' . ($row['department_name'] ?: 'N/A') . '</td>';
            echo '<td>' . $row['course_count'] . '</td>';
            echo '<td class="actions">';
            echo '<a href="teacher_details.php?id=' . $row['teacher_id'] . '" class="btn-view" title="View Details"><i class="fas fa-eye"></i></a>';
            echo '<a href="edit_teacher.php?id=' . $row['teacher_id'] . '" class="btn-edit" title="Edit"><i class="fas fa-edit"></i></a>';
            echo '<a href="?delete=' . $row['teacher_id'] . '" class="btn-delete" title="Delete" onclick="return confirm(\'Are you sure?\')"><i class="fas fa-trash"></i></a>';
            echo '</td>';
            echo '</tr>';
        }
        
        echo '</tbody>';
        echo '</table>';
    } else {
        echo '<p class="no-data">No teachers found.</p>';
    }
    ?>
</div>

<?php include 'school_footer.php'; ?>