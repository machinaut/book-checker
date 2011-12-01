<?php 
// Dictionary filename
static $dictfilename = "./moby.dict";
// Max number of results to show
static $maxresults = 12;
// Levenshtein Distance Constants (all ints)
static $lsins = 15; // Cost of insertion
static $lsrep = 10; // Cost of replacement 
static $lsdel = 15; // Cost of deletion
// Length difference factor
static $diffconst = 9;
if (!isset($_POST["word"]) || empty($_POST["word"])) {
    // If no word to compare, do nothing
    echo "<p>Please pick a word.</p>";
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
            global $diffconst;
            // Given Occurences of the word in the text (how 'common' the word is)
            $this->occur = $occur;
            // Calculate Levenshtein Distance (basically how 'different' the word is)
            $lsval = levenshtein($dictword,$base,$lsins,$lsrep,$lsdel);
            // Calculate Difference between word lenths
            $diff = abs(strlen($dictword) - strlen($base)) * $diffconst;

            // SCORE CALCULATION
            $this->score = $lsval + $diff;
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
    $word = $_POST["word"];
    // Iterate over the dictionary and calculate a score for each word
    // We know the dictionary is a shallow JSON object, so this is always "str"=>"str"
    foreach ($dictionary as $key => $val) { 
        if ($word == $key) { // stop prematurely if we find the word
            $found = new Dictword($word,$key,$val);
            break;
        }
        $wordlist[] = new Dictword($word,$key,$val); // add to the dict and calc score
    } 
    // Check if we found the word, or not (then suggest words).
    if (isset($found)) { 
        echo '<span id="found">';
        echo '<table class="bordered-table zebra-striped" id="results"><thead><tr> <th>#</th>
            <th>Dictionary Word</th> <th>Occurances in Text</th> <th>Calculated Score</th> 
            </tr></thead>';
        $dword = $found->dword;
        $occur = $found->occur;
        $score = $found->score;
        echo "<tr><td>0</td><td>$dword</td><td>$occur</td><td>$score</td></tr></table></span>";
    } else {
        // Now sort by scores to get the best dictionary words
        usort($wordlist, array("DictWord", "rank"));

        // Print it out as an HTML table to make it easy to look at
        //echo "<p>Suggesting for: $word.</p>";
        echo '<span id="notfound">';
        echo '<table class="bordered-table zebra-striped" id="results"> <thead><tr> <th>#</th>
            <th>Dictionary Word</th> <th>Occurances in Text</th> <th>Calculated Score</th> 
            </tr></thead>';
        global $maxresults;
        for ($i = 0; $i < $maxresults; $i++) {
            $dword = $wordlist[$i]->dword;
            $occur = $wordlist[$i]->occur;
            $score = $wordlist[$i]->score;
            echo "<tr><td>$i</td><td>$dword</td><td>$occur</td><td>$score</td></tr>";
        }
        echo "</table></span>"; 
    }
}
?>
