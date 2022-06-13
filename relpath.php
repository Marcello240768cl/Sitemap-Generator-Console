 
<?php
function rel2abs($rel, $base) {
		if(strpos($rel,"//") === 0) {
			return "http:".$rel;
		}
		/* return if  already absolute URL */
		if  (parse_url($rel, PHP_URL_SCHEME) != '') return $rel;
		$first_char = substr ($rel, 0, 1);
		/* queries and  anchors */
		if ($first_char == '#'  || $first_char == '?') return $base.$rel;
		/* parse base URL  and convert to local variables:
		$scheme, $host,  $path */
		extract(parse_url($base));
		/* remove  non-directory element from path */
		$path = preg_replace('#/[^/]*$#',  '', $path);
		/* destroy path if  relative url points to root */
		if ($first_char ==  '/') $path = '';
		/* dirty absolute  URL */
		$abs =  "$host$path/$rel";
		/* replace '//' or  '/./' or '/foo/../' with '/' */
		$re =  array('#(/.?/)#', '#/(?!..)[^/]+/../#');
		for($n=1; $n>0;  $abs=preg_replace($re, '/', $abs, -1, $n)) {}
		/* absolute URL is  ready! */
		return  $scheme.'://'.$abs;
	}
?>