<?php
// enrollments.php
session_start();
include 'school_db_connect.php';
include 'school_header.php';

// Handle form submission for adding new enrollment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_enrollment'])) {
    $student_id = intval($_POST['student_id']);
    $course_id = intval($_POST['course_id']);
    $enrollment_date = $conn->real_escape_string($_POST['enrollment_date']);
    
    // Check if already enrolled
    $check = $conn->query("SELECT * FROM student_enrollments 
                          WHERE student_id = $student_id AND course_id = $course_id");
    
    if ($check->num_rows > 0) {
        echo '<div class="alert error">Student is already enrolled in this course!</div>';
    } else {
        $sql = "INSERT INTO student_enrollments (student_id, course_id, enrollment_date)
                VALUES ($student_id, $course_id, '$enrollment_date')";
        
        if ($conn->query($sql)) {
            echo '<div class="alert success">Student enrolled successfully!</div>';
        } else {
            echo '<div class="alert error">Error enrolling student: ' . $conn->error . '</div>';
        }
    }
}

// Handle enrollment deletion
if (isset($_GET['delete'])) {
    $enrollment_id = intval($_GET['delete']);
    $conn->query("DELETE FROM student_enrollments WHERE enrollment_id = $enrollment_id");
    echo '<div class="alert success">Enrollment deleted successfully!</div>';
}

// Handle grade update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_grade'])) {
    $enrollment_id = intval($_POST['enrollment_id']);
    $grade = $conn->real_escape_string($_POST['grade']);
    $status = $conn->real_escape_string($_POST['status']);
    
    $sql = "UPDATE student_enrollments 
            SET grade = '$grade', status = '$status'
            WHERE enrollment_id = $enrollment_id";
    
    if ($conn->query($sql)) {
        echo '<div class="alert success">Grade updated successfully!</div>';
    } else {
        echo '<div class="alert error">Error updating grade: ' . $conn->error . '</div>';
    }
}
?>

<h2>Enrollments Management</h2>

