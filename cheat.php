<!DOCTYPE html>
<html>
<head><title>LETS SEE IF I CAN BEAT PHP's SPELLCHEKCER</title></head>
<body>
<?php 
echo "<p> Hello World </p>";
if (!isset($_GET["word"]) || empty($_GET["word"])) {
    // If no word to compare, do nothing
    echo "<p>Please pick a word.</p>";
} else {
    // Word to check
    $word = $_GET["word"];
    echo "<p>Suggesting for: $word.</p>";
    // Open the Dictionary
    $pspell = pspell_new("en","","","utf-8",PSPELL_BAD_SPELLERS);
    // Get Word Suggestions
    $suggestions = pspell_suggest($pspell, $word);
    // Print it out as an HTML table to make it easy to look at
    echo "<p>Suggested Word</p><ul>\n";
    foreach ($suggestions as $suggestion) {
        echo "<li>$suggestion</li>\n";
    }
    echo "</ul>\n"; 
}
?>
</body>
</html>
