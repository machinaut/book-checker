<?php

function deletion($words){
  foreach($words as $word){
    for($x=0;$x<strlen($word);$x++){
      $results[] = substr($word, 0, $x) . substr($word, $x+1, strlen($word));
    }
  }
  return $results;
}

function transposition($words){
  foreach($words as $word){
    for($x=0;$x<strlen($word)-1;$x++){
      $results[] = substr($word, 0, $x) . $word[$x+1] . $word[$x] . substr($word, $x+2, strlen($word));
    }
  }
  return $results;
}

function alteration($words){
  $alphabet = "abcdefghijklmnopqrstuvwxyz";
  foreach($words as $word){
    for($c=0;$c<strlen($alphabet);$c++){
      for($x=0;$x<strlen($word);$x++){
        $results[] = substr($word, 0, $x) . $alphabet[$c] . substr($word, $x+1, strlen($word));
      }
    }
  }
  return $results;
}

function insertion($words){
  $alphabet = "abcdefghijklmnopqrstuvwxyz";
  foreach($words as $word){
    for($c=0;$c<strlen($alphabet);$c++){
      for($x=0;$x<strlen($word)+1;$x++){
        $results[] = substr($word, 0, $x) . $alphabet[$c] . substr($word, $x, strlen($word));
      }
    }
  }
  return $results;
}

function train($features){
  foreach($features as $feature){
    @$model[$feature] += 1;
  }
  return $model;
}

function words($text){
  $matches = preg_match_all("/[a-z]+/", strtolower($text), $output);
  return $output[0];
}

function read($file){
  $data = "";
  $fp = fopen($file, "r");
  while(!feof($fp)){
    $data .= fread($fp, 8192);
  }
  fclose($fp);
  return $data;
}

function edits1($words){
  return @array_merge(deletion($words), transposition($words), alteration($words), insertion($words));
}

function known($word){
  $file = "big.txt";
  @$nwords = train(words(read($file)));
  if(array_key_exists($word, $nwords)){
    return $word;
  }
}

function known_edits2($word){
  $file = "big.txt";
  @$nwords = train(words(read($file)));
  if(sizeof($word)<2){
    $word = array($word);
  }
  $variations = edits1($word);
  while($variation = current($variations)){
    if(array_key_exists($variation, $nwords)){
      return $variation;
      exit();
    }
    else{
      $subvariations = edits1(array($variation));
      while($subvariation = current($subvariations)){
        if(array_key_exists($subvariation, $nwords)){
          return $subvariation;
          exit();
        }
        next($subvariations);
      }
    }
    next($variations);
  }
}

function correct($word){
  if(known($word)){
    return $word;
  }
  else if(known(edits1($word))){
    return known(edits1($word));
  }
  else if(known_edits2($word)){
    return known_edits2($word);
  }
}

echo(correct("octabr"));

?>