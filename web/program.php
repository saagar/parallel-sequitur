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
	<link rel="stylesheet" href="http://yandex.st/highlightjs/7.3/styles/default.min.css">
	<script src="js/jquery.js"></script>
	<script src="http://yandex.st/highlightjs/7.3/highlight.min.js"></script>
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
          <li><a href="index.php">Home</a></li>
          <li><a href="#">Problem</a></li>
          <li class="active"><a href="program.php">Program</a></li>
		  <li><a href="#">Performance</a></li>
		  <li><a href="#">Results</a></li>
		  <li><a href="#">Remarks</a></li>
		  <li><a href="#">Extensions</a></li>
		  <li><a href="#">Resources</a></li>
        </ul>
      </div>

      <hr>

		<div class="jumbotron">
			<h2 class="muted">Parallel Sequitur</h2>
			<h2 class="text-info">The Code</h2>

		</div>

      <hr>

		<div id="container">
			
			<h3> The Approach </h3>
				
			
				[TEXT GOES HERE]
		
			
				<br>
				
				<h3> The Serial Method </h3>
				
				<div class="span4"></div>
				<br>
				
				<h3> Parallel Approach 1: Merge and Replace </h3>
				
					<p>
						The Merge and Replace method is essentially an embarrasingly parallel approach to Sequitur compression. In this method, the main process (rank 0) takes the input text and sends every process a block of text to process. Each process runs Sequitur in serial. When compression is complete, rank 0 polls the processes and receives the compression sets in order.
					</p>
					<p>
						At this point, it seems relatively straightforward; we should take all the sets and merge the compressed string as well as all of the rules. However, as each process ran Sequitur independently, we have no guarantees on the rule naming conventions; each process is permitted to use the same rule names. Thus, we 
					</p>
			
				
				<br>
				
				<h3> Parallel Approach 2: Frequency Analysis </h3>
			
				<p>
					In theory, Sequitur generates rules that should be applied many times in the text. If a rule is generated, then the substring contained by the rule should be found frequently in the text.	This suggested that if we can analyze the text we want to compress, we can treat the English language as the main source for our rule dictionary. In order to make this approach work, we break up the work into three phases, the Word Count Phase, the Rule Generation phase, and the Rule Application Phase.
				</p>
				<h4> Phase 1: Word Count </h4>
				<p>
					The Frequency Analysis approach is essentially a word counting method. Using an MPI Master/Slave approach, we first count the words in the text. Since the text can be extemely long, the master reads the next line from the input text file and passes it to the next available slave for processing. The slave process maintains a dictionary where each key-value pair is a word and its associated count. Each slave simply takes the line, splits it across the spaces, and increments the count for every word it sees. If a word is not in the dictionary, we simply add it in and start counting from 1. After the master runs out of work, the master/slave ends and each slave sends its dictionary back to the master process.
				</p>
				<p> 
					Once the master gets the dictionaries from each slave, we merge the dictionaries together into a master dictionary. This requires us to iterate over each slave dictionary and update the master dictionry. If a key, value is not in the master, we add it straight into the master's copy. If a key is already in the master, we just update with the new count.</p>
				
				<h4> Phase 2: Rule Generation</h4>
				<p>
					Using the master dictionary, we create our rules. Because our current rule naming scheme is a minimum of 3 characters (the smallest rule is @1@), we first need to sort the list of rules by length. This is to ensure that each rule we use will reduce the number of characters in the original text. We also remove all words with length less than 4, because applying rules to those words will not compress the text. 
				</p>
				<p>
					For each word in the remaining list, we generate a new rule. We store the new mappings in two dictionaries, a word to rule mapping and a rule to word mapping. The word to rule mapping helps us simplify our compression process and the rule to word mapping is for decoding afterwards when we output the compressed text.
				</p>
				<h4> Phase 3: Rule Application</h4>
				<p> In this phase, we apply the rules to the original text. First, the main process (referred to as rank 0) sends the master word to rule dictionary to the other processes. Next, the rank 0 process opens the text and sends every process a block of text. For 16 processes, 16 roughly equal blocks are sent.</p>
				<p>Each process applies the rules to the text using a simple find and replace iteration over each line in the block received. After completion, rank 0 polls each other process in sequence and merges the compressed text in the order it was sent out. The resulting output is the full compressed text followed by the master ruleset.</p>
				
			
			<br>
			<br>
	
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
