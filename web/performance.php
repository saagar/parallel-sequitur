<?
	require_once("helpers.php");

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Parallel Sequitur</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
    <link href="css/bootstrap.css" rel="stylesheet">
    <style type="text/css">
      body {
        padding-top: 20px;
        padding-bottom: 40px;
      }

      /* Custom container */
      .container-narrow {
        margin: 0 auto;
        max-width: 700px;
      }
      .container-narrow > hr {
        margin: 30px 0;
      }

      /* Main marketing message and sign up button */
      .jumbotron {
        margin: 60px 0;
        text-align: center;
      }
      .jumbotron h1 {
        font-size: 72px;
        line-height: 1;
      }
      .jumbotron .btn {
        font-size: 21px;
        padding: 14px 24px;
      }

      /* Supporting marketing content */
      .marketing {
        margin: 60px 0;
      }
      .marketing p + h4 {
        margin-top: 28px;
      }
    </style>
    <link href="css/bootstrap-responsive.css" rel="stylesheet">
	<link href="css/pygments.css" rel="stylesheet">
	
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- Fav and touch icons -->
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="ico/apple-touch-icon-114-precomposed.png">
      <link rel="apple-touch-icon-precomposed" sizes="72x72" href="ico/apple-touch-icon-72-precomposed.png">
                    <link rel="apple-touch-icon-precomposed" href="ico/apple-touch-icon-57-precomposed.png">
                                   <link rel="shortcut icon" href="ico/favicon.png">
  </head>

  <body>

	<a href="https://github.com/raysaagar/parallel-sequitur"><img style="position: absolute; top: 0; right: 0; border: 0;" src="https://s3.amazonaws.com/github/ribbons/forkme_right_green_007200.png" alt="Fork me on GitHub"></a>
  
    <div class="container-narrow">
      <div class="masthead">
		<ul class="nav nav-pills pull-left">
          <? navBar("performance"); ?>
        </ul>
      </div>

      <hr>

		<div class="jumbotron">
			<h2 class="muted">Parallel Sequitur</h2>
			<h2 class="text-info">The Performance</h2>

		</div>

      <hr>

		<div id="container">
			<h3> Performance Benchmarks on Speed </h3>
			<h4> Merge and Replace Benchmarks</h4>
			<p> 
				For our benchmarking process, we ran a series of text through both the serial and Merge and Replace parallel versions of Sequitur, marking down the times as accurately as possible using the <code>MPI.Wtime()</code> and <code>time.time()</code>. We picked 5 articles of different lengths, which are contained in our repository on Github. For each text, we noticed that Merge and Replace was extremely faster and much more efficient. We were able to see massive speed ups in the process, proving to us that the parallel version of Sequitur benefits greatly from the parallelized MPI.
			</p>
				<?
					$dir = "img/time/*";  
					// Open a known directory, and proceed to read its contents  
					foreach(glob($dir) as $file)  
					{  
						//echo "filename: $file : filetype: " . filetype($file) . "<br />";
						echo "<img style='display: block; margin-left: auto; margin-right: auto;' src=".$file.">";
					}
				?>
			<p>The above images show the total time required for different file sizes and different numbers of processes.</p>
				<?
					$dir = "img/speedup/*";  
					// Open a known directory, and proceed to read its contents  
					foreach(glob($dir) as $file)  
					{  
						//echo "filename: $file : filetype: " . filetype($file) . "<br />";
						echo "<img style='display: block; margin-left: auto; margin-right: auto;' src=".$file.">";
					}
				?>
			<p>The above images show the speedups for different file sizes and different numbers of processes.</p>	
				<?
					$dir = "img/efficiency/*";  
					// Open a known directory, and proceed to read its contents  
					foreach(glob($dir) as $file)  
					{  
						//echo "filename: $file : filetype: " . filetype($file) . "<br />";
						echo "<img style='display: block; margin-left: auto; margin-right: auto;' src=".$file.">";
					}
				?>
			<p>The above images show the efficiencies for different file sizes and different numbers of processes.</p>	
			
			
			<h4> Frequency Analysis Method Benchmarks</h4>
			<p> 
				For our frequency analysis method, we only ran larger texts through the python program to note the time taken. We noticed immense speedups for our Wikipedia article, hobbit_wiki.txt, with the speedup for 8 processors at close to 500000 times the serial version, with an efficiency over 60000. This method seems extremely effective in terms of speed, possible because we iterate over the string 2 times rather than n times in the serial version. The Frequency Analysis method appears to be <code>O(2)</code> compared to <code>O(n^2)</code>. Because of the simplicity of the algorithm, we generate rules from substrings that can be easily found, as words are the smallest unit we deem useful for generating a rule, whereas the serial version will generate rules based on digrams as the smallest unit. We use a master/slave model to quickly count the words for rule generation, and apply the ruleset once over the string. This lack of immensive processing allows us to attempt compression on large files such as the King James' Bible (a file size of 4.2 M) in just under 2 seconds with 4 and 8 processors used with MPI.			
			</p>
			
			<h4> Which parallel method is faster? </h4>
			
			<p>
				It is of some importance to note that while both parallel methods are faster than the serial method, we were able to get significant speedups with our more radical approach. When we compared the Merge and Replace method with the the Frequency Analysis method, we saw that the word counting approach was by far the better method in terms of pure speed benchmarks.
			</p>
			<p>
				For parallel sequitur time, we note that the frequency analysis approach is much faster than the merge and replace approach for small number of processes. This intuitively makes sense because the frequency analysis approach utilizes the parallelization of MPI better; however, for larger number of proccesses, it seems that the communication overhead makes the two approaches about even.	The frequency analysis approach has a much higher speedup for moderate number of processes. Again, we suspect this is because of the communication overhead. When we communicate with 16 processes many many times, the speedup drops significantly.
			</p>
			<p>
				Furthermore, the efficiency of the frequency analysis approach is much hiegher than the merge and replace approach for the reasons explained earlier. Again, as the number of processes increases, the efficiency drops for the frequency analysis approach while it steadily climbs for the merge and replace approach.
			</p>
			<p>
				We propose that the more radical Frequency Analysis method is better because of the way we attempt to exploit the nature of the English language as well as the way we exploit the usage of MPI. By using Master/Slave, we only require the input line to be iterated over two times completely. By looking at word frequency and optimizing for word length, we are able to very quickly determine the master ruleset in one iteration over the word counts. After determining a constant set of rules, we apply them in one iteration over the string. In the Merge and Result method, we continuous iterate over the string to create bigger and more useful rules, which causes an increase in time. However, Frequency Analysis does require increased communication and will fail when the input text is too small, because there is not enough work to be sent to the processes.
			</p>
			
				<?
					$dir = "img/comparison/*";  
					// Open a known directory, and proceed to read its contents  
					foreach(glob($dir) as $file)  
					{  
						//echo "filename: $file : filetype: " . filetype($file) . "<br />";
						echo "<img style='display: block; margin-left: auto; margin-right: auto;' src=".$file.">";
					}
				?>
			<p>The above images show the comparison for different file sizes and different numbers of processes of the Merge and Replace vs. the Frequency Analysis Method.</p>	
		
			<h3>Conclusion</h3>
			<p> 
				After running a significant number of tests, we can confidently claim that parallelization of Sequitur improves speedup and efficiency when following the strict Sequitur method. However, in terms of speed and efficiency, we propose a newer, more radical approach to Sequitur compression which is based on frequency analysis. Furthermore, we believe taht a combination of frequency analysis and pure Sequitur may provide an improvement in compression while maintaining improved speedups over the serial algorithm.
			
			</p>
	
		</div>
	  


      <hr>

      <div class="footer">
	  <div class="span10">
		<p>
			A Computer Science 205 Final Project by Irene Chen and Saagar Deshpande</br>
			Harvard University
		</p>		
		</div>
      </div>

    </div> <!-- /container -->

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="js/jquery.js"></script>
    <script src="js/bootstrap-transition.js"></script>
    <script src="js/bootstrap-alert.js"></script>
    <script src="js/bootstrap-modal.js"></script>
    <script src="js/bootstrap-dropdown.js"></script>
    <script src="js/bootstrap-scrollspy.js"></script>
    <script src="js/bootstrap-tab.js"></script>
    <script src="js/bootstrap-tooltip.js"></script>
    <script src="js/bootstrap-popover.js"></script>
    <script src="js/bootstrap-button.js"></script>
    <script src="js/bootstrap-collapse.js"></script>
    <script src="js/bootstrap-carousel.js"></script>
    <script src="js/bootstrap-typeahead.js"></script>

	
	
  </body>
</html>
