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
         <? navBar("results"); ?>
        </ul>
      </div>

      <hr>

		<div class="jumbotron">
			<h2 class="muted">Parallel Sequitur</h2>
			<h2 class="text-info">The Results</h2>

		</div>

      <hr>

		<div id="container">
			<h3> Performance Benchmarks on Compression Performance </h3>
			<h4> Merge and Replace Benchmarks</h4>
			<p> 
				When comparing the serial and parallel version of Sequitur, we first compared the serial version to the Merge and Replace method, which closely mirrors the original Sequitur algorithm. We noticed that both methods gave us similar results in terms of file compression size. This inherently makes sense because both the serial and Merge and Replace methods use the same algorithmic concepts when compressing.
			</p>
			<p>
				However, when comparing the original files to the compressed files, we notice that the compressed file sizes are much bigger than we expect. In fact, often times, we end up with an encoded file that is larger than the original file. We suspect that this is due to the rule naming concept we use. While the original paper uses single capital letters (A, B, C, etc.), we found that we needed a much larger namespace that will work for all texts. As such, we use the convention of <code>@###@</code>. For example, we start with <code>@1@</code> for the first rule. This convention requires each rule to be a minimum of 3 characters long; the serial and Merge and Replace methods requires each rule's replacement text to be a minimum of 2 characters long, or 1 digram. Due to this offset, we often find that our rule names are longer than the rule values, which we believe to be the main reason that we do not see compression. Furthermore, we need to package the rules with the final compressed output in order to decode it later, which adds to the file size.
			</p>

			<h4> Frequency Analysis Method Benchmarks</h4>
			<p> 
				We next compared the compression sizes of the Frequency Analysis method to the original documents. At first, we noticed extremely poor compression, much worse that the Merge and Replace method above. We realized that much of the rules in our rule set had longer names than replacement values; this is actually extremely easy to fix with the word counting method. We decided to reorder all words by length and only generate rules for words of size 4 or larger, with word counts of at least 2. This means that shorter words should, in theory, be assigned shorter rule names. Over time, each rule application should save characters, so our encoded text will be compressed. To test this new method, we ran the Frequency Analysis method on the hobbit_wiki.txt article located in our repository. After optimizations, we saw that the encoded file is 46K, which is exactly the same size as the original, and the rules add 9K to the file, bringing the total to 55K. Without the rules, we achieve 1:1 compression, and we can probably improve this as the file sizes get bigger, such as on the King James' Bible, where we saw actual compression.
			</p>
		
			<h3>Sequitur: Theory vs. Application</h3>
			<p> 
				Is Sequitur a good compression method? In theory, it seems so; with an infinite rule namespace of 1 character items and proper rule generation and rule utility, we should be able to achieve good compression on any string with frequent repetitions. However, in practice, we were unable to gain significant compression on any file we tested using the original Sequitur algorithm, in serial or in parallel. This is because the namespace of 1 character items is too small for large files, requiring us to use a new method where the minimum rule size is 3 characters and increasing exponentially, as seen in the graph below.

			</p>
			<img style='display: block; margin-left: auto; margin-right: auto;' src="img/rulescomparison.png"><br>
			<p>
				The Frequency Analysis method, which uses elements of the Sequitur algorithm, has potential to provide us with compression, especially as the file size grows larger. We believe that running the frequency analysis method, then potentially trying to run a parallel Merge and Replace on the result will allow us to exploit even more of the entropy of the English language, where rules would contain not just words but phrases, sentences, and even complete paragraphs of repeated text. In our extensions page, we also propose an idea to improve compression by using a universal ruleset based on frequency of word usage across the entire English language, using a standard universal dictionary as our starting point.				
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
