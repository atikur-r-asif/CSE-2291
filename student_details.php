<?php
// student_details.php
session_start();
include 'school_db_connect.php';
include 'school_header.php';

$student_id = intval($_GET['id']);

// Get student details
$student = $conn->query("SELECT s.*, d.department_name 
                        FROM students s
                        LEFT JOIN departments d ON s.department_id = d.department_id
                        WHERE s.student_id = $student_id")->fetch_assoc();

if (!$student) {
    echo '<div class="container"><p>Student not found.</p></div>';
    include 'school_footer.php';
    exit();
}

// Get enrolled courses for this student
$enrollments = $conn->query("SELECT se.*, c.course_code, c.course_name, c.credits
                            FROM student_enrollments se
                            JOIN courses c ON se.course_id = c.course_id
                            WHERE se.student_id = $student_id
                            ORDER BY se.enrollment_date DESC");
?>

<div class="card student-profile">
    <div class="profile-header">
        <div class="avatar">
            <i class="fas fa-user-graduate"></i>
        </div>
        <div class="profile-info">
            <h2><?php echo $student['first_name'] . ' ' . $student['last_name']; ?></h2>
            <p class="email"><i class="fas fa-envelope"></i> <?php echo $student['email']; ?></p>
            <p class="phone"><i class="fas fa-phone"></i> <?php echo $student['phone']; ?></p>
        </div>
    </div>
    
    <div class="profile-details">
        <div class="detail-section">
            <h3><i class="fas fa-info-circle"></i> Personal Information</h3>
            <table class="info-table">
                <tr>
                    <td><strong>Date of Birth:</strong></td>
                    <td><?php echo $student['date_of_birth']; ?></td>
                </tr>
                <tr>
                    <td><strong>Gender:</strong></td>
                    <td><?php echo $student['gender']; ?></td>
                </tr>
                <tr>
                    <td><strong>Age:</strong></td>
                    <td><?php echo date_diff(date_create($student['date_of_birth']), date_create('today'))->y; ?> years</td>
                </tr>
            </table>
        </div>
        
        <div class="detail-section">
            <h3><i class="fas fa-school"></i> Academic Information</h3>
            <table class="info-table">
                <tr>
                    <td><strong>Department:</strong></td>
                    <td><?php echo $student['department_name']; ?></td>
                </tr>
                <tr>
                    <td><strong>Enrollment Date:</strong></td>
                    <td><?php echo $student['enrollment_date']; ?></td>
                </tr>
                <tr>
                    <td><strong>Address:</strong></td>
                    <td><?php echo nl2br($student['address']); ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>

<div class="card">
    <h3><i class="fas fa-book"></i> Enrolled Courses</h3>
    
    <?php if ($enrollments->num_rows > 0): ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Course Code</th>
                    <th>Course Name</th>
                    <th>Credits</th>
                    <th>Enrollment Date</th>
                    <th>Grade</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $total_credits = 0;
                $completed_credits = 0;
                $gpa_sum = 0;
                $graded_courses = 0;
                
                while($enrollment = $enrollments->fetch_assoc()): 
                    $total_credits += $enrollment['credits'];
                    
                    if ($enrollment['status'] == 'Completed' && $enrollment['grade']) {
                        $completed_credits += $enrollment['credits'];
                        $graded_courses++;
                        
                        // Convert grade to GPA points
                        $grade_points = [
                            'A' => 4.0, 'A-' => 3.7, 'B+' => 3.3, 'B' => 3.0, 'B-' => 2.7,
                            'C+' => 2.3, 'C' => 2.0, 'C-' => 1.7, 'D+' => 1.3, 'D' => 1.0, 'F' => 0.0
                        ];
                        
                        if (isset($grade_points[$enrollment['grade']])) {
                            $gpa_sum += $grade_points[$enrollment['grade']] * $enrollment['credits'];
                        }
                    }
                ?>
                    <tr>
                        <td><?php echo $enrollment['course_code']; ?></td>
                        <td><?php echo $enrollment['course_name']; ?></td>
                        <td><?php echo $enrollment['credits']; ?></td>
                        <td><?php echo $enrollment['enrollment_date']; ?></td>
                        <td>
                            <?php if ($enrollment['grade']): ?>
                                <span class="grade-badge"><?php echo $enrollment['grade']; ?></span>
                            <?php else: ?>
                                <span class="no-grade">Not Graded</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="status-badge status-<?php echo strtolower($enrollment['status']); ?>">
                                <?php echo $enrollment['status']; ?>
                            </span>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        
        <div class="academic-summary">
            <h4>Academic Summary</h4>
            <div class="summary-stats">
                <div class="summary-item">
                    <span class="label">Total Credits:</span>
                    <span class="value"><?php echo $total_credits; ?></span>
                </div>
                <div class="summary-item">
                    <span class="label">Completed Credits:</span>
                    <span class="value"><?php echo $completed_credits; ?></span>
                </div>
                <div class="summary-item">
                    <span class="label">Current GPA:</span>
                    <span class="value">
                        <?php 
                        if ($graded_courses > 0) {
                            echo number_format($gpa_sum / $completed_credits, 2);
                        } else {
                            echo 'N/A';
                        }
                        ?>
                    </span>
                </div>
            </div>
        </div>
    <?php else: ?>
        <p class="no-data">This student is not enrolled in any courses yet.</p>
    <?php endif; ?>
</div>

<div class="action-buttons">
    <a href="students.php" class="btn-secondary"><i class="fas fa-arrow-left"></i> Back to Students</a>
    <a href="edit_student.php?id=<?php echo $student_id; ?>" class="btn-primary"><i class="fas fa-edit"></i> Edit Student</a>
    <a href="enroll_student.php?student_id=<?php echo $student_id; ?>" class="btn-success"><i class="fas fa-plus"></i> Enroll in Course</a>
</div>

<?php include 'school_footer.php'; ?>