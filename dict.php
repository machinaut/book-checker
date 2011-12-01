<!DOCTYPE html>
<html>
<head> <title> OH PHP HOW THIS GET HERE I AM NOT GOOD WITH COMPUTER </title> </head>
<body>
<?php 
// Dictionary filename
static $dictfilename = "./moby.dict";
// Max number of results to show
static $maxresults = 30;
// Levenshtein Distance Constants (all ints)
static $lsins = 1; // Cost of insertion
static $lsrep = 1; // Cost of replacement
static $lsdel = 1; // Cost of deletion
echo "<p> Hello World </p>";
if (!isset($_GET["word"]) || empty($_GET["word"])) {
    // If no word to compare, do nothing
    echo "<p>Please pick a word.</p>";
} else {
    // grab the dictionary, stored in parsed JSON
    global $dictfilename;
    $json = file_get_contents($dictfilename); 

    // iterator to read the JSON dictionary 
    $dictionary = json_decode($json, TRUE, 2);
    $count = count($dictionary);
    echo "<p>Read $count words from dictionary: $dictfilename.</p>";

    // class to store word and associated data
    class DictWord {
        var $dword; // Actual dictionary word this represents
        var $occur; // occurance of dictionary word in test
        var $score; // calculated score to given base word
        // constructor
        function DictWord ($base,$dictword,$occur) {
            $this->dword = $dictword;   // dictionary word

            // CALCULATE THE SCORE FOR THIS DICTIONARY WORD
            // --------------------------------------------
            // ** MAGIC HAPPENS HERE **

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
        // static comparing, used for sorting an array of these
        // Iversely compare score (distance), smaller is better
        static function cmp_obj ( $a, $b ) {
            if ($a->score == $b->score) { 
                // same score, sort by occurences, more is better
                if ($a->occur == $b->occur) {
                    return 0;
                } 
                return ($a->occur < $b->occur) ? +1 : -1;
            }
            return ($a->score > $b->score) ? +1 : -1;
        }
        // prettyprinting. Kind of evil there is HTML formatting in here
        public function __toString() {
            return "<tr> <td>$this->dword</td> <td>$this->occur</td>
                <td>$this->score</td> </tr>\n";
        }
    }

    echo "<p>Iterate List</p>";
    // This is the word to look up in the dictionary
    $word = $_GET["word"];
    // Iterate over the dictionary and calculate a score for each word
    // We know the dictionary is a shallow JSON object, so this is always "str"=>"str"
    foreach ($dictionary as $key => $val) { 
        $wordlist[] = new Dictword($word,$key,$val);
    } 
    // Now sort by scores to get the best dictionary words
    usort($wordlist, array("DictWord", "cmp_obj"));

    // Print it out as an HTML table to make it easy to look at
    echo "<p>Suggesting for: $word.</p>";
    echo "<table><tr> <td>Dictionary Word</td> <td>Occurances in Text</td> 
            <td>Calculated Score</td> </tr>\n";
    global $maxresults;
    for ($i = 0; $i < $maxresults; $i++) {
        echo $wordlist[$i];
    }
    echo "</table>\n\n"; 
}
?>
</body>
</html>
