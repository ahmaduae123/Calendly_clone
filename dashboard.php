<?php
require 'db.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
}

$user_id = $_SESSION['user_id'];

// Handle new booking submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_booking'])) {
    $visitor_email = $_POST['visitor_email'];
    $booking_date = $_POST['booking_date'];
    $start_time = $_POST['start_time'];

    $stmt = $pdo->prepare("INSERT INTO bookings (user_id, visitor_email, booking_date, start_time) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $visitor_email, $booking_date, $start_time]);
    echo "<script>window.location.href='dashboard.php';</script>";
}

// Handle booking cancellation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cancel_booking'])) {
    $booking_id = $_POST['booking_id'];
    $stmt = $pdo->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ? AND user_id = ?");
    $stmt->execute([$booking_id, $user_id]);
    echo "<script>window.location.href='dashboard.php';</script>";
}

// Fetch all bookings for the user
$stmt = $pdo->prepare("SELECT * FROM bookings WHERE user_id = ?");
$stmt->execute([$user_id]);
$bookings = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SchedulePro</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #6b48ff, #a777e3);
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            line-height: 1.6;
        }
        .container {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
            text-align: center;
            position: relative;
        }
        h2, h3 {
            font-size: 1.8em;
            margin-bottom: 20px;
            color: #4a4a4a;
        }
        h3 {
            font-size: 1.4em;
        }
        .booking-list, .add-booking, .calendar-view {
            margin-top: 20px;
        }
        .booking-item {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 10px;
            text-align: left;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s;
        }
        .booking-item:hover {
            transform: translateY(-2px);
        }
        .booking-item p {
            margin: 5px 0;
        }
        .booking-item strong {
            color: #6b48ff;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
        }
        .btn {
            padding: 8px 15px;
            background: #ff5e57;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9em;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #e54a43;
        }
        .logout-btn, .signature {
            position: absolute;
            top: 15px;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            font-size: 0.9em;
            transition: background 0.3s;
        }
        .logout-btn {
            right: 15px;
            background: #ff5e57;
            color: #fff;
        }
        .logout-btn:hover {
            background: #e54a43;
        }
        .signature {
            right: 120px;
            font-family: 'Dancing Script', cursive;
            font-size: 1.2em;
            color: #6b48ff;
            text-decoration: none;
            background: none;
            cursor: default;
        }
        .calendar {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
            margin-top: 10px;
        }
        .calendar div {
            padding: 10px;
            background: #f0f0f0;
            border: 1px solid #ccc;
            text-align: center;
            cursor: pointer;
        }
        .calendar .booked {
            background: #6b48ff;
            color: #fff;
        }
        .calendar div:hover {
            background: #e0e0e0;
        }
        .calendar-details {
            margin-top: 15px;
            text-align: left;
        }
        .no-bookings {
            color: #888;
            font-style: italic;
        }
        @media (max-width: 600px) {
            .container {
                padding: 20px;
                margin: 10px;
            }
            h2 {
                font-size: 1.5em;
            }
            h3 {
                font-size: 1.2em;
            }
            .booking-item {
                padding: 10px;
            }
            .logout-btn, .signature {
                top: 10px;
                font-size: 0.8em;
            }
            .signature {
                right: 100px;
            }
            .calendar {
                grid-template-columns: repeat(4, 1fr);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="signature">Ahmad</div>
        <button class="logout-btn" onclick="window.location.href='logout.php'">Logout</button>
        <h2>Your Dashboard</h2>

        <!-- Add Booking Form -->
        <div class="add-booking">
            <h3>Add a Booking</h3>
            <form method="POST">
                <div class="form-group">
                    <label for="visitor_email">Visitor Email</label>
                    <input type="email" id="visitor_email" name="visitor_email" required>
                </div>
                <div class="form-group">
                    <label for="booking_date">Date</label>
                    <input type="date" id="booking_date" name="booking_date" required>
                </div>
                <div class="form-group">
                    <label for="start_time">Time</label>
                    <input type="time" id="start_time" name="start_time" required>
                </div>
                <button type="submit" name="add_booking" class="btn">Add Booking</button>
            </form>
        </div>

        <!-- Calendar View -->
        <div class="calendar-view">
            <h3>Your Schedule</h3>
            <div class="calendar" id="calendar"></div>
            <div class="calendar-details" id="calendar-details"></div>
        </div>

        <!-- Booking List -->
        <div class="booking-list">
            <h3>Your Bookings</h3>
            <?php if (count($bookings) > 0): ?>
                <?php foreach ($bookings as $booking): ?>
                    <div class="booking-item">
                        <p><strong>Date:</strong> <?php echo $booking['booking_date']; ?></p>
                        <p><strong>Time:</strong> <?php echo $booking['start_time']; ?></p>
                        <p><strong>Visitor:</strong> <?php echo $booking['visitor_email']; ?></p>
                        <p><strong>Status:</strong> <?php echo $booking['status']; ?></p>
                        <?php if ($booking['status'] == 'confirmed'): ?>
                            <form method="POST">
                                <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                <button type="submit" name="cancel_booking" class="btn">Cancel</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="booking-item no-bookings">
                    No bookings found.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        const calendar = document.getElementById('calendar');
        const calendarDetails = document.getElementById('calendar-details');
        const bookings = <?php echo json_encode($bookings); ?>;

        function generateCalendar() {
            const today = new Date();
            const year = today.getFullYear();
            const month = today.getMonth();
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            const firstDay = new Date(year, month, 1).getDay();

            // Add empty slots for days before the 1st
            for (let i = 0; i < firstDay; i++) {
                const emptyDay = document.createElement('div');
                calendar.appendChild(emptyDay);
            }

            // Add days of the month
            for (let i = 1; i <= daysInMonth; i++) {
                const day = document.createElement('div');
                day.textContent = i;
                const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(i).padStart(2, '0')}`;
                
                // Check if there are bookings on this day
                const hasBooking = bookings.some(booking => booking.booking_date === dateStr);
                if (hasBooking) {
                    day.classList.add('booked');
                }

                day.onclick = () => showBookingsForDay(dateStr);
                calendar.appendChild(day);
            }
        }

        function showBookingsForDay(date) {
            calendarDetails.innerHTML = `<h4>Bookings on ${date}</h4>`;
            const dayBookings = bookings.filter(booking => booking.booking_date === date);
            if (dayBookings.length > 0) {
                dayBookings.forEach(booking => {
                    const bookingInfo = document.createElement('p');
                    bookingInfo.textContent = `Time: ${booking.start_time}, Visitor: ${booking.visitor_email}, Status: ${booking.status}`;
                    calendarDetails.appendChild(bookingInfo);
                });
            } else {
                calendarDetails.innerHTML += '<p>No bookings on this day.</p>';
            }
        }

        generateCalendar();
    </script>
</body>
</html>
