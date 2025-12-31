<?php
// departments.php
session_start();
include 'school_db_connect.php';
include 'school_header.php';

// Handle form submission for adding new department
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_department'])) {
    $department_name = $conn->real_escape_string($_POST['department_name']);
    $department_code = $conn->real_escape_string($_POST['department_code']);
    $head_of_department = $conn->real_escape_string($_POST['head_of_department']);
    
    $sql = "INSERT INTO departments (department_name, department_code, head_of_department)
            VALUES ('$department_name', '$department_code', '$head_of_department')";
    
    if ($conn->query($sql)) {
        echo '<div class="alert success">Department added successfully!</div>';
    } else {
        echo '<div class="alert error">Error adding department: ' . $conn->error . '</div>';
    }
}

// Handle department deletion
if (isset($_GET['delete'])) {
    $department_id = intval($_GET['delete']);
    $conn->query("DELETE FROM departments WHERE department_id = $department_id");
    echo '<div class="alert success">Department deleted successfully!</div>';
}
?>

<h2>Departments Management</h2>

<!-- Add Department Form -->
<div class="card">
    <h3><i class="fas fa-plus-circle"></i> Add New Department</h3>
    <form method="POST">
        <input type="hidden" name="add_department" value="1">
        <div class="form-row">
            <div class="form-group">
                <label for="department_name">Department Name:</label>
                <input type="text" id="department_name" name="department_name" required>
            </div>
            <div class="form-group">
                <label for="department_code">Department Code:</label>
                <input type="text" id="department_code" name="department_code" required>
            </div>
        </div>
        
        <div class="form-group">
            <label for="head_of_department">Head of Department:</label>
            <input type="text" id="head_of_department" name="head_of_department">
        </div>
        
        <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Add Department</button>
    </form>
</div>

<!-- Departments List -->
<div class="card">
    <h3><i class="fas fa-building"></i> All Departments</h3>
    
    <?php
    $departments = $conn->query("SELECT d.*,
                                (SELECT COUNT(*) FROM students WHERE department_id = d.department_id) as student_count,
                                (SELECT COUNT(*) FROM teachers WHERE department_id = d.department_id) as teacher_count,
                                (SELECT COUNT(*) FROM courses WHERE department_id = d.department_id) as course_count
                                FROM departments d
                                ORDER BY d.department_name");
    
    if ($departments->num_rows > 0) {
        echo '<table class="data-table">';
        echo '<thead>';
        echo '<tr>';
        echo '<th>Department Code</th>';
        echo '<th>Department Name</th>';
        echo '<th>Head of Department</th>';
        echo '<th>Students</th>';
        echo '<th>Teachers</th>';
        echo '<th>Courses</th>';
        echo '<th>Actions</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        
        while($row = $departments->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $row['department_code'] . '</td>';
            echo '<td>' . $row['department_name'] . '</td>';
            echo '<td>' . ($row['head_of_department'] ?: 'N/A') . '</td>';
            echo '<td>' . $row['student_count'] . '</td>';
            echo '<td>' . $row['teacher_count'] . '</td>';
            echo '<td>' . $row['course_count'] . '</td>';
            echo '<td class="actions">';
            echo '<a href="department_details.php?id=' . $row['department_id'] . '" class="btn-view" title="View Details"><i class="fas fa-eye"></i></a>';
            echo '<a href="edit_department.php?id=' . $row['department_id'] . '" class="btn-edit" title="Edit"><i class="fas fa-edit"></i></a>';
            echo '<a href="?delete=' . $row['department_id'] . '" class="btn-delete" title="Delete" onclick="return confirm(\'Are you sure?\')"><i class="fas fa-trash"></i></a>';
            echo '</td>';
            echo '</tr>';
        }
        
        echo '</tbody>';
        echo '</table>';
    } else {
        echo '<p class="no-data">No departments found.</p>';
    }
    ?>
</div>

<?php include 'school_footer.php'; ?>