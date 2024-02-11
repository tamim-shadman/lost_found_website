<?php
session_start();
?>


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
    <link rel="stylesheet" href="User_profile.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/d39d7cbf13.js" crossorigin="anonymous"></script>
   

</head>
<body class = "body">
   
        <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
            <div class="container">
                <a class="navbar-brand text-light pe-lg-4" href="index.php">
                    <h3 class="fw-light logo">Lost and found Bangladesh</h3>
                    <h6 class="fw-lighter ps-md-5 sublogo">Lost Moments, Found Stories</h6>
                </a>
        
                <!-- Toggle button -->
                <button class="navbar-toggler shadow-none border-0 " type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <!-- sidebar -->
        
                <div class="sidebar offcanvas offcanvas-start" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
        
        
                    <!-- sidebar header -->
                    <div class="offcanvas-header text-dark border-bottom">
                        <h5 class="offcanvas-title" id="offcanvasNavbarLabel">Lost and Found Bangladesh</h5>
                        <button type="button" class="btn-close btn-close-dark" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div>
        
                    <!-- sidebar body -->
                    <div class="offcanvas-body">
                        <?php

                    if (isset($_SESSION['user_email'])) {
                      echo '   <ul class="navbar-nav justify-content-end flex-grow-1 pe-lg-1 mt-1">
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
                    
                      <li class="nav-item mx-2 mx-lg-1 pb-5">
                          <a class="nav-link text-light recent-post " href="recentposts.php">View recent Posts</a>
                      </li>
  
                      <li class="nav-item mx-2 mx-lg-1 pb-5">
                          <a class="nav-link text-light recent-post " href="User_profile.html">Profile</a>
                        
                      </li>
                      <li class="nav-item mx-2 mx-lg-1 pb-5">
                      <a href="logout.php" class="btn btn-outline-danger">Logout</a>
                      </li>
                      <li class="nav-item mx-2 mx-lg-1 pb-5">
                      <a href="chatbot.php" class="btn btn-outline-success">Ask a Query</a>
                      </li>
                      <div class="d-lg-flex flex-column pb-5">
                      <form class="d-flex justify-content-center align-items-center mt-1" role="search" method="post" action="livesearch.php">
                          <button class="btn btn-outline-info btn-sm" type="submit" style="height: 30px;">Search</button>
                      </form>
                  </div>

                  
                      
                </ul> ';
                    

                        

  
                    }
                    else{

                      echo' <li class="nav-item mx-5">
                      <div class="dropdown d-flex align-items-center justify-content-center ">
                          <button class="btn btn-outline-secondary ms-5 btn-lg dropdown-toggle text-light" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                              Registration
                          </button>
                          <ul class="dropdown-menu bg-secondary-subtle text-white">
                              <li><a class="dropdown-item fw-bold" href="login.html">Login</a></li>
                              <li><a class="dropdown-item fw-bold" href="signup.html">Sign Up</a></li>
                          </ul>
                      </div>
                  </li>
                  ';

                      
                      
                    }?>
                        
                        
        
                       
        
                    </div>
                </div>
            </div>
        </nav>
        
        

        <main style="margin-top: 150px;">
            
          
  
            <?php
            if (isset($_SESSION['user_email'])) {
              echo '<!-- recenet post section -->
              <h3 class="offcanvas-title text-center text-light mt-5" id="offcanvasNavbarLabel">Recent Posts</h3>
              <div id="carouselExampleCaptions" class="carousel slide">
                <!-- carousal for recent posts -->
                <div class="carousel-indicators">
                  <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                  <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1" aria-label="Slide 2"></button>
                  <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2" aria-label="Slide 3"></button>
                </div>
                <div class="carousel-inner">
                  <div class="carousel-item active">
                    <img src="..." class="d-block w-100" alt="...">
                    <div class="carousel-caption d-none d-md-block">
                      <h5>First slide label</h5>
                      <p>Some representative placeholder content for the first slide.</p>
                    </div>
                  </div>
                  <div class="carousel-item">
                    <img src="..." class="d-block w-100" alt="...">
                    <div class="carousel-caption d-none d-md-block">
                      <h5>Second slide label</h5>
                      <p>Some representative placeholder content for the second slide.</p>
                    </div>
                  </div>
                  <div class="carousel-item">
                    <img src="..." class="d-block w-100" alt="...">
                    <div class="carousel-caption d-none d-md-block">
                      <h5>Third slide label</h5>
                      <p>Some representative placeholder content for the third slide.</p>
                    </div>
                  </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
                  <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                  <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
                  <span class="carousel-control-next-icon" aria-hidden="true"></span>
                  <span class="visually-hidden">Next</span>
                </button>
              </div>
  
  
              <!-- hero section -->
              <div class="hero my-4 mx-auto" style="width: 50%;">
                  <h3 class="offcanvas-title my-5 text-center text-light " id="offcanvasNavbarLabel">Our Heroes</h3>
            <!-- carousal for heroes -->
            <div id="carouselExampleCaptions" class="carousel slide">
               
                <div class="carousel-indicators">
                  <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                  <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1" aria-label="Slide 2"></button>
                  <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2" aria-label="Slide 3"></button>
                </div>
                <div class="carousel-inner">
                  <div class="carousel-item active">
                    <img src="..." class="d-block w-100" alt="...">
                    <div class="carousel-caption d-none d-md-block">
                      <h5>First slide label</h5>
                      <p>Some representative placeholder content for the first slide.</p>
                    </div>
                  </div>
                  <div class="carousel-item">
                    <img src="..." class="d-block w-100" alt="...">
                    <div class="carousel-caption d-none d-md-block">
                      <h5>Second slide label</h5>
                      <p>Some representative placeholder content for the second slide.</p>
                    </div>
                  </div>
                  <div class="carousel-item">
                    <img src="..." class="d-block w-100" alt="...">
                    <div class="carousel-caption d-none d-md-block">
                      <h5>Third slide label</h5>
                      <p>Some representative placeholder content for the third slide.</p>
                    </div>
                  </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
                  <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                  <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
                  <span class="carousel-control-next-icon" aria-hidden="true"></span>
                  <span class="visually-hidden">Next</span>
                </button>
              </div>
              </div>
            
          

  
  ';}
  else{
    echo '<p class="text-center text-light fs-4 mx-auto">Got something to report?</p>
    <div class="d-flex justify-content-center align-items-center report">
      <button type="button" class="btn btn-primary m-2 p-2"><a href="submit_lostitems.php" class="text-light">Missing Item</a></button> 
      <button type="button" class="btn btn-danger m-2 p-2"><a href="missing_person.php"><span class="person">Missing Person</span></a></button> 
      <button type="button" class="btn btn-outline-success m-2 p-2"><a href="chatbot.php"><span class="chatbot">Ask Query</span></a></button> 
    </div>
    <p class="text-center text-primary fs-4 mx-auto mt-5">Login/Signup and explore the other things.</p>';

  }
            ?>


</main>
            
            
  <footer class="footer" style="margin-top: 50rem;">
    <div class="footer-container">
        <div class="row">
            <div class="footer-col">
                <h4>Lost & Found</h4>
                <ul>
                    <li><a href="#">about us</a></li>
                    <li><a href="#">Contact US</a></li>
                    <li><a href="#">our services</a></li>
                    <li><a href="#">affiliate program</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>get help</h4>
                <ul>
                    <li><a href="#">FAQ</a></li>
                    <li><a href="#">Anti-scam</a></li>
                    <li><a href="#">Post Status</a></li>
                    <li><a href="#">Payment Options</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>My Account</h4>
                <ul>
                    <li><a href="#">Profile</a></li>
                    <li><a href="#">Home</a></li>
                    <li><a href="#">My Ads</a></li>
                    <li><a href="#">Favourite Ads</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>follow us</h4>
                <div class="social-links">
                    <a href="https://www.facebook.com/"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://twitter.com/?lang=en"><i class="fa-brands fa-x-twitter"></i></a>
                    <a href="https://www.instagram.com/"><i class="fab fa-instagram"></i></a>
                    <a href="https://www.linkedin.com/?original_referer="><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
        </div>
    </div>

    <div class="d-lg-flex flex-column pb-5">
                      <form class="d-flex justify-content-center align-items-center mt-1" role="search" method="post" action="payment.php">
                          <button class="btn btn-outline-success btn-sm" type="submit" style="height: 30px;">Buy us a Coffee!</button>
                      </form>
                  </div>

    <div class="copyright">
        <div class="bottom_text">
            <span class="copyright_text">© 2023 Lost & Found. All Rights Reserved. Developed by Group-06. All rights Reserved</span>
            <span class="policy_terms">
            <a href="#">Privacy policy</a>
            <a href="#">Terms & Conditions</a>
        </span>
        </div>

        </div>
</footer>


    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <script>
          $(document).ready(function () {
              // Trigger the carousel to start sliding
              $('#slidingNamesCarousel').carousel();
  
              // Set the interval for sliding (adjust the duration as needed)
              var interval = 2000; // 2 seconds
  
              // Function to slide to the next item
              function slideNext() {
                  $('#slidingNamesCarousel').carousel('next');
              }
  
              // Start sliding
              setInterval(slideNext, interval);
          });
      </script>

      <script>
        
chrome.runtime.sendMessage({greeting: “hello”}, function (response) {
if (!chrome.runtime.lastError) {
// if you have any response
} else {
// if your document doesn’t have any response, it’s fine but you should actually handle
// it and we are doing this by carefully examining chrome.runtime.lastError
}
});

</script>
</body>
</html>