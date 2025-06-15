<?php

$host = 'localhost';
$username = 'root'; 
$password = ''; 
$database = 'beyonce_concert';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Compubook</title>
   
   <link rel="stylesheet" href="style.css">
</head>
<body>
        <div class="Header">
           <header>
              <h1>Beyonce Christmas Concert - 25 December 2025</h1>
           </header>
        </div>

        <div class="Ticktet_container">
                    <img src="https://assets.goal.com/images/v3/blt5d0d8f8a098d78d8/Beyonce%20Cowboy%20Carter%20Tour%20.jpg?auto=webp&format=pjpg&width=828&quality=60" alt="Coach">
              <div>
                       <form action="question1.php" method="post">
                             <h2>Get your tickets now!</h2>
                       <fieldset>
                             <label for="first_name">First Name:</label>
                             <input type="text" id="first_name" name="first_name" placeholder="First Name" required><br>
                             
                             <label for="last_name">Last Name:</label>
                             <input type="text" id="last_name" name="last_name" placeholder="Last Name" required><br>
                             
                             <label for="Age">Age:</label>
                             <input type="number" id="Age" name="Age" placeholder="Age" required><br>

                             <label>Gender:</label><br>
                             <select name="gender" required>
                                 <option value="">--Select--</option>
                                 <option value="Female">Female</option>
                                 <option value="Male">Male</option>
                                 <option value="Other">Other</option>
                             </select><br><br>

                            <div>
                                <p>Ticket Type:</p><br>
                                <label for="general_admission_ticket">General Admission</label>
                                <input type="radio" id="General_admission" name="Ticket" value="General Admission" required>
                                <input type="number" name="num_general" min="0" placeholder="Number of General Admission tickets" required><br>
                                <label for="VIP_ticket">VIP</label>
                                <input type="radio" id="VIP" name="Ticket" value="VIP" >
                                <input type="number" name="num_vip" min="0" placeholder="Number of VIP tickets" ><br>
                                <label for="VVIP_ticket">VVIP</label>
                                <input type="radio" id="VVIP" name="Ticket" value="VVIP" >
                                <input type="number" name="num_vvip" min="0" placeholder="Number of VVIP tickets" ><br>
                                <button type="submit">Book Ticket</button>
                            </div>
                          </fieldset>
                       </form>
               </div>
        </div>

        <?php
$host = 'localhost';
$username = 'root'; 
$password = ''; 
$database = 'beyonce_concert';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$maleCount = 0;
$femaleCount = 0;
$otherCount = 0;

$ageGroups = [
    "16-21" => 0,
    "22-30" => 0,
    "31-40" => 0,
    "41-50" => 0,
    "51+" => 0
];

$genderAgeGroupTickets = [
    "Male" => ["16-21" => 0, "22-30" => 0, "31-40" => 0, "41-50" => 0, "51+" => 0],
    "Female" => ["16-21" => 0, "22-30" => 0, "31-40" => 0, "41-50" => 0, "51+" => 0],
    "Other" => ["16-21" => 0, "22-30" => 0, "31-40" => 0, "41-50" => 0, "51+" => 0],
];


$tickets_avail = 60000;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $Name = $_POST["first_name"];
    $surname = $_POST["last_name"];
    $age = $_POST["Age"];
    $ticket = $_POST["Ticket"];
    $gender = $_POST["gender"];

    // validating Age restriction 
    if ($age < 16) {
        echo "<p style='color:red; text-align:center;'>Not for sale to persons under the age of 16.</p>";
    } else {
        
        if ($ticket == "General Admission") {
            $price = 500;
            $num_tickets = $_POST["num_general"];
        } elseif ($ticket == "VIP") {
            $price = 2000;
            $num_tickets = $_POST["num_vip"];
        } elseif ($ticket == "VVIP") {
            $price = 3000;
            $num_tickets = $_POST["num_vvip"];
        }

        $total = $num_tickets * $price;
        $first_letter = strtoupper(substr($ticket, 0, 1));
        $random_seat_number = rand(1, $tickets_avail); 
        $seat_number = $first_letter . $random_seat_number;

        $sql = "INSERT INTO bookings (first_name, last_name, age, gender, ticket_type, quantity, total_price, seat_number)
        VALUES ('$Name', '$surname', $age, '$gender', '$ticket', $num_tickets, $total, '$seat_number')";

        if ($conn->query($sql) === TRUE) {
            $tickets_avail -= $num_tickets;

            
            switch($gender) {
                case "Male":
                    $maleCount += $num_tickets;
                    break;
                case "Female":
                    $femaleCount += $num_tickets;
                    break;
                case "Other":
                    $otherCount += $num_tickets;
                    break;
            }

            //  age group
            if ($age >= 16 && $age <= 21) {
                $group = "16-21";
            } elseif ($age >= 22 && $age <= 30) {
                $group = "22-30";
            } elseif ($age >= 31 && $age <= 40) {
                $group = "31-40";
            } elseif ($age >= 41 && $age <= 50) {
                $group = "41-50";
            } else {
                $group = "51+";
            }

            $ageGroups[$group] += $num_tickets;
            $genderAgeGroupTickets[$gender][$group] += $num_tickets;

            //  Ticket section
            echo "<h2>Ticket Purchase Summary</h2>";
            echo "<table border='1' cellpadding='10'>
                    <tr><th>Name</th><th>Ticket Type</th><th>Quantity</th><th>Total Price</th><th>Seat Number</th></tr>
                    <tr><td>$Name $surname</td><td>$ticket</td><td>$num_tickets</td><td>R$total</td><td>$seat_number</td></tr>
                  </table>";

            // concert stats Stats 
            echo "<h2>Concert Statistics</h2>";
            echo "<table border='1' cellpadding='10'>
                    <tr><th>Gender</th><th>16-21</th><th>22-30</th><th>31-40</th><th>41-50</th><th>51+</th></tr>";

            foreach ($genderAgeGroupTickets as $gen => $groups) {
                echo "<tr><td>$gen</td><td>{$groups['16-21']}</td><td>{$groups['22-30']}</td><td>{$groups['31-40']}</td><td>{$groups['41-50']}</td><td>{$groups['51+']}</td></tr>";
            }

            echo "</table>";

            echo "<h3>Tickets Remaining: $tickets_avail</h3>";
        } else {
            echo "<p style='color:red;'>Error saving booking!</p>";
        }
    }
}

$conn->close();
?>
</body>
</html>
