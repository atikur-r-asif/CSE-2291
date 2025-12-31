<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Result Management System</title>
    <link rel="stylesheet" href="./css_school_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="header-content">
            <h1><i class="fas fa-school"></i>Student Result Management System</h1>
            <nav class="user-menu">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <span>Welcome, <?php echo $_SESSION['user_name']; ?></span>
                    <a href="logout.php" class="logout-btn">Logout</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    
    <div class="nav">
        <a href="school_dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
        <a href="departments.php"><i class="fas fa-building"></i> Departments</a>
        <a href="students.php"><i class="fas fa-user-graduate"></i> Students</a>
        <a href="teachers.php"><i class="fas fa-chalkboard-teacher"></i> Teachers</a>
        <a href="courses.php"><i class="fas fa-book"></i> Courses</a>
        <a href="enrollments.php"><i class="fas fa-clipboard-list"></i> Enrollments</a>
        <a href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a>
    </div>
    
    <div class="container">