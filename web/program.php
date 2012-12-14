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
          <? navBar("program"); ?>
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
				
				<p>We first implemented a serial version of Sequitur in order to fully understand the algorithm. By doing this, we gained some sense of how best to attempt the parallel version of Sequitur as well as some problems that we might run into. Using MPI, we created an embarrassingly parallel version of Sequitur trying to stick to the original algorithm as closely as possible. We then attempted to exploit the frequency of word usage in the English language with a more radical approach.</p>
				<h5>Dependencies</h5>
				<ul>
					<li>MPI (mpi4py)</li>
					<li>Python (sys, csv)</li>
				</ul>
				<p>
				The above packages should already be installed on the resonance.seas cluster for CS 205. On the resonance node, <code>module load courses/cs205/2012</code> will load these modules into the user environment automatically.
				</p>
				
				<h3> The Serial Method </h3>
				
				[TEXT GOES HERE]
				
				<br>
				
				<h3> Parallel Approach 1: Merge and Replace </h3>
					<p>
						The Merge and Replace method is essentially an embarrasingly parallel approach to Sequitur compression. In this method, the main process (rank 0) takes the input text and sends every process a block of text to process. Each process runs Sequitur in serial. When compression is complete, rank 0 polls the processes and receives the compression sets in order.
					</p>
					<p>
						At this point, it seems relatively straightforward; we should take all the sets and merge the compressed string as well as all of the rules. However, as each process ran Sequitur independently, we have no guarantees on the rule naming conventions; each process is permitted to use the same rule names, starting from <code>@1@</code>. Thus, we need to globally update all of the dictionaries first. We iterate through all of the rules and uniquely rename the rules. Then we merge all of the compressed strings and the rule sets to produce the compressed text.
					</p>
				
				<h5>Merge and Replace Function</h5>
