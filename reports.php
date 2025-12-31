<?php
// reports.php
session_start();
include 'school_db_connect.php';
include 'school_header.php';
?>

<h2>Reports & Analytics</h2>

<div class="stats">
    <div class="stat-card">
        <i class="fas fa-chart-line"></i>
        <h3>
            <?php 
            $avg_gpa = $conn->query("
                SELECT AVG(CASE 
                    WHEN grade = 'A' THEN 4.0
                    WHEN grade = 'A-' THEN 3.7
                    WHEN grade = 'B+' THEN 3.3
                    WHEN grade = 'B' THEN 3.0
                    WHEN grade = 'B-' THEN 2.7
                    WHEN grade = 'C+' THEN 2.3
                    WHEN grade = 'C' THEN 2.0
                    WHEN grade = 'C-' THEN 1.7
                    WHEN grade = 'D+' THEN 1.3
                    WHEN grade = 'D' THEN 1.0
                    WHEN grade = 'F' THEN 0.0
                    ELSE NULL
                END) as avg_gpa
                FROM student_enrollments 
                WHERE grade IS NOT NULL
            ")->fetch_assoc()['avg_gpa'];
            echo number_format($avg_gpa ?: 0, 2);
            ?>
        </h3>
        <p>Average GPA</p>
    </div>
    
    <div class="stat-card">
        <i class="fas fa-user-check"></i>
        <h3>
            <?php
            $completion_rate = $conn->query("
                SELECT 
                    (SELECT COUNT(*) FROM student_enrollments WHERE status = 'Completed') * 100.0 /
                    (SELECT COUNT(*) FROM student_enrollments WHERE status != 'Dropped') as rate
            ")->fetch_assoc()['rate'];
            echo number_format($completion_rate ?: 0, 1) . '%';
            ?>
        </h3>
        <p>Course Completion Rate</p>
    </div>
    
    <div class="stat-card">
        <i class="fas fa-graduation-cap"></i>
        <h3>
            <?php
            $top_department = $conn->query("
                SELECT d.department_name, COUNT(*) as student_count
                FROM students s
                JOIN departments d ON s.department_id = d.department_id
                GROUP BY d.department_id
                ORDER BY student_count DESC
                LIMIT 1
            ")->fetch_assoc();
            echo $top_department['student_count'] ?? 0;
            ?>
        </h3>
        <p>Most Students in <?php echo $top_department['department_name'] ?? 'N/A'; ?></p>
    </div>
    
    <div class="stat-card">
        <i class="fas fa-star"></i>
        <h3>
            <?php
            $top_teacher = $conn->query("
                SELECT t.first_name, t.last_name, COUNT(*) as course_count
                FROM courses c
                JOIN teachers t ON c.teacher_id = t.teacher_id
                GROUP BY t.teacher_id
                ORDER BY course_count DESC
                LIMIT 1
            ")->fetch_assoc();
            echo $top_teacher['course_count'] ?? 0;
            ?>
        </h3>
        <p>Most Courses by <?php echo $top_teacher['first_name'] . ' ' . $top_teacher['last_name'] ?? 'N/A'; ?></p>
    </div>
</div>

<div class="dashboard-sections">
    <!-- Grade Distribution -->
    <div class="section">
        <h3><i class="fas fa-chart-pie"></i> Grade Distribution</h3>
        <?php
        $grades = $conn->query("
            SELECT grade, COUNT(*) as count
            FROM student_enrollments
            WHERE grade IS NOT NULL
            GROUP BY grade
            ORDER BY FIELD(grade, 'A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'C-', 'D+', 'D', 'F')
        ");
        
        if ($grades->num_rows > 0) {
            echo '<table class="data-table">';
            echo '<tr><th>Grade</th><th>Count</th><th>Percentage</th></tr>';
            
            $total_grades = $conn->query("SELECT COUNT(*) as total FROM student_enrollments WHERE grade IS NOT NULL")->fetch_assoc()['total'];
            
            while($grade = $grades->fetch_assoc()) {
                $percentage = ($grade['count'] / $total_grades) * 100;
                echo '<tr>';
                echo '<td><span class="grade-badge">' . $grade['grade'] . '</span></td>';
                echo '<td>' . $grade['count'] . '</td>';
                echo '<td>';
                echo '<div class="progress-bar">';
                echo '<div class="progress-fill" style="width: ' . $percentage . '%"></div>';
                echo '<span>' . number_format($percentage, 1) . '%</span>';
                echo '</div>';
                echo '</td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo '<p class="no-data">No grade data available.</p>';
        }
        ?>
    </div>

    <!-- Department Statistics -->
    <div class="section">
        <h3><i class="fas fa-university"></i> Department Statistics</h3>
        <?php
        $dept_stats = $conn->query("
            SELECT d.department_name,
                   COUNT(DISTINCT s.student_id) as student_count,
                   COUNT(DISTINCT t.teacher_id) as teacher_count,
                   COUNT(DISTINCT c.course_id) as course_count
            FROM departments d
            LEFT JOIN students s ON d.department_id = s.department_id
            LEFT JOIN teachers t ON d.department_id = t.department_id
            LEFT JOIN courses c ON d.department_id = c.department_id
            GROUP BY d.department_id
            ORDER BY student_count DESC
        ");
        
        if ($dept_stats->num_rows > 0) {
            echo '<table class="data-table">';
            echo '<tr><th>Department</th><th>Students</th><th>Teachers</th><th>Courses</th></tr>';
            
            while($dept = $dept_stats->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . $dept['department_name'] . '</td>';
                echo '<td>' . $dept['student_count'] . '</td>';
                echo '<td>' . $dept['teacher_count'] . '</td>';
                echo '<td>' . $dept['course_count'] . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo '<p class="no-data">No department data available.</p>';
        }
        ?>
    </div>
</div>

<!-- Enrollment Trends -->
<div class="card">
    <h3><i class="fas fa-chart-bar"></i> Monthly Enrollment Trends</h3>
    <?php
    $monthly_enrollments = $conn->query("
        SELECT DATE_FORMAT(enrollment_date, '%Y-%m') as month,
               COUNT(*) as enrollment_count
        FROM student_enrollments
        GROUP BY DATE_FORMAT(enrollment_date, '%Y-%m')
        ORDER BY month DESC
        LIMIT 12
    ");
    
    if ($monthly_enrollments->num_rows > 0) {
        echo '<table class="data-table">';
        echo '<tr><th>Month</th><th>Enrollments</th><th>Chart</th></tr>';
        
        $max_enrollments = 0;
        $data = [];
        while($row = $monthly_enrollments->fetch_assoc()) {
            $data[] = $row;
            if ($row['enrollment_count'] > $max_enrollments) {
                $max_enrollments = $row['enrollment_count'];
            }
        }
        
        foreach(array_reverse($data) as $row) {
            $percentage = ($row['enrollment_count'] / max($max_enrollments, 1)) * 100;
            echo '<tr>';
            echo '<td>' . $row['month'] . '</td>';
            echo '<td>' . $row['enrollment_count'] . '</td>';
            echo '<td>';
            echo '<div class="progress-bar">';
            echo '<div class="progress-fill" style="width: ' . $percentage . '%"></div>';
            echo '<span>' . $row['enrollment_count'] . '</span>';
            echo '</div>';
            echo '</td>';
            echo '</tr>';
        }
        echo '</table>';
    } else {
        echo '<p class="no-data">No enrollment trend data available.</p>';
    }
    ?>
</div>

<style>
.progress-bar {
    width: 100%;
    height: 20px;
    background-color: #ecf0f1;
    border-radius: 4px;
    position: relative;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background-color: #3498db;
    border-radius: 4px;
    transition: width 0.3s ease;
}

.progress-bar span {
    position: absolute;
    width: 100%;
    text-align: center;
    line-height: 20px;
    color: #2c3e50;
    font-size: 0.85rem;
    font-weight: 600;
}
</style>

<?php include 'school_footer.php'; ?>