<!-- Add Enrollment Form -->
<div class="card">
    <h3><i class="fas fa-user-plus"></i> Enroll Student in Course</h3>
    <form method="POST">
        <input type="hidden" name="add_enrollment" value="1">
        <div class="form-row">
            <div class="form-group">
                <label for="student_id">Student:</label>
                <select id="student_id" name="student_id" required>
                    <option value="">Select Student</option>
                    <?php
                    $students = $conn->query("SELECT * FROM students ORDER BY last_name, first_name");
                    while($student = $students->fetch_assoc()) {
                        echo '<option value="' . $student['student_id'] . '">' . 
                             $student['first_name'] . ' ' . $student['last_name'] . ' (' . $student['email'] . ')' . 
                             '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="course_id">Course:</label>
                <select id="course_id" name="course_id" required>
                    <option value="">Select Course</option>
                    <?php
                    $courses = $conn->query("SELECT * FROM courses ORDER BY course_code");
                    while($course = $courses->fetch_assoc()) {
                        echo '<option value="' . $course['course_id'] . '">' . 
                             $course['course_code'] . ' - ' . $course['course_name'] . 
                             '</option>';
                    }
                    ?>
                </select>
            </div>
        </div>
        
        <div class="form-group">
            <label for="enrollment_date">Enrollment Date:</label>
            <input type="date" id="enrollment_date" name="enrollment_date" required value="<?php echo date('Y-m-d'); ?>">
        </div>
        
        <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Enroll Student</button>
    </form>
</div>

<!-- Enrollments List -->
<div class="card">
    <h3><i class="fas fa-clipboard-list"></i> All Enrollments</h3>
    
    <!-- Filter Form -->
    <div class="search-box">
        <form method="GET" action="">
            <div class="form-row">
                <div class="form-group">
                    <select name="student_id">
                        <option value="">All Students</option>
                        <?php
                        $students = $conn->query("SELECT * FROM students ORDER BY last_name, first_name");
                        while($student = $students->fetch_assoc()) {
                            $selected = (isset($_GET['student_id']) && $_GET['student_id'] == $student['student_id']) ? 'selected' : '';
                            echo '<option value="' . $student['student_id'] . '" ' . $selected . '>' . 
                                 $student['first_name'] . ' ' . $student['last_name'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <select name="course_id">
                        <option value="">All Courses</option>
                        <?php
                        $courses = $conn->query("SELECT * FROM courses ORDER BY course_code");
                        while($course = $courses->fetch_assoc()) {
                            $selected = (isset($_GET['course_id']) && $_GET['course_id'] == $course['course_id']) ? 'selected' : '';
                            echo '<option value="' . $course['course_id'] . '" ' . $selected . '>' . 
                                 $course['course_code'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <select name="status">
                        <option value="">All Status</option>
                        <option value="Enrolled" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Enrolled') ? 'selected' : ''; ?>>Enrolled</option>
                        <option value="Completed" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                        <option value="Dropped" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Dropped') ? 'selected' : ''; ?>>Dropped</option>
                    </select>
                </div>
                <div class="form-group">
                    <button type="submit"><i class="fas fa-filter"></i> Filter</button>
                    <a href="enrollments.php" class="btn-secondary">Clear</a>
                </div>
            </div>
        </form>
    </div>
    
    <?php
    // Build filter query
    $where_conditions = [];
    if (isset($_GET['student_id']) && !empty($_GET['student_id'])) {
        $where_conditions[] = "se.student_id = " . intval($_GET['student_id']);
    }
    if (isset($_GET['course_id']) && !empty($_GET['course_id'])) {
        $where_conditions[] = "se.course_id = " . intval($_GET['course_id']);
    }
    if (isset($_GET['status']) && !empty($_GET['status'])) {
        $where_conditions[] = "se.status = '" . $conn->real_escape_string($_GET['status']) . "'";
    }
    
    $where_clause = "";
    if (!empty($where_conditions)) {
        $where_clause = "WHERE " . implode(" AND ", $where_conditions);
    }
    
    $enrollments = $conn->query("SELECT se.*, 
                                s.first_name as student_first, s.last_name as student_last, s.email as student_email,
                                c.course_code, c.course_name, c.credits
                                FROM student_enrollments se
                                JOIN students s ON se.student_id = s.student_id
                                JOIN courses c ON se.course_id = c.course_id
                                $where_clause
                                ORDER BY se.enrollment_date DESC");
    
    if ($enrollments->num_rows > 0) {
        echo '<table class="data-table">';
        echo '<thead>';
        echo '<tr>';
        echo '<th>Student</th>';
        echo '<th>Course</th>';
        echo '<th>Enrollment Date</th>';
        echo '<th>Grade</th>';
        echo '<th>Status</th>';
        echo '<th>Actions</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        
        while($row = $enrollments->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $row['student_first'] . ' ' . $row['student_last'] . '<br><small>' . $row['student_email'] . '</small></td>';
            echo '<td>' . $row['course_code'] . '<br><small>' . $row['course_name'] . ' (' . $row['credits'] . ' credits)</small></td>';
            echo '<td>' . $row['enrollment_date'] . '</td>';
            echo '<td>';
            if ($row['grade']) {
                echo '<span class="grade-badge">' . $row['grade'] . '</span>';
            } else {
                echo '<span class="no-grade">Not Graded</span>';
            }
            echo '</td>';
            echo '<td>';
            echo '<span class="status-badge status-' . strtolower($row['status']) . '">' . $row['status'] . '</span>';
            echo '</td>';
            echo '<td class="actions">';
            echo '<button type="button" class="btn-edit" onclick="openGradeModal(' . $row['enrollment_id'] . ', \'' . $row['grade'] . '\', \'' . $row['status'] . '\')" title="Update Grade">
                    <i class="fas fa-edit"></i>
                  </button>';
            echo '<a href="?delete=' . $row['enrollment_id'] . '" class="btn-delete" title="Delete" onclick="return confirm(\'Are you sure?\')">
                    <i class="fas fa-trash"></i>
                  </a>';
            echo '</td>';
            echo '</tr>';
        }
        
        echo '</tbody>';
        echo '</table>';
    } else {
        echo '<p class="no-data">No enrollments found.</p>';
    }
    ?>
</div>

<!-- Grade Update Modal -->
<div id="gradeModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeGradeModal()">&times;</span>
        <h3>Update Grade and Status</h3>
        <form method="POST" id="gradeForm">
            <input type="hidden" name="update_grade" value="1">
            <input type="hidden" name="enrollment_id" id="modal_enrollment_id">
            
            <div class="form-group">
                <label for="modal_grade">Grade:</label>
                <select id="modal_grade" name="grade">
                    <option value="">Select Grade</option>
                    <option value="A">A</option>
                    <option value="A-">A-</option>
                    <option value="B+">B+</option>
                    <option value="B">B</option>
                    <option value="B-">B-</option>
                    <option value="C+">C+</option>
                    <option value="C">C</option>
                    <option value="C-">C-</option>
                    <option value="D+">D+</option>
                    <option value="D">D</option>
                    <option value="F">F</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="modal_status">Status:</label>
                <select id="modal_status" name="status" required>
                    <option value="Enrolled">Enrolled</option>
                    <option value="Completed">Completed</option>
                    <option value="Dropped">Dropped</option>
                </select>
            </div>
            
            <button type="submit" class="btn-primary">Update</button>
        </form>
    </div>
</div>

<script>
function openGradeModal(enrollmentId, currentGrade, currentStatus) {
    document.getElementById('modal_enrollment_id').value = enrollmentId;
    document.getElementById('modal_grade').value = currentGrade;
    document.getElementById('modal_status').value = currentStatus;
    document.getElementById('gradeModal').style.display = 'block';
}

function closeGradeModal() {
    document.getElementById('gradeModal').style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    var modal = document.getElementById('gradeModal');
    if (event.target == modal) {
        closeGradeModal();
    }
}
</script>

<style>
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
}

.modal-content {
    background-color: white;
    margin: 10% auto;
    padding: 30px;
    border-radius: 8px;
    width: 400px;
    max-width: 90%;
}

.close {
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    color: #7f8c8d;
}

.close:hover {
    color: #000;
}
</style>

<?php include 'school_footer.php'; ?>