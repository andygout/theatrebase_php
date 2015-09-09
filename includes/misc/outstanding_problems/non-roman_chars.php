<?php
function generateurl($s)
{
  //Convert accented characters, and remove parentheses and apostrophes.
  $from=explode(',', "Ç,ç,Æ,æ,Œ,œ,Ð,ð,Á,á,É,é,Í,í,Ó,ó,Ú,ú,Ý,ý,À,à,È,è,Ì,ì,Ò,ò,Ù,ù,Ã,ã,Ñ,ñ,Õ,õ,Ä,ä,Ë,ë,Ï,ï,Ö,ö,Ü,ü,Ÿ,ÿ,Â,â,Ê,ê,Î,î,Ô,ô,Û,û,Ā,ā,Ē,ē,Ī,ī,Ō,ō,Ū,ū,Ȳ,ȳ,Ǣ,ǣ,Ḡ,ḡ,Ǟ,ǟ,Ȫ,ȫ,Ǖ,ǖ,Ṻ,ṻ,Ǡ,ǡ,Ȱ,ȱ,Ḹ,ḹ,Ṝ,ṝ,Ǭ,ǭ,Ȭ,ȭ,Ḗ,ḗ,Ṓ,ṓ,Ḕ,ḕ,Ṑ,ṑ,Ӣ,ӣ,Ӯ,ӯ,Ᾱ,ᾱ,Ῑ,ῑ,Ῡ,ῡ,Å,å,Ø,ø,ß,¿,¡,&,[,]");
  $to=explode(',', 'c,c,ae,ae,oe,oe,d,d,a,a,e,e,i,i,o,o,u,u,y,y,a,a,e,e,i,i,o,o,u,u,a,a,n,n,o,o,a,a,e,e,i,i,o,o,u,u,y,y,a,a,e,e,i,i,o,o,u,u,a,a,e,e,i,i,o,o,u,u,y,y,ae,ae,g,g,a,a,o,o,u,u,u,u,a,a,o,o,l,l,r,r,o,o,o,o,e,e,o,o,e,e,o,o,n,n,y,y,a,a,i,i,y,u,a,a,o,o,ss,?,!,and,,');
  //Do the replacements, and convert all other non-alphanumeric characters to spaces.
  $s=preg_replace('/[^()\d\p{L}]+/u', '-', str_replace($from, $to, trim($s)));
  //Correct conversions for words ending with apostrophes (i.e. 'duke-of-york-s-theatre' becomes 'duke-of-yorks-theatre').
  $s=preg_replace('/-(d-)|-(d)$|-(ll-)|-(ll)$|-(m-)|-(m)$|-(re-)|-(re)$|-(s-)|-(s)$|-(t-)|-(t)$|-(ve-)|-(ve)$/', '$1$2$3$4$5$6$7$8$9$10$11$12$13$14', $s);
  //Remove any hyphens (-) at the beginning or end of string and make lowercase.
  return strtolower(preg_replace ('/(^-|-$)/', '', $s));
  //Remove 'the', 'a' and 'an' from URL by replacing last line of function code with the following (/i=regular expression modifier that makes regex match case insensitive.):-
  //return strtolower (preg_replace ('/(^-|-$)/', '', preg_replace ('/\b(^the|^a|^an)\b/i', '', $s)));
}
echo generateurl('Monkey');

//How to match Cyrillic characters with a regular expression: http://stackoverflow.com/questions/1716609/how-to-match-cyrillic-characters-with-a-regular-expression
//\p{L}

//Regular Expression for Japanese characters:  http://stackoverflow.com/questions/6787716/regular-expression-for-japanese-characters

//a href to person/役所-広司 does not work (solved in .htaccess?; needs to recognise URL stored in database which is currently '??-??')

//see below - Cyrillic (Russian) text cannot have alpha function applied (although Japanese fine...).
function potato($s) {
if(preg_match('/^(A |An |The |\W+)(\S+.*)$/i', $s)) {echo preg_replace('/^(A |An |The |\W+)(\S+.*)$/i', '$2', $s);}
else {echo $prf_prsn_rl;}
}
echo potato('Хомэро анёмал праэчынт но хёз. Эю про чтэт дэлььиката, дуо экз эчжынт луптатум');
?>