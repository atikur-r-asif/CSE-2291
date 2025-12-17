<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="./font-awesome-4.7.0/css/font-awesome.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
</head>

<body>

<div class="container">
    <span>Kawla Bassidi</span>
</div>

<div class="main">
    <!-- LOGIN FORM -->
    <div class="form-box active" id="login-form">
        <form action="login.php" method="POST">
            <h2>Login</h2>

            <?php
            if (isset($_GET['error'])) {
                echo "<p style='color:red'>Invalid Email, Password or Role!</p>";
            }
            ?>

            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>

            <select name="role" required>
                <option value="">Select Role</option>
                <option value="user">Student</option>
                <option value="user">Teacher</option>
                <option value="admin">Admin</option>
            </select>

            <button type="submit" name="login">Login</button>

            <p>Don't have an account!
                <a href="#" onclick="showForm('register-form')">Register</a>
            </p>

            <h3>@Design & Develop by: Group 05</h3>
        </form>
    </div>

    <!-- REGISTER FORM -->
    <div class="form-box" id="register-form">
        <form action="register.php" method="POST">
            <h2>Register</h2>

            <input type="text" name="name" placeholder="Name" required>
            <input type="text" name="studentId" placeholder="Student ID" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>

            <select name="role" required>
                <option value="">Select Role</option>
                <option value="user">Student</option>
                <option value="user">Teacher</option>
                <option value="admin">Admin</option>
            </select>

            <button type="submit" name="register">Register</button>

            <p>Already have an account!
                <a href="#" onclick="showForm('login-form')">Login</a>
            </p>

            <h3>@Design & Develop by: Group 05</h3>
        </form>
    </div>
</div>

<script src="script.js"></script>
</body>
</html>
