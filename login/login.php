<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ✅ Include database connection
require_once '../admin/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $loginInput = isset($_POST['loginInput']) ? trim($_POST['loginInput']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (empty($loginInput) || empty($password)) {
        displayError("Error: Both fields are required.");
    }

    // ✅ Check whether input is email or username
    if (filter_var($loginInput, FILTER_VALIDATE_EMAIL)) {
        $sql = "SELECT * FROM users WHERE email = ?";
    } else {
        $sql = "SELECT * FROM users WHERE username = ?";
    }

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        displayError("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("s", $loginInput);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // ✅ Compare plain text password
        if ($password === $user['password']) {
            // ✅ Save user session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['is_admin'] = $user['is_admin'];

            // ✅ Success Message
            echo '
            <div class="login-success">
                <h2>Welcome back, ' . htmlspecialchars($user['full_name']) . '!</h2>
                <p>You have successfully logged in.<br>Redirecting to your dashboard...</p>
            </div>
            <style>
                body {
                    background: linear-gradient(to right, #ffe4b5, #ffb6c1);
                    font-family: Arial, sans-serif;
                    text-align: center;
                    margin: 0;
                    padding: 0;
                }
                .login-success {
                    margin: 100px auto;
                    padding: 40px;
                    background-color: #fff0e1;
                    border-radius: 40px;
                    width: 90%;
                    max-width: 500px;
                    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
                    animation: fadeIn 1s ease-in-out;
                }
                .login-success h2 {
                    font-family: "Suez One", serif;
                    font-size: 28px;
                    color: #2e7d32;
                    margin-bottom: 20px;
                }
                .login-success p {
                    font-size: 18px;
                    color: #444;
                    line-height: 1.6;
                }
                @keyframes fadeIn {
                    from { opacity: 0; transform: translateY(20px); }
                    to   { opacity: 1; transform: translateY(0); }
                }
            </style>';

            // ✅ Redirect after 2 seconds
            header("Refresh:2; url=../index.php");
            exit();
        } else {
            displayError("Error: Invalid password.");
        }
    } else {
        displayError("Error: User not found.");
    }

    $stmt->close();
    $conn->close();
}

// ⚠️ Error function
function displayError($message) {
    echo '
    <div class="error-message">' . htmlspecialchars($message) . '</div>
    <style>
        body {
            background: linear-gradient(to right, #ffe4b5, #ffb6c1);
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 0;
            padding: 0;
        }
        .error-message {
            margin: 100px auto;
            padding: 30px;
            background-color: #f8d7da;
            color: #721c24;
            border-radius: 20px;
            width: 90%;
            max-width: 500px;
            font-size: 18px;
            font-weight: bold;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
    </style>';
    exit();
}
?>
