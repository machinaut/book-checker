<!DOCTYPE html>
<html>
  <head> 
    <meta charset="utf-8">
    <title> Book-Based Spell Checker </title>
    <meta name="description" content="Spellcheck words based on a Project Gutenberg book!">
    <meta name="author" content="Alex Ray">

    <!-- Le HTML5 shim, for IE6-8 support of HTML elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- Le styles -->
    <link rel="stylesheet" href="css/bootstrap.css">
    <link href="css/docs.css" rel="stylesheet">

    <!-- Le javascript -->
    <script src="js/jquery.js" type="text/javascript" charset="utf-8"></script>

  </head>
  <body>
    <!-- Topbar
    ================================================== -->
    <div class="topbar" data-scrollspy="scrollspy" >
      <div class="topbar-inner">
        <div class="container">
          <a class="brand" href="#">Book-Based Spell Checker</a>
          <ul class="nav">
            <li class="active"><a href="#overview">Overview</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="#spellcheck">Spellcheck</a></li>
            <li><a href="#contact">Contact</a></li>
          </ul>
        </div>
      </div>
    </div>
  
    <!-- Masthead (blueprinty thing)
    ================================================== -->
    <div class="container">
    <header class="jumbotron masthead" id="overview">
      <div class="inner">
        <div class="container">
          <h1>Bookchecker, woohoo!</h1>
          <p class="lead">
            Bookchecker is an exercise: spellcheck words against a specific corpus 
            (e.g. Moby Dick). <br />
            The goal was to find a good way of raking found words. <br />
          </p>
          <p><strong>Open Source!</strong> All the codez can be cound on
            <a href="https://github.com/ajray/book-checker">my GitHub</a> 
            Feel free to poke around, play, and ask questions.
          </p>
        </div><!-- /container -->
      </div>
    </header>

    <div class="container">


    <!-- About
    ================================================== -->
    <section id="about">
      <div class="page-header">
        <h1>About Bootchecker <small>Whats in it?</small></h1>
      </div>
      <p>Simply put, this program reads in a work of literature (commonly referred to as a 
        'book'), and then lets you submit words to spell-check against it.  The algorithm weights
        both the <a href="http://en.wikipedia.org/wiki/Levenshtein_distance">similarity</a>
        of the given word to dictionary words, and the frequency of those dictionary words.
      </p>
      <p>The application logic is (per request) PHP, and a litle Python and Bash 
        for administrative file operations.  I've never done PHP before so this was me learning
        a new language as well.
      </p>
      <p>The beautliful UI is shamelessly taken from Twitter's, and this was an excuse to use it.
      Read more on <a href="http://twitter.github.com/bootstrap/">Bootstrap's homepage</a>.</p>
    </section>


    <!-- Spellcheck
    ================================================== -->
    <section id="spellcheck">
      <div class="page-header">
        <h1>Lets check some spells <small>Or word some checks</small></h1>
      </div>
      <form>
        <fieldset>
          <legend>Search Terms</legend>
          <div class="clearfix">
            <label for="book">Book</label>
            <div class="xlarge input">
              <input id="book" type="text" placeholder="Moby Dick" disabled/>
            </div>
          </div><!-- /clearfix -->
          <div class="clearfix success">
            <label for="word">Word</label>
            <div class="xlarge input">
              <input id="word" type="text"/>
              <span class="help-inline">Correct Word!</span>
            </div>
          </div><!-- /clearfix -->
          <div class="actions">
            <button class="btn primary">Check!</button>
          </div><!-- /actions -->
        </fieldset>
      </form>
    </section>


    <?php 
    // Dictionary filename
    static $dictfilename = "./moby.dict";
    // Max number of results to show
    static $maxresults = 12;
    // Levenshtein Distance Constants (all ints)
    static $lsins = 1; // Cost of insertion
    static $lsrep = 1; // Cost of replacement
    static $lsdel = 1; // Cost of deletion
    if (!isset($_GET["word"]) || empty($_GET["word"])) {
        // If no word to compare, do nothing
        //echo "<p>Please pick a word.</p>";
    } else {
        // grab the dictionary, read in parsed JSON
        global $dictfilename;
        $json = file_get_contents($dictfilename); 
        $dictionary = json_decode($json, TRUE, 2);
        $count = count($dictionary);
        //echo "<p>Read $count words from dictionary: $dictfilename.</p>";
  
        // class to store word and associated data
        class DictWord {
            var $dword; // Actual dictionary word this represents
            var $occur; // occurance of dictionary word in test
            var $score; // calculated score to given base word
  
            // CONSTRUCTOR: CALCULATE THE SCORE FOR THIS DICTIONARY WORD
            function DictWord ($base,$dictword,$occur) {
                $this->dword = $dictword;   // dictionary word
  
                // INPUTS TO THE SCORE CALCULATION
                // Given Constants defined globally 
                global $lsins, $lsrep, $lsdel; // levenshtein distance costs
                // Given Occurences of the word in the text (how 'common' the word is)
                $this->occur = $occur;
                // Calculate Levenshtein Distance (basically how 'different' the word is)
                $lsval = levenshtein($dictword,$base,$lsins,$lsrep,$lsdel);
                // Calculate Difference in word lenths between
                $diff = strlen($dictword) - strlen($base);
  
                // SCORE CALCULATION
                $this->score = $lsval;
            }
  
            // SORT: COMPARE THE RANK FOR THIS DICTIONARY WORD
            static function rank ( $a, $b ) {
                // First sort by score, lower is better. (distance)
                if ($a->score == $b->score) { 
                    // Then sort by occurences, more is better. (frequency)
                    if ($a->occur == $b->occur) {
                        return 0;
                    } 
                    return ($a->occur < $b->occur) ? +1 : -1; // more is better
                }
                return ($a->score > $b->score) ? +1 : -1; // less is better
            }
        }
  
        //echo "<p>Iterate List</p>";
        // This is the word to look up in the dictionary
        $word = $_GET["word"];
        // Iterate over the dictionary and calculate a score for each word
        // We know the dictionary is a shallow JSON object, so this is always "str"=>"str"
        foreach ($dictionary as $key => $val) { 
            if ($word == $key) { // stop prematurely if we find the word
                $found = $word;
                break;
            }
            $wordlist[] = new Dictword($word,$key,$val); // add to the dict and calc score
        } 
        // Check if we found the word, or not (then suggest words).
        if (isset($found)) { 
            echo "<h1>found</h1>"; 
        } else {
            // Now sort by scores to get the best dictionary words
            usort($wordlist, array("DictWord", "rank"));
  
            // Print it out as an HTML table to make it easy to look at
            //echo "<p>Suggesting for: $word.</p>";
            echo '<table class="bordered-table zebra-striped">
                <thead><tr> <th>#</th>
                <th>Dictionary Word</th> <th>Occurances in Text</th> <th>Calculated Score</th> 
                </tr></thead>';
            global $maxresults;
            for ($i = 0; $i < $maxresults; $i++) {
                $dword = $wordlist[$i]->dword;
                $occur = $wordlist[$i]->occur;
                $score = $wordlist[$i]->score;
                echo "<tr><td>$i</td><td>$dword</td><td>$occur</td><td>$score</td></tr>";
            }
            echo "</table>"; 
        }
    }
    ?>
    </section>


    <!-- Spellcheck
    ================================================== -->
    <section id="contact">
      <div class="page-header">
        <h1>Contact <small>Who am I?</small></h1>
      </div>
      <h3>Alex Ray</h3>
      <p>I'm a hacker and a nerd; I love interesting programming challenges (like this!), 
        I kludge open source software to do things it was never meant to do, 
        and I love building things.
      </p>
      <p>My <a href="https://github.com/ajray">GitHub</a> is the only resume I really care about,
        but if it's that important, my real <a href="https://github.com/ajray/XeTeX-Resume/blob/master/moderncv/ajray-resume.pdf?raw=true">Resume</a> is on GitHub too!
      All my contact info is on that, 
      </p>
      <p>I'm interested in startups and entrepreneurship in general, so I'd love the chance to
        talk shop.  Even moreso if you guys think you could use (another?) linux nerd to beat
        the systems.
      </p>
    </section>


    </div><!-- /container -->

    <footer class="footer">
      <div class="container">
        <p class="pull-right"><a href="#">Back to top</a></p>
        <p> Fueled by coffee: 
          <a href="http://twitter.com/machinaut" target="_blank">@machinaut </a> (le twitter) 
          <br />
          Code licensed under the <a href="http://www.apache.org/licenses/LICENSE-2.0" 
          target="_blank">Apache License v2.0</a>.  Documentation licensed under 
          <a href="http://creativecommons.org/licenses/by/3.0/">CC BY 3.0</a>.
        </p>
      </div>
    </footer>


  </body>
</html>
