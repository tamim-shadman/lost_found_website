<?php
include "config.php";

$sql = "SELECT * FROM founditem";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title> View Page </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2>Posted Items</h2>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Item Name</th>
                <th>Address</th>
                <th>Address2</th>
                <th>City</th>
                <th>State</th>
                
            </tr>
        </thead>
        <tbody>
            <?php
                if($result->num_rows>0) {
                    while($row = $result->fetch_assoc()) {
            ?>
                    <tr>
                        <td><?php echo $row["id"]; ?> </td>
                        <td><?php echo $row["name"]; ?> </td>
                        <td><?php echo $row["address"]; ?> </td>
                        <td><?php echo $row["address2"]; ?> </td>
                        <td><?php echo $row["city"]; ?> </td>
                        <td><?php echo $row["state"]; ?> </td>
                        
                        <td><a class="btn btn-info" href="update.php?id=<?php echo $row['id']; ?>">
                        Edit</a>&nbsp;<a class="btn btn-danger" href="delete.php?id=<?php echo $row['id']; ?>">
                        Delete</a></td>
                    </tr>

                    <?php   }
            }
         ?>      
        </tbody>
    </table>
    </div>
</body>
</html>