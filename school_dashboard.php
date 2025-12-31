<?php
// school_dashboard.php
session_start();
include 'school_db_connect.php';
include 'school_header.php';

// Get counts for dashboard
$departments_count = $conn->query("SELECT COUNT(*) FROM departments")->fetch_row()[0];
$students_count = $conn->query("SELECT COUNT(*) FROM students")->fetch_row()[0];
$teachers_count = $conn->query("SELECT COUNT(*) FROM teachers")->fetch_row()[0];
$courses_count = $conn->query("SELECT COUNT(*) FROM courses")->fetch_row()[0];
$enrollments_count = $conn->query("SELECT COUNT(*) FROM student_enrollments")->fetch_row()[0];
?>

<h2>Dashboard</h2>

<div class="stats">
    <div class="stat-card">
        <i class="fas fa-building"></i>
        <h3><?php echo $departments_count; ?></h3>
        <p>Departments</p>
    </div>
    <div class="stat-card">
        <i class="fas fa-user-graduate"></i>
        <h3><?php echo $students_count; ?></h3>
        <p>Students</p>
    </div>
    <div class="stat-card">
        <i class="fas fa-chalkboard-teacher"></i>
        <h3><?php echo $teachers_count; ?></h3>
        <p>Teachers</p>
    </div>
    <div class="stat-card">
        <i class="fas fa-book"></i>
        <h3><?php echo $courses_count; ?></h3>
        <p>Courses</p>
    </div>
    <div class="stat-card">
        <i class="fas fa-clipboard-list"></i>
        <h3><?php echo $enrollments_count; ?></h3>
        <p>Enrollments</p>
    </div>
</div>

<div class="dashboard-sections">
    <!-- Recent Students -->
    <div class="section">
        <h3><i class="fas fa-user-plus"></i> Recent Students</h3>
        <?php
        $recent_students = $conn->query("SELECT s.*, d.department_name 
                                        FROM students s 
                                        LEFT JOIN departments d ON s.department_id = d.department_id
                                        ORDER BY s.enrollment_date DESC LIMIT 5");
        
        if ($recent_students->num_rows > 0) {
            echo '<table>';
            echo '<tr><th>Name</th><th>Email</th><th>Department</th><th>Enrollment Date</th></tr>';
            while($row = $recent_students->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . $row['first_name'] . ' ' . $row['last_name'] . '</td>';
                echo '<td>' . $row['email'] . '</td>';
                echo '<td>' . ($row['department_name'] ?: 'N/A') . '</td>';
                echo '<td>' . $row['enrollment_date'] . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo '<p>No students found.</p>';
        }
        ?>
    </div>

    <!-- Upcoming Courses -->
    <div class="section">
        <h3><i class="fas fa-calendar-alt"></i> Current Courses</h3>
        <?php
        $courses = $conn->query("SELECT c.*, d.department_name, t.first_name, t.last_name 
                                FROM courses c
                                LEFT JOIN departments d ON c.department_id = d.department_id
                                LEFT JOIN teachers t ON c.teacher_id = t.teacher_id
                                ORDER BY c.course_name LIMIT 5");
        
        if ($courses->num_rows > 0) {
            echo '<table>';
            echo '<tr><th>Course Code</th><th>Course Name</th><th>Department</th><th>Teacher</th></tr>';
            while($row = $courses->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . $row['course_code'] . '</td>';
                echo '<td>' . $row['course_name'] . '</td>';
                echo '<td>' . ($row['department_name'] ?: 'N/A') . '</td>';
                echo '<td>' . $row['first_name'] . ' ' . $row['last_name'] . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo '<p>No courses found.</p>';
        }
        ?>
    </div>
</div>

<?php include 'school_footer.php'; ?>