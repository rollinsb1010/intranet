<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Centurion Framework</title>
  <meta name="description" content="Centurion Framework">
  <meta name="author" content="">
  
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  
  <link rel="stylesheet" href="/css/reset.css" />
  <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700' rel='stylesheet' type='text/css'>
  <!-- Centurion Framework -->
  <link rel="stylesheet" href="css/main.css" />
  <!-- End of Framework -->
 
  <link rel="stylesheet" href="css/custom.css" />
  
  <!--[if gte IE 9]>
    <style type="text/css">
      .gradient {
         filter: none;
      }
    </style>
  <![endif]-->
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
  <script type="text/javascript" src="js/centurion.js"></script>
  <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
  
  <style type="text/css">
  		
  
  </style>
  
  <script type="text/javascript">
    $(document).ready(function(){
      
      $('.calculate [class*="grid-"]').append('<p><span class="pixels"><!--px width--></span><br /><span class="percentage"><!--percentage--></span><br /></p>')
      
      $('.calculate [class*="grid-"]').each(function(){
        
        var el = $(this);
        var width = el.outerWidth() + 'px';
        //var per = el.outerWidth();
        //var percentage = (per * 10)/100 + '%';
        var gridClass = $(this).attr('class').split('grid-').join('').split(' ')[0];
        
        el.find('span.pixels').html(width);
        el.find('span.percentage').html(gridClass + '%');
      });
      
    });  
  </script>

</head>
<body>
 <!-- Navigation -->
<header>

	<div class="container">
		<div class="grid-100">
				<h1>Centurion Framework</h1>
				<nav id="main" class="mobile">
				<ul>
					<li><a href="../index.html">Home</a></li>
					<li><a href="about.html">About</a></li>
					<li><a href="grid.html">Grid</a></li>
					<li><a href="documentation.html">Docs</a></li>
				</ul>
			</nav>
		</div>
	</div>

</header>
<!-- End of Navigation -->

  <!-- Content Section -->
  <section id="grid">
    <div class="container">
        <div class="grid-100">
          <h1>The Grid</h1>
          <p class="noStyle">This is a page to show how the grid layout functions.</p>
        </div>
        <div class="clear"></div>

<section class="calculate">

<div class="grid-25">
    <!-- grid -->
</div>
<div class="grid-25">
    <!-- grid -->
</div>
<div class="grid-25">
    <!-- grid -->
</div>
<div class="grid-25">
    <!-- grid -->
</div>
<div class="clear"></div>



<!-- Grid layout - 12 column -->
<div class="grid-10">
    <!-- grid -->
</div>
<div class="grid-90">
    <!-- grid -->
</div>



<!-- 1 x 11 -->
<div class="clear"></div>

<div class="grid-20">
    <!-- grid -->
</div>
<div class="grid-80">
    <!-- grid -->
</div>



<!-- 2 x 10 -->
<div class="clear"></div>

<div class="grid-25">
    <!-- grid -->
</div>
<div class="grid-75">
    <!-- grid -->
</div>



<!-- 3 x 9 -->
<div class="clear"></div>

<div class="grid-33">
    <!-- grid -->
</div>
<div class="grid-66">
    <!-- grid -->
</div>



<!-- 4 x 8 -->
<div class="clear"></div>

<div class="grid-40">
    <!-- grid -->
</div>
<div class="grid-60">
    <!-- grid -->
</div>



<!-- 5 x 7 -->
<div class="clear"></div>

<div class="grid-50">
    <!-- grid -->
</div>
<div class="grid-50">
    <!-- grid -->
</div>



<!-- 6 x 6 -->
<div class="clear"></div>

<div class="grid-60">
    <!-- grid -->
</div>
<div class="grid-40">
    <!-- grid -->
</div>




<!-- 7 x 5 -->
<div class="clear"></div>

<div class="grid-66">
    <!-- grid -->
</div>
<div class="grid-33">
    <!-- grid -->
</div>




<!-- 8 x 4 -->
<div class="clear"></div>

<div class="grid-75">
    <!-- grid -->
</div>
<div class="grid-25">
    <!-- grid -->
</div>




<!-- 9 x 3 -->
<div class="clear"></div>

<div class="grid-80">
    <!-- grid -->
</div>
<div class="grid-20">
    <!-- grid -->
</div>



<!-- 10 x 2 -->
<div class="clear"></div>
<div class="grid-90">
    <!-- grid -->
</div>
<div class="grid-10">
    <!-- grid -->
</div>



<!-- 11 x 1 -->
<div class="clear"></div>

<div class="grid-100">
    <!-- grid -->
</div>
<!-- 12 -->



<div class="clear"></div>
</section>



<!-- PUSH GRIDS -->
<div class="grid-100">
  <h2>Push the Grid</h2>
</div>

<section class="calculate">
  <div class="grid-20">
      <!-- grid -->
  </div>
  <div class="clear"></div>
  
  <div class="grid-20 push-20">
      <!-- grid -->
  </div>
  <div class="clear"></div>
  
  <div class="grid-20 push-40">
      <!-- grid -->
  </div>
  <div class="clear"></div>
  
  <div class="grid-20 push-60">
      <!-- grid -->
  </div>
  <div class="clear"></div>
  
  <div class="grid-20 push-80">
      <!-- grid -->
  </div>
  <div class="clear"></div>
  
  <div class="grid-80 push-20">
      <!-- grid -->
  </div>
  <div class="grid-20 pull-80">
      <!-- grid -->
  </div>
  <div class="clear"></div>
  
  
  <div class="grid-75 push-25">
      <!-- grid -->
  </div>
  <div class="grid-25 pull-75">
      <!-- grid -->
  </div>
  <div class="clear"></div>
