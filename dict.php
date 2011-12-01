<!DOCTYPE html>
<html>
<head> <title> OH PHP HOW THIS GET HERE I AM NOT GOOD WITH COMPUTER </title> </head>
<body>
<?php 
echo "<p> Hello World </p>";
// Levenshtein Distance Constants (all ints)
$lsins = 1; // Cost of insertion
$lsrep = 1; // Cost of replacement
$lsdel = 1; // Cost of deletion
if (!isset($_GET["word"]) || empty($_GET["word"])) {
    // If no word to compare, do nothing
    echo "pick a word";
} else {
    // grab the dictionary, stored in parsed JSON
    $json = file_get_contents("dict.json"); 

    // iterate over every word in the dictionary
    $jsonIterator = new RecursiveIteratorIterator(
            new RecursiveArrayIterator(json_decode($json, TRUE)),
            RecursiveIteratorIterator::SELF_FIRST);

    // class to store word and associated data
    echo "<p> Class DictWord </p>";
    class DictWord {
        var $dword;
        var $score;
        var $lsval;
        var $occur;
        // constructor
        function DictWord ($base,$dictword,$occur) {
            $this->dword = $dictword;
            $this->lsval = levenshtein($base,$dictword,$lsins,$lsrep,$lsdel); //distance
            $this->occur = $occur; // occurences of word in moby dick
            $this->score = $this->lsval + abs(strlen($dictword)-strlen($base));
        }
        // static comparing, used for sorting an array of DictWords,
        static function cmp_obj ( $a, $b ) {
            if ($a->score == $b->score) { 
                return 0; 
            }
            return ($a->score > $b->score) ? +1 : -1;
        }
        // prettyprinting. Kind of evil there is HTML formatting in here
        public function __toString() {
            return "<tr>
                <td>$this->dword</td>
                <td>$this->lsval</td>
                <td>$this->occur</td>
                <td>$this->score</td>
                </tr>\n";
        }
    }

    echo "<p> Iterate List </p>";
    // This is the word to look up in the dictionary
    $word = $_GET["word"];
    // Iterate over the dictionary and calculate a score for each word
    // We know the dictionary is a shallow JSON object, so this is always "str"=>"str"
    foreach ($jsonIterator as $key => $val) { 
        $wordlist[] = new Dictword($word,$key,$val);
    } 
    // Now sort by scores to get the best dictionary words
    usort($wordlist, array("DictWord", "cmp_obj"));

    echo "<table><tr> <td>Dictionary Word</td> <td>Levenshtein Distance</td> 
            <td>Occurances in Text</td> <td>Calculated Score</td> </tr>\n";
    for ($i = 0; $i < 10; $i++) {
        echo $wordlist[$i];
    }
    echo "</table>\n\n"; 
}
?>
</body>
</html>
