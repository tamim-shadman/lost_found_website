<?php
$conn = new mysqli("localhost","root","", "l&fbd");
if($conn->connect_error)
{
    die("Connection failed: ".$conn->connect_error);
}

if(isset($_POST["submit"])){
$name = isset($_POST['item_name']) ? filter_var($_POST['item_name'], FILTER_SANITIZE_STRING) : '';
$address = isset($_POST['address']) ? filter_var($_POST['address'], FILTER_SANITIZE_STRING) : '';
$address2 = isset($_POST['address2']) ? filter_var($_POST['address2'], FILTER_SANITIZE_STRING) : '';
$city = isset($_POST['city']) ? filter_var($_POST['city'], FILTER_SANITIZE_STRING) : '';
$state = isset($_POST['state']) ? filter_var($_POST['state'], FILTER_SANITIZE_STRING) : '';
// $calendar = isset($_POST['date']) ? filter_var($_POST['date'], FILTER_SANITIZE_STRING) : '';

if($_FILES["image"]["error"]==4)
{
    echo "
    <script> alert('Image does not Exist'); </script>" ;
}

else{
    $fileName=$_FILES["image"]["name"];
    $fileSize=$_FILES["image"]["size"];
    $tmpName=$_FILES["image"]["tmp_name"];
    $validImageExtension= ["jpg","jpeg", "png"];
    $imageExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    if(!in_array($imageExtension,$validImageExtension)){
        echo "
    <script> alert('Invalid Image'); </script>" ;
    }


else if($fileSize>10000000){
    echo 
    
    "<script> alert('Image too large'); </script>" 
    ;
}

else{
    $newImageName=uniqid();
    $newImageName = "." . $imageExtension;

    move_uploaded_file("E:\New folder\htdocs\lost_found_website\images", $newImageName);
    $query="INSERT INTO founditem VALUES(' ','$name', '$address', '$address2', '$city', '$state', CURRENT_TIMESTAMP, '$newImageName')";
    mysqli_query($conn,$query);

    echo
    "<script>
alert('Successfully added your information');

    </script>"
    ;
}
}
}
?>