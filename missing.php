<?php
session_start();

        // Check if the form is submitted
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Establish a database connection (replace with your actual database details)
            $conn = new mysqli("localhost", "root", "", "l&fbd");

            // Check the connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Get form data
            $name = $_POST['name'];
            $nid = $_POST['nid'];
            $phone = $_POST['phone'];
            $missingDate = $_POST['Date'];
            $age = $_POST['Age'];
            $hairColor = $_POST['haircolor'];
            $eyeColor = $_POST['eyecolor'];
            $cloths = $_POST['Cloths'];

            // SQL query to insert data into the database (replace with your actual table name)
            $query = "INSERT INTO missingperson (Name, Nid, Phone, Missingdate, Age, Haircolor, Eyecolor, Cloths)
                    VALUES ('$name', '$nid', '$phone', '$missingDate', '$age', '$hairColor', '$eyeColor', '$cloths')";

            // Execute the query
            if ($conn->query($query) === TRUE) {
                echo "Record added successfully";
            } else {
                echo "Error: " . $query . "<br>" . $conn->error;
            }

            // Close the database connection
            $conn->close();
        }
?>
