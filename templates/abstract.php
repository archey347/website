<html>
 <head>
  <title><?php print($page_title); ?></title>
  
  <link rel="stylesheet" href="style.css">
  
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="">

 </head>
 <body>
  <div class="container">
  <div class="navbar-container">
   <div class="page-width"><div class="navbar">
    <div class="navbar-inside">
     <ul>
      <li style="padding: 7px;">
         <ul class="navbar-header">
           <li style="float: left;"><h3>Archey Barrell</h3></li>
           <li style="float: right;" class="mob-btn" onclick="toggleMenu()"><img src="/media/menu.svg"></li>
        </ul>
      </li>

      <li class="mob mob-hidden"><a href="/">Home</a></li>
      <li class="mob mob-hidden"><a href="/projects.php">Projects</a></li>
      <li class="mob mob-hidden"><a href="/contact.php">Contact</a></li>

     </ul>

    </div>
   </div></div>
  </div>
  <div class="page-container">
   <div class="page-width"><div class="page">
    <div class="page-inside">
      <?php
        if(isset($include_file)) {
  	  include($include_file);
        }
      ?>
    </div>
    <div class="footer">
     <p>
      &copy; Archey Barrell <?php print(date("Y")); ?>. All Rights Reserved.
     </p>
    </dib>
   </div></div>
  </div>
  </div>
  <script>
   function toggleMenu() {
     items = document.getElementsByClassName('mob')

     for(var i = 0; i < items.length; i++) {
       items[i].classList.toggle('mob-hidden')
     }

   }
  </script>
  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
 </body>	
</html>
