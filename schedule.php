<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['set_availability'])) {
    $user_id = $_SESSION['user_id'];
    $day = $_POST['day'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    
    $stmt = $pdo->prepare("INSERT INTO availability (user_id, day_of_week, start_time, end_time) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $day, $start_time, $end_time]);
    echo "<script>alert('Availability set successfully!');</script>";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book_appointment'])) {
    $user_id = $_POST['user_id'];
    $visitor_email = $_POST['visitor_email'];
    $booking_date = $_POST['booking_date'];
    $start_time = $_POST['start_time'];
    
    $stmt = $pdo->prepare("INSERT INTO bookings (user_id, visitor_email, booking_date, start_time) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $visitor_email, $booking_date, $start_time]);
    // Simulate email confirmation (not implemented due to sandbox limitations)
    echo "<script>alert('Appointment booked successfully!');</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule Appointment - SchedulePro</title>
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
        .container {
            flex: 1;
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-container, .booking-container {
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 15px;
            color: #333;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
        }
        .btn {
            padding: 10px 20px;
            background: #ff6f61;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.1em;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #e55a50;
        }
        .calendar {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
            margin-top: 20px;
        }
        .calendar div {
            padding: 10px;
            background: #fff;
            border: 1px solid #ccc;
            text-align: center;
            cursor: pointer;
        }
        .calendar div:hover {
            background: #f0f0f0;
        }
        .time-slots {
            margin-top: 20px;
        }
        .time-slots button {
            margin: 5px;
            padding: 10px;
            background: #6e8efb;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .time-slots button:hover {
            background: #5a7de3;
        }
        @media (max-width: 600px) {
            .container {
                padding: 10px;
            }
            .calendar {
                grid-template-columns: repeat(3, 1fr);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if (isset($_SESSION['user_id'])): ?>
            <h2>Set Your Availability</h2>
            <div class="form-container">
                <form method="POST">
                    <div class="form-group">
                        <label for="day">Day of Week</label>
                        <select id="day" name="day" required>
                            <option value="Monday">Monday</option>
                            <option value="Tuesday">Tuesday</option>
                            <option value="Wednesday">Wednesday</option>
                            <option value="Thursday">Thursday</option>
                            <option value="Friday">Friday</option>
                            <option value="Saturday">Saturday</option>
                            <option value="Sunday">Sunday</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="start_time">Start Time</label>
                        <input type="time" id="start_time" name="start_time" required>
                    </div>
                    <div class="form-group">
                        <label for="end_time">End Time</label>
                        <input type="time" id="end_time" name="end_time" required>
                    </div>
                    <button type="submit" name="set_availability" class="btn">Set Availability</button>
                </form>
            </div>
        <?php endif; ?>
        
        <h2>Book an Appointment</h2>
        <div class="booking-container">
            <div class="calendar" id="calendar"></div>
            <div class="time-slots" id="time-slots"></div>
            <form method="POST" id="booking-form" style="display: none;">
                <input type="hidden" name="user_id" id="user_id" value="1">
                <div class="form-group">
                    <label for="visitor_email">Your Email</label>
                    <input type="email" id="visitor_email" name="visitor_email" required>
                </div>
                <div class="form-group">
                    <label for="booking_date">Selected Date</label>
                    <input type="date" id="booking_date" name="booking_date" readonly required>
                </div>
                <div class="form-group">
                    <label for="start_time">Selected Time</label>
                    <input type="time" id="start_time" name="start_time" readonly required>
                </div>
                <button type="submit" name="book_appointment" class="btn">Book Appointment</button>
            </form>
        </div>
    </div>
    <script>
        const calendar = document.getElementById('calendar');
        const timeSlots = document.getElementById('time-slots');
        const bookingForm = document.getElementById('booking-form');
        const bookingDate = document.getElementById('booking_date');
        const startTime = document.getElementById('start_time');

        function generateCalendar() {
            const today = new Date();
            const daysInMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0).getDate();
            calendar.innerHTML = '';
            for (let i = 1; i <= daysInMonth; i++) {
                const day = document.createElement('div');
                day.textContent = i;
                day.onclick = () => selectDate(i, today.getMonth(), today.getFullYear());
                calendar.appendChild(day);
            }
        }

        function selectDate(day, month, year) {
            const selectedDate = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            bookingDate.value = selectedDate;
            fetchTimeSlots(selectedDate);
            bookingForm.style.display = 'block';
        }

        function fetchTimeSlots(date) {
            // Simulate fetching available time slots (replace with actual DB query)
            timeSlots.innerHTML = '';
            const times = ['09:00', '10:00', '11:00', '14:00', '15:00'];
            times.forEach(time => {
                const button = document.createElement('button');
                button.textContent = time;
                button.onclick = () => {
                    startTime.value = time;
                };
                timeSlots.appendChild(button);
            });
        }

        generateCalendar();
    </script>
</body>
</html>
