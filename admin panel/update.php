<?php
include "config.php";

if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $address = $_POST['address'];
    $address2 = $_POST['address2'];
    $city = $_POST['city'];
    $state = $_POST['state'];

    // Validate and sanitize user inputs here if necessary

    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("UPDATE `founditem` SET `name` = ?, `address` = ?, `address2` = ?, `city` = ?, `state` = ? WHERE `id` = ?");
    $stmt->bind_param("sssssi", $name, $address, $address2, $city, $state, $id);

    if ($stmt->execute()) {
        echo "Record updated successfully";
        $stmt->close();
        header("Location: view.php"); // Redirect to the "view" page
        exit(); // Ensure that no more code is executed after the redirect
    } else {
        echo "Error: " . $stmt->error;
        $stmt->close();
    }
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM `founditem` WHERE `id`='$id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $name = $row['name'];
            $address = $row['address'];
            $address2 = $row['address2'];
            $city = $row['city'];
            $state = $row['state'];
        }
        ?>

        <!DOCTYPE html>
        <html lang="en">
        <head>
            <title>Update Form</title>
        </head>
        <body>
        <h2>Update Form</h2>
        <form action="" method="post">
            <fieldset>
                <legend>Lost Items:</legend>
                Name:<br>
                <input type="text" name="name" value="<?php echo $name; ?>">
                <input type="hidden" name="id" value="<?php echo $id; ?>">
                <br>
                Address:<br>
                <input type="text" name="address" value="<?php echo $address; ?>">
                <br>
                Address2:<br>
                <input type="text" name="address2" value="<?php echo $address2; ?>">
                <br>
                City:<br>
                <input type="text" name="city" value="<?php echo $city; ?>">
                <br>
                State:<br>
                <input type="text" name="state" value="<?php echo $state; ?>">
                <br>

                <br><br>
                <input type="submit" value="Update" name="update">
            </fieldset>
        </form>
        </body>
        </html>

    <?php
    } else {
        header('Location: view.php');
    }
}
?>
