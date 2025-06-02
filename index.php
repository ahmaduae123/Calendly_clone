<?php
session_start();
if (isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='dashboard.php';</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SchedulePro - Home</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #6e8efb, #a777e3);
            color: #fff;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        header {
            background: rgba(0, 0, 0, 0.7);
            padding: 20px;
            text-align: center;
        }
        header h1 {
            margin: 0;
            font-size: 2.5em;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }
        .container {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 20px;
        }
        .container h2 {
            font-size: 2em;
            margin-bottom: 20px;
        }
        .container p {
            font-size: 1.2em;
            max-width: 600px;
            margin-bottom: 30px;
        }
        .btn {
            padding: 15px 30px;
            font-size: 1.2em;
            border: none;
            border-radius: 25px;
            background: #ff6f61;
            color: #fff;
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
            margin: 10px;
        }
        .btn:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }
        @media (max-width: 600px) {
            header h1 {
                font-size: 1.8em;
            }
            .container h2 {
                font-size: 1.5em;
            }
            .btn {
                padding: 10px 20px;
                font-size: 1em;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>SchedulePro</h1>
    </header>
    <div class="container">
        <h2>Effortless Scheduling</h2>
        <p>Schedule meetings with ease. Set your availability, share your booking link, and let others book time with you seamlessly.</p>
        <button class="btn" onclick="window.location.href='schedule.php'">Book a Meeting</button>
        <button class="btn" onclick="window.location.href='signup.php'">Sign Up</button>
        <button class="btn" onclick="window.location.href='login.php'">Login</button>
    </div>
</body>
</html>