<div class="highlight"><pre>
<span class="k">def</span> <span class="nf">merge_and_replace</span><span class="p">(</span><span class="n">list_of_grammars</span><span class="p">):</span>
    <span class="n">masterset</span> <span class="o">=</span> <span class="p">{}</span>
    <span class="n">back_dict</span> <span class="o">=</span> <span class="p">{}</span>

    <span class="n">num_rules</span> <span class="o">=</span> <span class="mi">0</span>

    <span class="c"># re-number all rules for all grammars in list of grammars</span>
    <span class="k">for</span> <span class="n">grammar</span> <span class="ow">in</span> <span class="n">list_of_grammars</span><span class="p">:</span>
        
        <span class="n">all_rules</span> <span class="o">=</span> <span class="p">[</span><span class="n">a</span> <span class="k">for</span> <span class="n">a</span> <span class="ow">in</span> <span class="n">grammar</span><span class="o">.</span><span class="n">keys</span><span class="p">()</span> <span class="k">if</span> <span class="n">a</span> <span class="o">!=</span> <span class="s">&#39;0&#39;</span><span class="p">]</span>
        
        <span class="c"># for each rule, if the actual rule hasn&#39;t been seen before</span>
        <span class="k">for</span> <span class="n">x</span> <span class="ow">in</span> <span class="n">all_rules</span><span class="p">:</span>
            <span class="c"># create new rule and insert into record</span>
            <span class="k">if</span> <span class="ow">not</span> <span class="n">grammar</span><span class="p">[</span><span class="n">x</span><span class="p">]</span> <span class="ow">in</span> <span class="n">back_dict</span><span class="o">.</span><span class="n">keys</span><span class="p">():</span>
                <span class="n">num_rules</span> <span class="o">+=</span> <span class="mi">1</span>
                <span class="c"># re-number rule and add to list of rules that have been seen before</span>
                <span class="n">grammar</span><span class="p">[</span><span class="s">&#39;0&#39;</span><span class="p">]</span> <span class="o">=</span> <span class="n">grammar</span><span class="p">[</span><span class="s">&#39;0&#39;</span><span class="p">]</span><span class="o">.</span><span class="n">replace</span><span class="p">(</span><span class="s">&#39;@&#39;</span><span class="o">+</span><span class="n">x</span><span class="o">+</span><span class="s">&#39;@&#39;</span><span class="p">,</span> <span class="s">&#39;@&#39;</span><span class="o">+</span><span class="nb">str</span><span class="p">(</span><span class="n">num_rules</span><span class="p">)</span><span class="o">+</span><span class="s">&#39;@&#39;</span><span class="p">)</span>
                <span class="n">back_dict</span><span class="p">[</span><span class="n">grammar</span><span class="p">[</span><span class="n">x</span><span class="p">]]</span> <span class="o">=</span> <span class="nb">str</span><span class="p">(</span><span class="n">num_rules</span><span class="p">)</span>
                <span class="n">masterset</span><span class="p">[</span><span class="nb">str</span><span class="p">(</span><span class="n">num_rules</span><span class="p">)]</span> <span class="o">=</span> <span class="n">grammar</span><span class="p">[</span><span class="n">x</span><span class="p">]</span>

            <span class="k">else</span><span class="p">:</span>
                <span class="k">try</span><span class="p">:</span>
                    <span class="c"># if the rule has been seen before</span>
                    <span class="n">rule_to_use</span> <span class="o">=</span> <span class="n">back_dict</span><span class="p">[</span><span class="n">masterset</span><span class="p">[</span><span class="n">x</span><span class="p">]]</span>
                    <span class="c"># use previous rule to rewrite main string</span>
                    <span class="n">grammar</span><span class="p">[</span><span class="s">&#39;0&#39;</span><span class="p">]</span><span class="o">.</span><span class="n">replace</span><span class="p">(</span><span class="n">x</span><span class="p">,</span> <span class="nb">str</span><span class="p">(</span><span class="n">rule_to_use</span><span class="p">))</span>
                    <span class="c"># save accordingly</span>
                    <span class="n">grammar</span><span class="p">[</span><span class="n">rule_to_use</span><span class="p">]</span> <span class="o">=</span> <span class="n">grammar</span><span class="p">[</span><span class="n">x</span><span class="p">]</span>
                <span class="k">except</span><span class="p">:</span>
                    <span class="n">num_rules</span> <span class="o">+=</span> <span class="mi">1</span>
                    <span class="c"># re-number rule and add to list of rules that have been seen before</span>
                    <span class="n">grammar</span><span class="p">[</span><span class="s">&#39;0&#39;</span><span class="p">]</span> <span class="o">=</span> <span class="n">grammar</span><span class="p">[</span><span class="s">&#39;0&#39;</span><span class="p">]</span><span class="o">.</span><span class="n">replace</span><span class="p">(</span><span class="s">&#39;@&#39;</span><span class="o">+</span><span class="n">x</span><span class="o">+</span><span class="s">&#39;@&#39;</span><span class="p">,</span> <span class="s">&#39;@&#39;</span><span class="o">+</span><span class="nb">str</span><span class="p">(</span><span class="n">num_rules</span><span class="p">)</span><span class="o">+</span><span class="s">&#39;@&#39;</span><span class="p">)</span>
                    <span class="n">back_dict</span><span class="p">[</span><span class="n">grammar</span><span class="p">[</span><span class="n">x</span><span class="p">]]</span> <span class="o">=</span> <span class="nb">str</span><span class="p">(</span><span class="n">num_rules</span><span class="p">)</span>
                    <span class="n">masterset</span><span class="p">[</span><span class="nb">str</span><span class="p">(</span><span class="n">num_rules</span><span class="p">)]</span> <span class="o">=</span> <span class="n">grammar</span><span class="p">[</span><span class="n">x</span><span class="p">]</span>

                                                                                                 
    <span class="c"># find all rules &#39;0&#39; and merge them</span>
    <span class="n">stringMaster</span> <span class="o">=</span> <span class="s">&#39;&#39;</span>
    <span class="k">for</span> <span class="n">grammar</span> <span class="ow">in</span> <span class="n">list_of_grammars</span><span class="p">:</span>
        <span class="n">stringMaster</span> <span class="o">=</span> <span class="n">stringMaster</span> <span class="o">+</span> <span class="n">grammar</span><span class="p">[</span><span class="s">&#39;0&#39;</span><span class="p">]</span>
    
    <span class="c"># push rule &#39;0&#39; into masterset</span>
    <span class="n">masterset</span><span class="p">[</span><span class="s">&#39;0&#39;</span><span class="p">]</span> <span class="o">=</span> <span class="n">stringMaster</span>

    <span class="c"># done! pat yourself on the back and return masterset</span>
    <span class="k">return</span> <span class="n">masterset</span><span class="p">,</span> <span class="n">num_rules</span>
</pre></div>


				
				<h3> Parallel Approach 2: Frequency Analysis </h3>
			
				<p>
					In theory, Sequitur generates rules that should be applied many times in the text. If a rule is generated, then the substring contained by the rule should be found frequently in the text.	This suggested that if we can analyze the text we want to compress, we can treat the English language as the main source for our rule dictionary. In order to make this approach work, we break up the work into three phases, the Word Count Phase, the Rule Generation phase, and the Rule Application Phase.
				</p>
				<h4> Phase 1: Word Count </h4>
				<p>
					The Frequency Analysis approach is essentially a word counting method. Using an MPI Master/Slave approach, we first count the words in the text. Since the text can be extemely long, the master reads the next line from the input text file and passes it to the next available slave for processing. The slave process maintains a dictionary where each key-value pair is a word and its associated count. Each slave simply takes the line, splits it across the spaces, and increments the count for every word it sees. If a word is not in the dictionary, we simply add it in and start counting from 1. After the master runs out of work, the master/slave ends and each slave sends its dictionary back to the master process.
				</p>
				<h5>The Slave Counting Process</h5>
<div class="highlight"><pre>
<span class="k">def</span> <span class="nf">slave</span><span class="p">(</span><span class="n">comm</span><span class="p">):</span>

    <span class="n">rank</span> <span class="o">=</span> <span class="n">comm</span><span class="o">.</span><span class="n">Get_rank</span><span class="p">()</span>
    <span class="c"># need status updates</span>
    <span class="n">status</span> <span class="o">=</span> <span class="n">MPI</span><span class="o">.</span><span class="n">Status</span><span class="p">()</span>

    <span class="n">wc_dict</span> <span class="o">=</span> <span class="p">{}</span>

    <span class="k">while</span> <span class="bp">True</span><span class="p">:</span>
        <span class="c"># receive the job</span>
        <span class="n">line</span> <span class="o">=</span> <span class="n">comm</span><span class="o">.</span><span class="n">recv</span><span class="p">(</span><span class="n">source</span> <span class="o">=</span> <span class="mi">0</span><span class="p">,</span> <span class="n">tag</span> <span class="o">=</span> <span class="n">MPI</span><span class="o">.</span><span class="n">ANY_TAG</span><span class="p">,</span> <span class="n">status</span> <span class="o">=</span> <span class="n">status</span><span class="p">)</span>

        <span class="k">if</span> <span class="n">status</span><span class="o">.</span><span class="n">Get_tag</span><span class="p">()</span> <span class="o">==</span> <span class="mi">0</span><span class="p">:</span>
            <span class="c"># if we get the dietag, end the slave process and return the word counts</span>
            <span class="n">comm</span><span class="o">.</span><span class="n">send</span><span class="p">(</span><span class="n">wc_dict</span><span class="p">,</span> <span class="n">dest</span><span class="o">=</span><span class="mi">0</span><span class="p">,</span> <span class="n">tag</span><span class="o">=</span><span class="n">rank</span><span class="p">)</span>
            <span class="k">return</span>
        
        <span class="n">wordlist</span> <span class="o">=</span> <span class="n">line</span><span class="o">.</span><span class="n">split</span><span class="p">()</span>
        <span class="k">for</span> <span class="n">word</span> <span class="ow">in</span> <span class="n">wordlist</span><span class="p">:</span>
            <span class="c">#strip last char if not in A-Z,a-z,0-9</span>
            <span class="n">x</span> <span class="o">=</span> <span class="nb">ord</span><span class="p">(</span><span class="n">word</span><span class="p">[</span><span class="o">-</span><span class="mi">1</span><span class="p">:])</span>
            <span class="k">if</span> <span class="n">x</span> <span class="o">&lt;</span> <span class="mi">48</span> <span class="ow">or</span> <span class="n">x</span> <span class="o">&gt;</span> <span class="mi">57</span> <span class="ow">or</span> <span class="n">x</span> <span class="o">&lt;</span> <span class="mi">65</span> <span class="ow">or</span> <span class="n">x</span> <span class="o">&gt;</span> <span class="mi">90</span> <span class="ow">or</span> <span class="n">x</span> <span class="o">&lt;</span> <span class="mi">97</span> <span class="ow">or</span> <span class="n">x</span> <span class="o">&gt;</span> <span class="mi">122</span><span class="p">:</span>
                <span class="n">word</span> <span class="o">=</span> <span class="n">word</span><span class="p">[:</span><span class="o">-</span><span class="mi">1</span><span class="p">]</span>
            <span class="k">if</span> <span class="ow">not</span> <span class="n">word</span> <span class="ow">in</span> <span class="n">wc_dict</span><span class="p">:</span>
                <span class="n">wc_dict</span><span class="p">[</span><span class="n">word</span><span class="p">]</span> <span class="o">=</span> <span class="mi">1</span>
            <span class="k">else</span><span class="p">:</span>
                <span class="n">wc_dict</span><span class="p">[</span><span class="n">word</span><span class="p">]</span> <span class="o">+=</span> <span class="mi">1</span>

        <span class="n">comm</span><span class="o">.</span><span class="n">send</span><span class="p">(</span><span class="bp">True</span><span class="p">,</span><span class="n">dest</span><span class="o">=</span><span class="mi">0</span><span class="p">,</span><span class="n">tag</span><span class="o">=</span><span class="n">rank</span><span class="p">)</span>
</pre></div>
				
				<p> 
					Once the master gets the dictionaries from each slave, we merge the dictionaries together into a master dictionary. This requires us to iterate over each slave dictionary and update the master dictionry. If a key, value is not in the master, we add it straight into the master's copy. If a key is already in the master, we just update with the new count.</p>
				
				<h4> Phase 2: Rule Generation</h4>
				<p>
					Using the master dictionary, we create our rules. Because our current rule naming scheme is a minimum of 3 characters (the smallest rule is <code>@1@</code>), we first need to sort the list of rules by length. This is to ensure that each rule we use will reduce the number of characters in the original text. We also remove all words with length less than 4, because applying rules to those words will not compress the text. 
				</p>
				<p>
					For each word in the remaining list, we generate a new rule. We store the new mappings in two dictionaries, a word to rule mapping and a rule to word mapping. The word to rule mapping helps us simplify our compression process and the rule to word mapping is for decoding afterwards when we output the compressed text.
				</p>
				<h5>Rule Generation</h5> <h6>Sort the word and generate the rules.</h6>
<div class="highlight"><pre>
    <span class="c"># prune the count dictionary. remove all items smaller than 3 chars</span>
    <span class="c"># also, reorder. give smallest words the smallest rule names</span>
    <span class="k">if</span> <span class="n">rank</span> <span class="o">==</span> <span class="mi">0</span><span class="p">:</span>
         <span class="c"># get all word we want from dict and put them in a list</span>
        <span class="k">for</span> <span class="n">k</span><span class="p">,</span><span class="n">v</span> <span class="ow">in</span> <span class="n">wordcount</span><span class="o">.</span><span class="n">items</span><span class="p">():</span>
            <span class="k">if</span> <span class="n">v</span> <span class="o">&gt;</span> <span class="mi">1</span> <span class="ow">and</span> <span class="nb">len</span><span class="p">(</span><span class="n">k</span><span class="p">)</span> <span class="o">&gt;</span> <span class="mi">2</span><span class="p">:</span>
                <span class="c"># add word to list</span>
                <span class="n">rulestomake</span><span class="o">.</span><span class="n">append</span><span class="p">(</span><span class="n">k</span><span class="p">)</span>
        <span class="c"># sort the words we want by length</span>
        <span class="n">rulestomake</span><span class="o">.</span><span class="n">sort</span><span class="p">(</span><span class="n">key</span><span class="o">=</span><span class="nb">len</span><span class="p">)</span>
    <span class="n">stime</span> <span class="o">=</span> <span class="n">MPI</span><span class="o">.</span><span class="n">Wtime</span><span class="p">()</span>
    <span class="c"># generate the rules from the count dictionary</span>
    <span class="k">if</span> <span class="n">rank</span> <span class="o">==</span> <span class="mi">0</span><span class="p">:</span>
        <span class="k">for</span> <span class="n">item</span> <span class="ow">in</span> <span class="n">rulestomake</span><span class="p">:</span>
            <span class="n">rule</span> <span class="o">=</span> <span class="s">&#39;@&#39;</span> <span class="o">+</span> <span class="nb">str</span><span class="p">(</span><span class="n">rule_num</span><span class="p">)</span> <span class="o">+</span> <span class="s">&#39;@&#39;</span>
            <span class="n">wordtoruleset</span><span class="p">[</span><span class="n">item</span><span class="p">]</span> <span class="o">=</span> <span class="n">rule</span>
            <span class="n">ruletowordset</span><span class="p">[</span><span class="n">rule</span><span class="p">]</span> <span class="o">=</span> <span class="n">item</span>
            <span class="n">rule_num</span> <span class="o">+=</span> <span class="mi">1</span>
            <span class="n">totalchars</span> <span class="o">+=</span> <span class="nb">len</span><span class="p">(</span><span class="n">k</span><span class="p">)</span>
            <span class="n">totalrulesize</span> <span class="o">+=</span> <span class="nb">len</span><span class="p">(</span><span class="n">rule</span><span class="p">)</span>
        <span class="k">print</span> <span class="s">&quot;Total Chars: &quot;</span> <span class="o">+</span> <span class="nb">str</span><span class="p">(</span><span class="n">totalchars</span><span class="p">)</span>
        <span class="k">print</span> <span class="s">&quot;Total Rule Size: &quot;</span> <span class="o">+</span> <span class="nb">str</span><span class="p">(</span><span class="n">totalrulesize</span><span class="p">)</span>
        <span class="c"># calculate our rough stats. not very meaningful unless we analyze all differences</span>
        <span class="n">ruleset_compression</span> <span class="o">=</span> <span class="p">(</span><span class="n">totalchars</span> <span class="o">-</span> <span class="n">totalrulesize</span><span class="p">)</span><span class="o">/</span><span class="p">(</span><span class="nb">float</span><span class="p">(</span><span class="n">totalchars</span><span class="p">))</span>
        <span class="k">print</span> <span class="s">&quot;Compression of </span><span class="si">%f</span><span class="s"> if all rules used once&quot;</span> <span class="o">%</span> <span class="p">(</span><span class="n">ruleset_compression</span><span class="o">*</span><span class="mi">100</span><span class="p">)</span>

</pre></div>
				
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
