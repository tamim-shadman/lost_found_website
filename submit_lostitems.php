<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="images/logo.png" href="images/logo.png" >
    <title>Lost and Found Bangladesh</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="User_profile.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
  
    <script src="https://kit.fontawesome.com/d39d7cbf13.js" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    
     
  
  </head>
<body>
    <?php
    session_start();

    // Check if the user is logged in.
    if (!isset($_SESSION['user_email'])) {
        // Redirect to the login page.
        header('Location: login.php');
        exit();
    }

    // Allow access to the page.
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
                          Your Items
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
      <main class="pt-5">
        <div class="container">
            <div class="row  d-flex justify-content-center align-items-center ">
                <div class="col-6 login-form mt-5">
                    <div class="card center-card shadow-lg p-3 mb-5 mt-5 bg-body-tertiary rounded" style="width: 35rem; height: auto;">
        
                        <div class="card-body card-contents">

                              <!-- submit lost item -->
                            <form class="card-text" action="lost_item.php" method="post" enctype="multipart/form-data">
                                <div class="col-md-12">
                                    <p class="card-title text-center fs-5 fw-bold my-3 text-dark">
                                        Reclaim Your Lost
                                </div>
        
                                <div class="col-md-12">
                                    <label for="itemName" class="form-label">Lost Item</label>
                                    <input type="text" class="form-control" id="itemName" name="item_name" placeholder="Enter item name">
                                  </div>
                                  
                                
                                <div class="col-12 mt-3">
                                    <label for="inputState" class="form-label">Specific Address</label>
                                    <select id="inputState" name="address" class="form-select">
                                      <option selected>Airport</option>
                                      <option>Bus-stand</option>
                                      <option>Residential Area</option>
                                      <option>Hotel</option>
                                      <option>Restaurant</option>
                                    </select>
                                </div>
                                <div class="col-12 mt-3">
                                  <label for="inputAddress2" class="form-label"> Explain more about the surroundings</label>
                                  <input type="text" class="form-control" id="inputAddress2" name="address2" placeholder="Apartment, holding number, studio, or floor">
                                </div>
                                <div class="row my-1">
                                    <div class="col-md-4">
                                        <label for="inputCity" class="form-label">City</label>
                                        <input type="text" class="form-control" id="inputCity" name="city">
                                      </div>
                                      <div class="col-md-4">
                                        <label for="inputState" class="form-label">State</label>
                                        <select id="inputState" name="state" class="form-select">
                                          <option selected>Dhaka</option>
                                          <option>Sylhet</option>
                                          <option>Chattogram</option>
                                          <option>Cumilla</option>
                                          <option>Cox's bazar</option>
                                        </select>
                                      </div>
                                     

                                </div>
                                <h6 class="text-muted mt-3">Add picture(s)..</h6>
                                <div class="input-group my-3">
                                    <input type="file" class="form-control" id="inputGroupFile02" name="image">
                                  
                                  </div>
                                
                                <div class="col-12">
                                  <button type="submit" class="btn btn-outline-success" name="submit">Submit</button>
                                </div>
                                <!--  will add google map here -->
                              </form>
        
                              
                             
                        </div>
                        
                    </div>
        
                </div>
        
                
            </div>
        </div>
    
      </main>
      


    












</body>
</html>