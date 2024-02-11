<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BD Police Missing Form</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="missing_person.css">
</head>
<body>






    <header >
        <nav class="  text-center text-white ">Bangladesh Police</nav>
    </header>

    <div class="form">
        <div class="container">
            <form class="report" method="post" action="missing.php">
                <h2 class="text-center">Missing Person Report</h2> 
                <p class="text-center">File Your Report Below</p>
                <div class="form_group">
                    <div class="row">
                        <div class="col-md-6">
                          
                            <input type="text" class="form-control" name="name" placeholder="Full Name">
                        </div>
                        <div class="col-md-6">
                            
                            <input type="text"  class="form-control" name="nid" placeholder="NID If Any">
                        </div>
                    </div>
        
                </div>
                <div class="form-group pt-3">
                    
                    <input type="text" class="form-control" name="phone" placeholder="Phone">
                </div>
                <div class="form-group">
                    
                    <input type="text" class="form-control" name="Date" placeholder="Missing Date">
                </div>
                <div class="form-group">
                   
                    <input type="text" class="form-control" name="Age" placeholder="Age">
                </div>
        
                <div class="form-group">
                     
                    <input type="text" class="form-control" name="haircolor" placeholder="Hair Color">
                </div>
        
                <div class="form-group">
                     
                    <input type="text" class="form-control" name="eyecolor" placeholder="Eye Color">
                </div>
        
                <div class="form-group">
                    
                    <input type="text" class="form-control" name="Cloths" placeholder="Cloths">
                </div>
        
                <input type="submit" class="btn btn-success btn-block" name="" value="Submit">
            </form>
        </div>
        
    </div>

    

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    
</body>
</html>