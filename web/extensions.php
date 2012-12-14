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
          <? navBar("extensions"); ?>
        </ul>
      </div>

      <hr>

		<div class="jumbotron">
			<h2 class="muted">Parallel Sequitur</h2>
			<h2 class="text-info">The Extensions</h2>

		</div>

      <hr>

		<div id="container">
			<h3> Potential Extensions for Future Work </h3>
			
			<h4>Rule Passing Sequitur</h4>
			
			<p>
				One method we wanted to explore was continuously passing rules between processes. The basic idea is that every time a new rule is created in the rank A process, by using the MPI broadcast and allgather, we can send the rule from rank A to all other ranks and receive rules from all other ranks. By doing this, we can maintain a master rule repository with all patterns seen amongst all processes. We believe that while there is extra communication earlier on, for larger strings we will be able to compress more later on because we generate many different rules at the start. Thus, by some extra cost at the start, we should be able to improve compression size and speed because we generate our master set faster at the start.
			</p>
			<h5>Potential Problems</h5>
			<p>This method runs into a few key issues. First, there is a lot of communication earlier on, and we need to send a lot of data between all processes in order to maintain the correct names for all rules. Furthermore, we need to ensure that there are no duplicates between the actual rules themselves. To reduce the communication, we propose using a threshold; after each process generates a number of rules indicated by the threshold, we conduct a large allgather and merge the rules together. Another approach to this method might be a master/slave style of rule generation, where the master hands out work and the global ruleset, and the slaves return new rules and the compressed dataset. We believe that this method overall has too much communication overhead to be viable.</p>
			
			<h4>Rule Overlapping</h4>
			
			<p>  
				In the rule overlapping approach, we propose that we have an overlap in the blocks being sent to the workers for rule generation and compression. By having an overlap, we will be able to generate rules faster and potentially find better rules to compress with. This method is tricky because we need to determine which strings to use in the final compressed output and we have no easy way to know which compressed strings will be the best. Although there is also extra communication, this method may provide us with rules earlier so we can compress faster later. Once again, the communication overhead and rule storage complexity makes this method seem too wasteful to be viable.
			</p>
			
			<h4>Rule Standardization</h4>
			<p>
				One potential way to make Sequitur compression useful across all texts would be to create a standardized ruleset using a dictionary such as Oxford along with statistical information on the most frequently used words. We make the assumption that we can create rules such that each name will be smaller than the rule it contains using this listing. In this situation, we no longer need to store the rules along with the encoding, and can send only the compressed output to a user, who can use his own standard ruleset to decode. This would save time and space required for compression.			
			</p>
			
			
			<h3>Improvements</h3>
			
			<h4>Rule Representation</h4>
			
			<p>
				While developing Sequitur, we came across a space issue when generating rules. In the original paper, rules are named with capital letters, such as A, B, C, etc. This namespace only allows for 26 rules and works only when the input strings consist of non-uppercase characters. To resolve the namespace issue, we decided to implement a new rule concept, where a rule would start and end with a rarely used character, such as @. This leads us to the representation of <code>@###@</code>, where the @ symbols contain a number. Thus, the 15th rule would be <code>@15@</code>. This method allows us to overcome the namespace limitation but presents us with new challenges.
			</p>
			<h5>Rule Length</h5>
			<p>The first issue is that of the size of the rule. As we have more rules, the size of the rule name gets larger. At a minimum, we require 3 characters for a rule name. Some rules that Sequitur generates may only encapsulate a single digram. By using 3 characters for a 2 character rule, we are not able to compress, but instead are increasing the size of the output. In our frequency analysis method, we are able to overcome this by ordering the words by length first and removing words smaller than 4 characters from the list. However, the merge and replace method has no such ability to keep track of string lengths, and is a potential cause for our lack of compression.</p>
			
			<h5>Additional Search Complexity</h5>
			<p>The general Sequitur algorithm uses the last 2 characters as a digram. In string searching, this is extremely easy because we can treat the string as an array, so there is <code>O(1)</code> lookup for each digram. By using this new rule scheme, we need to search the string for the @ symbol to find the start and end of a rule. This requires considerable extra time, and is <code>O(n)</code> in the size of the rule.</p>
	
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
