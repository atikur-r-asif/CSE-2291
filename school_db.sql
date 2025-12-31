DROP DATABASE IF EXISTS school_db;

CREATE DATABASE school_db;

USE school_db;

CREATE TABLE departments (
    department_id INT PRIMARY KEY AUTO_INCREMENT,
    department_name VARCHAR(100) NOT NULL,
    department_code VARCHAR(10) UNIQUE NOT NULL,
    head_of_department VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE students (
    student_id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    date_of_birth DATE NOT NULL,
    gender ENUM('Male', 'Female', 'Other'),
    address TEXT,
    enrollment_date DATE NOT NULL,
    department_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(department_id) ON DELETE SET NULL
);

CREATE TABLE teachers (
    teacher_id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    date_of_birth DATE NOT NULL,
    gender ENUM('Male', 'Female', 'Other'),
    qualification VARCHAR(100),
    specialization VARCHAR(100),
    hire_date DATE NOT NULL,
    department_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(department_id) ON DELETE SET NULL
);

CREATE TABLE courses (
    course_id INT PRIMARY KEY AUTO_INCREMENT,
    course_code VARCHAR(20) UNIQUE NOT NULL,
    course_name VARCHAR(100) NOT NULL,
    credits INT NOT NULL,
    description TEXT,
    department_id INT,
    teacher_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(department_id) ON DELETE SET NULL,
    FOREIGN KEY (teacher_id) REFERENCES teachers(teacher_id) ON DELETE SET NULL
);

-- Table for Student Enrollment (Many-to-Many relationship)
CREATE TABLE student_enrollments (
    enrollment_id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT,
    course_id INT,
    enrollment_date DATE NOT NULL,
    grade CHAR(2),
    status ENUM('Enrolled', 'Completed', 'Dropped') DEFAULT 'Enrolled',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(course_id) ON DELETE CASCADE,
    UNIQUE KEY unique_enrollment (student_id, course_id)
);

-- Insert sample data for departments
INSERT INTO departments (department_name, department_code, head_of_department) VALUES
('Computer Science', 'CSE', 'Dr. .....'),
('Mathematics', 'EEE', 'Dr. .....'),
('Physics', 'TEX', 'Dr. ....'),
('English Literature', 'ELL', 'Dr. ....'),
('Business Administration', 'BBA', 'Dr. ....');

-- Insert sample data for students
INSERT INTO students (first_name, last_name, email, phone, date_of_birth, gender, address, enrollment_date, department_id) VALUES
('Atikur', 'Rahman', 'asif.s.eyes@gmail.com', '123-456-7890', '2002-05-15', 'Male', '123 Main St, City', '2021-09-01', 1),
('Asif', 'Shahriar', 'asif.s.eyes@gmail.com', '123-456-7891', '2003-02-28', 'Female', '456 Oak Ave, Town', '2022-09-01', 2),
('Tonmoy', 'Haque', 'asif.s.eyes@gmail.com', '123-456-7892', '2001-11-10', 'Male', '789 Pine Rd, Village', '2020-09-01', 1),
('Sarah', 'Zaman', 'asif.s.eyes@gmail.com', '123-456-7893', '2002-08-22', 'Female', '321 Elm St, City', '2021-09-01', 3),
('Anisj', 'Chaki', 'asif.s.eyes@gmail.com', '123-456-7894', '2003-03-17', 'Male', '654 Maple Dr, Town', '2022-09-01', 4),
('Joy', 'Mondol', 'asif.s.eyes@gmail.com', '123-456-7895', '2002-07-30', 'Female', '987 Cedar Ln, Village', '2021-09-01', 5),
('Sanzid', 'Zaman', 'dasif.s.eyes@gmail.com', '123-456-7896', '2001-12-05', 'Male', '147 Birch St, City', '2020-09-01', 2),
('Ahmad', 'Istiak', 'asif.s.eyes@gmail.com', '123-456-7897', '2003-01-20', 'Female', '258 Spruce Ave, Town', '2022-09-01', 1),
('Maymuna', 'Anjum', 'asif.s.eyes@gmail.com', '123-456-7898', '2002-04-12', 'Male', '369 Willow Rd, Village', '2021-09-01', 3),
('Huraira', 'Jim', 'asif.s.eyes@gmail.com', '123-456-7899', '2003-06-25', 'Female', '741 Oak St, City', '2022-09-01', 4);

-- Insert sample data for teachers
INSERT INTO teachers (first_name, last_name, email, phone, date_of_birth, gender, qualification, specialization, hire_date, department_id) VALUES
('Rayhan', 'Ul', 'atikurrahman.cse.nub@gmail.com', '987-654-3210', '1975-03-15', 'Male', 'PhD in Computer Science', 'Artificial Intelligence', '2010-08-01', 1),
('Doha', 'Islam', 'atikurrahman.cse.nub@gmail.com', '987-654-3211', '1980-07-22', 'Female', 'PhD in Mathematics', 'Calculus', '2012-08-01', 2),
('Faija', 'Tabassum', 'atikurrahman.cse.nub@gmail.com', '987-654-3212', '1978-11-30', 'Male', 'PhD in Physics', 'Quantum Mechanics', '2011-08-01', 3),
('Tonmoy', 'Hoque', 'atikurrahman.cse.nub@gmail.com', '987-654-3213', '1982-04-18', 'Female', 'PhD in English', 'Shakespeare Studies', '2013-08-01', 4),
('Rimon', 'Islam', 'atikurrahman.cse.nub@gmail.com', '987-654-3214', '1976-09-05', 'Male', 'PhD in Business', 'Marketing', '2009-08-01', 5),
('Sanjida', 'Zaman', 'atikurrahman.cse.nub@gmail.com', '987-654-3215', '1985-01-25', 'Female', 'MSc in Computer Science', 'Database Systems', '2015-08-01', 1),
('Anika', 'Ferdous', 'atikurrahman.cse.nub@gmail.com', '987-654-3216', '1979-12-10', 'Male', 'PhD in Mathematics', 'Statistics', '2014-08-01', 2),
('Anindo', 'Mondol', 'atikurrahman.cse.nub@gmail.com', '987-654-3217', '1983-06-15', 'Female', 'MSc in Physics', 'Astrophysics', '2016-08-01', 3),
('Tahsin', 'Hoque', 'atikurrahman.cse.nub@gmail.com', '987-654-3218', '1981-08-20', 'Male', 'MA in English', 'Creative Writing', '2017-08-01', 4),
('Aysha', 'Tasnim', 'atikurrahman.cse.nub@gmail.com', '987-654-3219', '1984-02-28', 'Female', 'MBA', 'Finance', '2018-08-01', 5);

-- Insert sample data for courses
INSERT INTO courses (course_code, course_name, credits, description, department_id, teacher_id) VALUES
('CS101', 'Introduction to Programming', 3, 'Basic programming concepts using Python', 1, 1),
('CS201', 'Data Structures', 4, 'Fundamental data structures and algorithms', 1, 6),
('MATH101', 'Calculus I', 4, 'Introduction to differential and integral calculus', 2, 2),
('MATH201', 'Linear Algebra', 3, 'Vectors, matrices, and linear transformations', 2, 7),
('PHY101', 'General Physics', 4, 'Mechanics, heat, and waves', 3, 3),
('PHY201', 'Modern Physics', 3, 'Introduction to relativity and quantum mechanics', 3, 8),
('ENG101', 'English Composition', 3, 'Writing skills and critical thinking', 4, 4),
('ENG201', 'British Literature', 3, 'Survey of British literature from medieval to modern', 4, 9),
('BA101', 'Principles of Management', 3, 'Introduction to management theories and practices', 5, 5),
('BA201', 'Marketing Fundamentals', 3, 'Basic marketing concepts and strategies', 5, 10);

-- Insert sample data for student enrollments
INSERT INTO student_enrollments (student_id, course_id, enrollment_date, grade, status) VALUES
(1, 1, '2023-09-01', 'A', 'Completed'),
(1, 2, '2024-01-15', 'B+', 'Enrolled'),
(2, 3, '2023-09-01', 'A-', 'Completed'),
(2, 4, '2024-01-15', NULL, 'Enrolled'),
(3, 1, '2023-09-01', 'B', 'Completed'),
(3, 5, '2024-01-15', NULL, 'Enrolled'),
(4, 5, '2023-09-01', 'A', 'Completed'),
(4, 6, '2024-01-15', 'B+', 'Enrolled'),
(5, 7, '2023-09-01', 'B-', 'Completed'),
(5, 8, '2024-01-15', NULL, 'Enrolled'),
(6, 9, '2023-09-01', 'A', 'Completed'),
(6, 10, '2024-01-15', 'A-', 'Enrolled'),
(7, 3, '2023-09-01', 'C+', 'Completed'),
(7, 4, '2024-01-15', NULL, 'Enrolled'),
(8, 1, '2023-09-01', 'A-', 'Completed'),
(8, 2, '2024-01-15', 'B', 'Enrolled'),
(9, 5, '2023-09-01', 'B+', 'Completed'),
(9, 6, '2024-01-15', NULL, 'Enrolled'),
(10, 7, '2023-09-01', 'A', 'Completed'),
(10, 8, '2024-01-15', 'A-', 'Enrolled');