</section>





<!-- MAKE A CENTERED ELEMENT -->
<div class="grid-100">
  <h2>Center the Grid</h2>
</div>
<div class="clear"></div>

<section class="calculate">
  <div class="grid-10 centerGrid">
      <!-- grid -->
  </div>
  
  <div class="grid-20 centerGrid">
      <!-- grid -->
  </div>
  
  <div class="grid-30 centerGrid">
      <!-- grid -->
  </div>
  
  <div class="grid-40 centerGrid">
      <!-- grid -->
  </div>
  
  <div class="grid-50 centerGrid">
      <!-- grid -->
  </div>
  
  <div class="grid-60 centerGrid">
      <!-- grid -->
  </div>
  
  <div class="grid-70 centerGrid">
      <!-- grid -->
  </div>
  
  <div class="grid-80 centerGrid">
      <!-- grid -->
  </div>
  
  <div class="grid-90 centerGrid">
      <!-- grid -->
  </div>
  
  <div class="grid-100 centerGrid">
      <!-- grid -->
  </div>
  
  <div class="clear"></div>
</section>


<!-- GRID NESTING -->
<div class="grid-100">
  <h2>Nesting Grid Elements</h2>
  <p class="noStyle">Elements with green are pulled to the left, pink are pushed to the right, and anything in lightblue is a nested element within the grid element above.</p>
</div>



<section class="calculate">
  <div class="grid-20">
		<!-- grid -->
		 <div class="grid-50 alpha">
			<!-- grid -->
		</div>
		<div class="grid-50 omega">
			<!-- grid -->
		</div>
	</div>
	<div class="grid-80">
		<!-- grid -->
		<div class="grid-25 alpha">
			<!-- grid -->
		</div>
		<div class="grid-25">
			<!-- grid -->
		</div>
		<div class="grid-25">
			<!-- grid -->
		</div>
		<div class="grid-25 omega">
			<!-- grid -->
		</div>

	</div>
	<div class="clear"></div>


	<div class="grid-75">
		<!-- grid -->
		<div class="grid-66 alpha">
			<!-- grid -->
		</div>
		<div class="grid-33 omega">
			<!-- grid -->
		</div>
	</div>

	<div class="grid-25">
		<!-- grid -->

		<div class="grid-33 alpha">
			<!-- grid -->
		</div>
		<div class="grid-33">
			<!-- grid -->
		</div>
		<div class="grid-33 omega">
			<!-- grid -->
		</div>

	</div>
	<div class="clear"></div>




	<div class="grid-40">
		<!-- grid -->

		<div class="grid-50 alpha">
			<!-- grid -->
		</div>
		<div class="grid-50 omega">
			<!-- grid -->
		</div>

	</div>
	<div class="grid-60">
		<!-- grid -->

		<div class="grid-25 push-75 omega">
			<p>25%</p>
		</div>
		<div class="grid-75 pull-25 alpha">
			<p>75%</p>
		</div>
	</div>
	<div class="clear"></div>

	
	
	<div class="grid-50">
		<!-- grid -->

		<div class="grid-50 alpha">
			<!-- grid -->
		</div>
		<div class="grid-50 omega">
			<!-- grid -->
		</div>

	</div>
	<div class="grid-50">
		<!-- grid -->

		<div class="grid-33 alpha">
			<!-- grid -->
		</div>
		<div class="grid-33">
			<!-- grid -->
		</div>
		<div class="grid-33 omega">
			<!-- grid -->
		</div>
	</div>




	<div class="grid-50">
		<!-- grid -->

		<div class="grid-20 alpha">
		  <!-- grid -->
		</div>
		<div class="grid-20">
			<!-- grid -->
		</div>
		<div class="grid-20">
			<!-- grid -->
		</div>
		<div class="grid-20">
			<!-- grid -->
		</div>
		<div class="grid-20 omega">
			<!-- grid -->
		</div>

	</div>
	<div class="grid-50">
		<!-- grid -->

		<div class="grid-33 alpha">
			<!-- grid -->
		</div>
		<div class="grid-33">
			<!-- grid -->
		</div>
		<div class="grid-33 omega">
			<!-- grid -->
		</div>
	</div>
	<div class="clear"></div>
</section>



    </div>
    <!-- container -->
    <div class="clear"></div>
  </section>
  <!-- End of Content Section -->


  <!-- Footer -->
  <footer>
    <div class="container">
      <div class="grid-80">
        <p>Centurion Framework was created by <a href="http://www.justinhough.com" title="Justin Hough">Justin Hough</a>. Images supplied by <a href="http://placehold.it/">placehold.it</a></p>
      </div>
      <div class="grid-20">
      	<p>Licensed under <a href="http://www.gnu.org/licenses/gpl.html" rel="license">GPL</a> and <a href="http://www.opensource.org/licenses/mit-license.php" rel="license">MIT</a>.</p>
      </div>
    </div>
  </footer>
  <!-- End of Footer -->

  <script src="http://localhost:35729/livereload.js"></script>
</body>
</html>