<?php
// courses.php
session_start();
include 'school_db_connect.php';
include 'school_header.php';

// Handle form submission for adding new course
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_course'])) {
    $course_code = $conn->real_escape_string($_POST['course_code']);
    $course_name = $conn->real_escape_string($_POST['course_name']);
    $credits = intval($_POST['credits']);
    $description = $conn->real_escape_string($_POST['description']);
    $department_id = intval($_POST['department_id']);
    $teacher_id = intval($_POST['teacher_id']);
    
    $sql = "INSERT INTO courses (course_code, course_name, credits, description, department_id, teacher_id)
            VALUES ('$course_code', '$course_name', $credits, '$description', $department_id, $teacher_id)";
    
    if ($conn->query($sql)) {
        echo '<div class="alert success">Course added successfully!</div>';
    } else {
        echo '<div class="alert error">Error adding course: ' . $conn->error . '</div>';
    }
}

// Handle course deletion
if (isset($_GET['delete'])) {
    $course_id = intval($_GET['delete']);
    $conn->query("DELETE FROM courses WHERE course_id = $course_id");
    echo '<div class="alert success">Course deleted successfully!</div>';
}
?>

<h2>Courses Management</h2>

<!-- Add Course Form -->
<div class="card">
    <h3><i class="fas fa-plus-circle"></i> Add New Course</h3>
    <form method="POST">
        <input type="hidden" name="add_course" value="1">
        <div class="form-row">
            <div class="form-group">
                <label for="course_code">Course Code:</label>
                <input type="text" id="course_code" name="course_code" required>
            </div>
            <div class="form-group">
                <label for="course_name">Course Name:</label>
                <input type="text" id="course_name" name="course_name" required>
            </div>
            <div class="form-group">
                <label for="credits">Credits:</label>
                <input type="number" id="credits" name="credits" min="1" max="5" required>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="department_id">Department:</label>
                <select id="department_id" name="department_id" required>
                    <option value="">Select Department</option>
                    <?php
                    $departments = $conn->query("SELECT * FROM departments ORDER BY department_name");
                    while($dept = $departments->fetch_assoc()) {
                        echo '<option value="' . $dept['department_id'] . '">' . $dept['department_name'] . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="teacher_id">Teacher:</label>
                <select id="teacher_id" name="teacher_id">
                    <option value="">Select Teacher</option>
                    <?php
                    $teachers = $conn->query("SELECT * FROM teachers ORDER BY last_name, first_name");
                    while($teacher = $teachers->fetch_assoc()) {
                        echo '<option value="' . $teacher['teacher_id'] . '">' . $teacher['first_name'] . ' ' . $teacher['last_name'] . '</option>';
                    }
                    ?>
                </select>
            </div>
        </div>
        
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="4"></textarea>
        </div>
        
        <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Add Course</button>
    </form>
</div>

<!-- Courses List -->
<div class="card">
    <h3><i class="fas fa-book"></i> All Courses</h3>
    
    <!-- Search Form -->
    <div class="search-box">
        <form method="GET" action="">
            <input type="text" name="search" placeholder="Search courses..." 
                   value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <button type="submit"><i class="fas fa-search"></i></button>
        </form>
    </div>
    
    <?php
    // Build search query
    $search_condition = "";
    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $conn->real_escape_string($_GET['search']);
        $search_condition = "WHERE c.course_code LIKE '%$search%' 
                            OR c.course_name LIKE '%$search%' 
                            OR c.description LIKE '%$search%'
                            OR d.department_name LIKE '%$search%'";
    }
    
    $courses = $conn->query("SELECT c.*, d.department_name, t.first_name, t.last_name,
                             (SELECT COUNT(*) FROM student_enrollments WHERE course_id = c.course_id) as enrollment_count
                             FROM courses c
                             LEFT JOIN departments d ON c.department_id = d.department_id
                             LEFT JOIN teachers t ON c.teacher_id = t.teacher_id
                             $search_condition
                             ORDER BY c.course_code");
    
    if ($courses->num_rows > 0) {
        echo '<table class="data-table">';
        echo '<thead>';
        echo '<tr>';
        echo '<th>Course Code</th>';
        echo '<th>Course Name</th>';
        echo '<th>Credits</th>';
        echo '<th>Department</th>';
        echo '<th>Teacher</th>';
        echo '<th>Enrollments</th>';
        echo '<th>Actions</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        
        while($row = $courses->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $row['course_code'] . '</td>';
            echo '<td>' . $row['course_name'] . '</td>';
            echo '<td>' . $row['credits'] . '</td>';
            echo '<td>' . ($row['department_name'] ?: 'N/A') . '</td>';
            echo '<td>' . ($row['first_name'] ? $row['first_name'] . ' ' . $row['last_name'] : 'N/A') . '</td>';
            echo '<td>' . $row['enrollment_count'] . '</td>';
            echo '<td class="actions">';
            echo '<a href="course_details.php?id=' . $row['course_id'] . '" class="btn-view" title="View Details"><i class="fas fa-eye"></i></a>';
            echo '<a href="edit_course.php?id=' . $row['course_id'] . '" class="btn-edit" title="Edit"><i class="fas fa-edit"></i></a>';
            echo '<a href="?delete=' . $row['course_id'] . '" class="btn-delete" title="Delete" onclick="return confirm(\'Are you sure?\')"><i class="fas fa-trash"></i></a>';
            echo '</td>';
            echo '</tr>';
        }
        
        echo '</tbody>';
        echo '</table>';
    } else {
        echo '<p class="no-data">No courses found.</p>';
    }
    ?>
</div>

<?php include 'school_footer.php'; ?>