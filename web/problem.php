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
          <? navBar("problem"); ?>
        </ul>
      </div>

      <hr>

		<div class="jumbotron">
			<h2 class="muted">Parallel Sequitur</h2>
			<h2 class="text-info">The Problem</h2>
		</div>

      <hr>

		<div id="container">
			<h3> Sequitur Compression </h3>
				
			<p> For our final project, we decided to parallelize the Sequitur compression algorithm. Sequitur is a lossless compression algorithm that creates a hierarchical representation of the original sequence by replacing repeated phrases iteratively. Simply put, Sequitur attempts to compress a string by iterating over each pair of characters, called a digram. Whenever Sequitur sees that a digram has been repeated, the algorithm creates a rule for that digram and replaces all occurances of that digram in the string. By this logic, we should only ever see a digram once in the entire body of our string and ruleset; this is called <b style="color:green">Digram Uniqueness</b>. Sequitur also checks the ruleset constantly to make sure that all rules are used more than once. If a rule is used only one time throughout the set, Sequitur merges the rules by applying the singly-used rule, then removing it. By doing this, we can get rules which contain frequently seen substrings larger than 2 characters. This is called <b style="color:green">Rule Utility</b> and allows us to compress the ruleset as well as the original string.</p>
			
			<p>
				For example, we can compress the string <code> abracadabraarbadacarba </code>. As Sequitur iterates over the string, it sees that the digram <code>ab</code> is repeated (<code><b style="color:blue">ab</b>racad<b style="color:blue">ab</b>raarbadacarba</code>). We can create a rule, <code> 1 -> ab </code> and apply it to the string to get <code><b style="color:blue">1</b>racad<b style="color:blue">1</b>raarbadacarba</code>. The final result is shown below, where Rule 0 is the compressed string.
				<br>
				<pre><code>			
				0: 1c2132ac3a
				1: abra
				2: ad
				3: arb
				</code></pre>			
			</p>
			
			
			<h3>Parallelizing Sequitur</h3>
			<h4>The Idea Behind Merge and Replace</h4>
			<p> 
				Naturally, Sequitur is a serial compression algorithm which iterates over the input string several times. Because this algorithm requires us to sequentially make a decision over the next digram seen, one immediate issue when parallelizing Sequitur is that we need to split up the input and somehow merge all the compressed sets together afterwards. Using MPI, we decided that the best way to do this would be to run Sequitur on each process and merge the rulesets together in serial on the master process. The reasoning for this is that iteration is the most expensive computation we can do on the strings, and the rule sets returned are small enough to be globally merged and updated by the single master process.
			</p>
			
			<h3>Going Beyond the Rules</h3>
			<h4>Choose Your Words Carefully</h4>
			<p>
				After understanding how Sequitur works, we decided to try a more radical approach. Because Sequitur compresses by using frequency of substrings, we wondered if preprocessing the text would allow us to more easily compress. Our Frequency Analysis approach attempts to compress by first doing a word count over the entire text. We then create rules for all words that are used more than once in the text, and run Sequitur, replacing all the words for their corresponding rules. Using this approach, we believe that we can compress large texts in less time than the original parallel Sequitur because we only need to iterate over the string twice and create the ruleset once.
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
