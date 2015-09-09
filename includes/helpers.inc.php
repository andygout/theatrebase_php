<?php
function html($text) {return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');}

function cln($text) {return mysqli_real_escape_string($GLOBALS['link'], $text);}

function errorfield($name) {global $errors; return $errors[$name] ? 'errorfield' : '';}

function error_for($name) {global $errors; if(!empty($errors[$name])) {return '<span class="error">'.$errors[$name].'</span>';}}

function generateurl($s)
{
  //Convert accented characters, and remove parentheses and apostrophes.
  $from=explode(',', "Ç,ç,Æ,æ,Œ,œ,Ð,ð,Á,á,É,é,Í,í,Ó,ó,Ú,ú,Ý,ý,À,à,È,è,Ì,ì,Ò,ò,Ù,ù,Ã,ã,Ñ,ñ,Õ,õ,Ä,ä,Ë,ë,Ï,ï,Ö,ö,Ü,ü,Ÿ,ÿ,Â,â,Ê,ê,Î,î,Ô,ô,Û,û,Ā,ā,Ē,ē,Ī,ī,Ō,ō,Ū,ū,Ȳ,ȳ,Ǣ,ǣ,Ḡ,ḡ,Ǟ,ǟ,Ȫ,ȫ,Ǖ,ǖ,Ṻ,ṻ,Ǡ,ǡ,Ȱ,ȱ,Ḹ,ḹ,Ṝ,ṝ,Ǭ,ǭ,Ȭ,ȭ,Ḗ,ḗ,Ṓ,ṓ,Ḕ,ḕ,Ṑ,ṑ,Ӣ,ӣ,Ӯ,ӯ,Ᾱ,ᾱ,Ῑ,ῑ,Ῡ,ῡ,Å,å,Ø,ø,ß,¿,¡,&,[,]");
  $to=explode(',', 'c,c,ae,ae,oe,oe,d,d,a,a,e,e,i,i,o,o,u,u,y,y,a,a,e,e,i,i,o,o,u,u,a,a,n,n,o,o,a,a,e,e,i,i,o,o,u,u,y,y,a,a,e,e,i,i,o,o,u,u,a,a,e,e,i,i,o,o,u,u,y,y,ae,ae,g,g,a,a,o,o,u,u,u,u,a,a,o,o,l,l,r,r,o,o,o,o,e,e,o,o,e,e,o,o,n,n,y,y,a,a,i,i,y,u,a,a,o,o,ss,?,!,and,,');
  //Do the replacements, and convert all other non-alphanumeric characters to spaces.
  $s=preg_replace('/[^()\w\d]+/', '-', str_replace($from, $to, trim($s)));
  //Correct conversions for words ending with apostrophes (i.e. 'duke-of-york-s-theatre' becomes 'duke-of-yorks-theatre').
  $s=preg_replace('/-(d-)|-(d)$|-(ll-)|-(ll)$|-(m-)|-(m)$|-(re-)|-(re)$|-(s-)|-(s)$|-(t-)|-(t)$|-(ve-)|-(ve)$/', '$1$2$3$4$5$6$7$8$9$10$11$12$13$14', $s);
  //Remove any hyphens (-) at the beginning or end of string and make lowercase.
  return strtolower(preg_replace ('/(^-|-$)/', '', $s));

  //Remove 'the', 'a' and 'an' from URL by replacing last line of function code with the following (/i=regular expression modifier that makes regex match case insensitive.):-
  //return strtolower (preg_replace ('/(^-|-$)/', '', preg_replace ('/\b(^the|^a|^an)\b/i', '', $s)));
}

function romannumeral($num)
{
  $n=intval($num);
    $res='';
    $roman_numerals=array( // roman_numerals array
        'M' =>1000, 'CM'=>900, 'D' =>500, 'CD'=>400, 'C' =>100, 'XC'=>90, 'L' =>50, 'XL'=>40, 'X' =>10, 'IX'=>9, 'V' =>5, 'IV'=>4, 'I' =>1);

    foreach($roman_numerals as $roman=>$number) // Divide to get  matches
  {
        $matches=intval($n / $number);
        $res .= str_repeat($roman, $matches); // Assign the roman char * $matches
        $n=$n % $number;  // Substract from the number
    }
    return $res; // Return the res
}

function alph($a)
{
  if(preg_match('/^(A |An |The |\W+)(\S+.*)$/i', $a)) {$alph=preg_replace('/^(A |An |The |\W+)(\S+.*)$/i', '$2', $a);}
  else {$alph=NULL;}
  return $alph;
}
?>