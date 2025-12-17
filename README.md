# Student Result Management System



## Department Information
**Department of Computer Science and Engineering**  
**Course Code:** CSE 2291  
**Course Title:** Software Development ii (Database Programming)  
**Project Title:** Student Result Management System (SRMS).  



## Submitted To
**MD: Tahsin Rahman**  
Lecturer  
Department of Computer Science and Engineering  
Northern University Bangladesh  



## Submitted By
- **Maymuna Anjum Shefath** — Id: 41230201245
- **Huraira Jim** — Id: 41230201272  
- **Md. Atikur Rahman** — Id: 41230201274
- **Hasibur Hassan** — Id: 41240102084



## Project Overview
This project is a **Student Result Management System** developed using **PHP & MySQL**.  
It allows management of:
- 
- 
- 
- 
- 



## Features
- **Admin Features**
  - Secure login system
  - Add / update / delete student information
  - Add / update / delete course information
  - Enter and update student marks
  - Generate mark sheets
  - View student results
  - Change password & logout

- **Student Features**
  - Login with Student ID
  - View semester-wise results
  - View and download mark sheet
  - Secure logout

- **System Features**
  - Role-based access (Admin / Student)
  - MySQL database integration
  - GPA & Grade calculation
  - Simple and clean UI



## Technologies Used
- **PHP**
- **MySQL**
- **CSS**
- **HTML**
- **JavaScript**



## Project Structure
```bash
srms/
│
├── database.php
├── index.php           ui page(login+register)
├── login.php           login process(logic)
├── register.php        register process
├── logout.php
│
├── admin/
│   ├── admin_dashboard.php
│   ├── add_student.php
│   ├── add_result.php
│   └── view_students.php
│
├── student/
│   ├── student_dashboard.php
│   └── view_result.php
│
├── css/
│   └── style.css
│
└── database.sql
