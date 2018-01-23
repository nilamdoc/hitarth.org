<?php
for ($i = 0; $i <=15; $i++){
 for ($j = 0; $j <=15; $j++){
  for ($k = 0; $k <=15; $k++){
   for ($l = 0; $l <=15; $l++){
    print_r("mkdir -p ".dechex($i)."/".dechex($j)."/".dechex($k)."/".dechex($l)."\n");
   }
  }
 }
}