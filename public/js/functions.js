window.addEventListener('load',function(){

// Desaparecer un alert
  $(".alert").fadeTo(4000, 500).slideUp(500, function(){
    $(".alert").slideUp(500);
  });

}) // Windows Load
