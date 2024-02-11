<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="images/logo.png" href="images/logo.png" >
    <title>Lost and Found Bangladesh</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
   

</head>
<body>
<?php
$conn = new mysqli("localhost", "root", "", "l&fbd");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$query = "SELECT * FROM founditem";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-black fixed-top">
      <div class="container">
        <a class="navbar-brand  text-light pe-lg-4" href="index.php">
          <h3 class="fw-light logo" >Lost and found Bangladesh</h3>
          <h6 class="fw-lighter ps-md-5 sublogo">Lost Moments, Found Stories</h6>

      
          </a>

        <!-- Toggle button -->
        <button class="navbar-toggler shadow-none border-0 " type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon" ></span>
      </button>
      <!-- sidebar -->

        <div class="sidebar offcanvas offcanvas-start" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">


          <!-- sidebar header -->
          <div class="offcanvas-header text-dark border-bottom">
            <h5 class="offcanvas-title" id="offcanvasNavbarLabel">Lost and Found Bangladesh</h5>
            <button type="button" class="btn-close btn-close-dark" data-bs-dismiss="offcanvas" aria-label="Close"></button>
          </div>

          <!-- sidebar body -->
          <div class="offcanvas-body bg-black">
            <ul class="navbar-nav justify-content-end flex-grow-1 pe-lg-1 mt-1">
              <li class="nav-item pb-5">
                  <div class="dropdown d-flex align-items-center justify-content-center pt-1 pe-3">
                      <a href="#" class="dropdown-toggle text-decoration-none text-light" id="registrationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Report
                      </a>
                      <ul class="dropdown-menu bg-secondary-subtle" aria-labelledby="registrationDropdown">
                        <li class="pb-2"><a class="dropdown-item fw-bold" href="submit_lostitems.php">Missing items</a></li>
                        <li><a class="dropdown-item fw-bold" href="missing_person.php">Missing person</a></li>
                      </ul>
                  </div>
                  
              </li>
              <li class="nav-item mx-2">
                  <div class="dropdown d-flex align-items-center justify-content-center ">
                      <button class="btn btn-outline-secondary btn-md dropdown-toggle text-light" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Registration
                      </button>
                      <ul class="dropdown-menu bg-secondary-subtle text-white">
                        <li><a class="dropdown-item fw-bold" href="login.html">Login</a></li>
                        <li><a class="dropdown-item fw-bold" href="signup.html">Sign Up</a></li>                          
                       
                      </ul>
                    </div>
              </li>
              <li class="nav-item mx-2 mx-lg-1 pb-5">
                  <a class="nav-link text-light recent-post " href="recentposts.php">View recent Posts</a>
              </li>

              <li class="nav-item mx-2 mx-lg-1 pb-5">
                <a class="nav-link text-light recent-post " href="User_profile.html">Profile</a>
            </li>
              
            </ul>
            <!-- registration -->
           




            <div class="d-lg-flex flex-column pt-1">
              <form class="d-flex justify-content-center align-items-center mt-1" role="search">
              <input class="form-control form-control-sm me-2" type="search" placeholder="Search" aria-label="Search" style="width: 150px; height: 30px;">
              <button class="btn btn-outline-success btn-sm" type="submit" style="height: 30px;">Search</button>
              </form>
          </div>
          
          </div>
        </div>
      </div>
    </nav>


      <main class="container" style="margin-top: 120px;">
      <div class="container-fluid bg-secondary">
    <div class="container">
        <div class="row">
            <?php
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<div class='col-md-3 mb-1'>"; // Adjust col-md-4 based on your layout needs
                echo "<div class='bg-light text-dark p-3'>"; // Add primary background color
                echo "<p>Name: " . $row['name'] . "</p>";
                echo "<p>Address: " . $row['address'] . "</p>";
                echo "<p>Address 2: " . $row['address2'] . "</p>";
                echo "<p>City: " . $row['city'] . "</p>";
                echo "<p>State: " . $row['state'] . "</p>";

                // Check if the key 'calendar' exists in the row
                if (isset($row['calendar'])) {
                    echo "<p>Date: " . $row['calendar'] . "</p>";
                } else {
                    echo "<p>Date: N/A</p>";
                }

                // Check if the key 'image' exists in the row
                if (isset($row['image'])) {
                    echo "<img src='images/" . $row['image'] . "' alt='Item Image' class='img-fluid'>";
                } else {
                    echo "<p>Item Image: N/A</p>";
                }

                echo "</div>"; // Close the div for primary background color
                echo "</div>"; // Close the col-md-4
            }
            ?>
        </div>
    </div>
</div>






      <?php
      mysqli_close($conn);
      ?>
        
      </main>
    












</body>
</html>