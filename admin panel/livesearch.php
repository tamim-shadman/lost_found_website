<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
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
   
<div class="d-lg-flex flex-column pt-1">
                            <form class="d-flex justify-content-center align-items-center mt-5" action="recentposts.php" method="post" role="search">
                                <input class="form-control form-control-sm me-2" type="text" name="search" id="search" placeholder="Search" aria-label="Search" style="width: 320px; height: 50px; margin-top: 380px;">
                                <button class="btn btn-outline-danger btn-sm" type="submit" style="height: 35px; width:75px; margin-top:380px">Search</button>
                            </form>
                        </div>

                        <div class="col-md-2" style= "position:relative; margin-top:2px; margin-left:760px; width:280px;">
          <div class="list-group" id="show-list">
            
</div>
</div>

   


<script type="text/javascript">
$(document).ready(function(){
  $("#search").keyup(function(){
    var searchText= $(this).val();
    //alert(searchText);
    if(searchText!= ' '){
      $.ajax({
url: "action.php",
method: "POST",
data:{query:searchText},
success: function(response){
  $("#show-list").fadeIn();
  $("#show-list").html(response);
}
      });
    }

    else{
      $("#show-list").fadeOut();
      $("#show-list").html(' ');
    }
  });

  $(document).on('click','a',function(){

    $("#search").val($(this).text());
    $("#show-list").html(' ');
  }
  
  );
});
   
</script>

</body>
</html>