<?php 
if (!isset($_GET["word"]) || empty($_GET["word"])) {
    // If no word to compare, do nothing
    echo "pick a word";
} else {
    // This is the word to look up in the dictionary
    $word = $_GET["word"];

    // grab the dictionary, stored in parsed JSON
    $json = file_get_contents("dict.json"); 

    // iterate over every word in the dictionary
    $jsonIterator = new RecursiveIteratorIterator(
            new RecursiveArrayIterator(json_decode($json, TRUE)),
            RecursiveIteratorIterator::SELF_FIRST);

    // class to store word and associated data
    class DictWord {
        var $word;
        var $score;
        var $lsval;
        var $stval;
        var $occur;
        // constructor
        function DictWord ($dictword,$occur) {
            $this->word = $dictword;
            $this->lsval = levenshtein($word,$dictword); // levenstein distance
            $this->stval = similar_text($word,$dictword); // similarity score
            $this->occur = $occur; // occurences of word in moby dick
            $this->score = $this->occur;
        }
        // static comparing, used for sorting an array of DictWords,
        // Note: it is reversed, so the larger strings will be sorted first
        static function cmp_obj ( $a, $b ) {
            if ($a->score == $b->score) { 
                return 0; 
            }
            return ($a->score < $b->score) ? +1 : -1;
        }
        // prettyprinting. Kind of evil there is HTML formatting in here
        public function __toString() {
            return "<tr>
                <td>$this->word</td>
                <td>$this->lsval</td>
                <td>$this->stval</td>
                <td>$this->occur</td>
                <td>$this->score</td>
                </tr>\n";
        }
    }

    // We know the dictionary is a shallow JSON object, so this is always "str"=>"str"
    foreach ($jsonIterator as $key => $val) { 
        $wordlist[] = new Dictword($key,$val);
    } 

    usort($wordlist, array("DictWord", "cmp_obj"));

    echo $word;
    echo levenshtein($word,"boo");

    echo "<table><tr> <td>word</td> <td>lsval</td> <td>stval</td>
            <td>occur</td> <td>score</td> </tr>\n";
    for ($i = 0; $i < 10; $i++) {
        echo $wordlist[$i];
    }
    echo "</table>\n\n"; 
}
?>
