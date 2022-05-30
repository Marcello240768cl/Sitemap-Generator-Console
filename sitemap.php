<?php
# array $urls containing link  of pages
    $urls = array(100 );
#Parameter of ricorsione $n
$n;
$str_finale="";$str_dump="";//Variable string of output of function  dump_url

#function of output printing of file xml
    function urlElement($url) {
        $aptr= '<url>'.PHP_EOL. 
         '<loc>'.$url.'</loc>'. PHP_EOL. 
        '<lastmod>'.date('d-m-Y').'</lastmod>'.PHP_EOL. 
         '</url>'.PHP_EOL;
return $aptr;
    }
    


#url site from post form of file input.html
$url =$_REQUEST['url_site'];


$html=file_get_contents($url);
# Create a DOM parser object
$dom = new DOMDocument();

# Parse the HTML from $_REQUEST['url_site'];
# The @ before the method call suppresses any warnings that
# loadHTML might throw because of invalid HTML in the page.
@$dom->loadHTML($html);
#Initializing array $urls
$urls[0]=$url;

$n=1;

 $dom = new DOMDocument();
#Content of html page of $url variable
$html=file_get_contents($url);
#Initialing output string of dump_url function
$str_dump="";
#recoursive function with 2 base parameter  conditions
function dump_url(&$urls,$n,$str_dump){ 
if($n==0){
$html=file_get_contents($urls[0]);
$dom = new DOMDocument();

$html=file_get_contents($urls[0]);

@$dom->loadHTML($html); }
   
else if($n==1){
foreach($dom->getElementsByTagName('a') as $link) {

      $urls[$n]=$link->getAttribute('href');
$str_dump.= urlElement($link->getAttribute('href'));
}
}
 else $str_dump.= dump_url($urls,$n+1,$str_dump);
return $str_dump;

}

  




#The sitemap file xml will named as the name of site without suffix http or https
$file_entry=basename($url).".xml";
$str_dump=dump_url($urls,count($urls)-1,$str_dump);//echo $str_dump;
$str_finale= '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL; 
  $str_finale.= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.PHP_EOL ; 
$str_dump=$str_finale.$str_dump;
$str_finale =$str_dump.'</urlset>';//echo $str_finale;
#put html content into $url file xml from folder 
$fp=file_put_contents($file_entry,$str_finale);



header("Content-Type: application/force-download; name=".$file_entry."");
header("Content-type: text/xml"); 
header("Content-Transfer-Encoding: binary");
header("Content-Disposition: attachment; filename=".$file_entry."");
header("Expires: 0");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
readfile($file_entry);

         
         
#Destroy file after downloading it
unlink($file_entry);

#Exit page 
exit();

?>



