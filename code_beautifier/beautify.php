<?php
require_once("beautifyFunctions.inc.php");
$indent=4;

// get the php code from file or input field
if ($HTTP_POST_VARS['submit']=='Upload'){
    if ($HTTP_POST_FILES['filename']['tmp_name'] == "none") {
        die("<p>No input file specified. Please go <a href=\"beautify.html\">back</a> and submit some PHP code you want to have beautified.</p>");
    } else if (!strstr($HTTP_POST_FILES['filename']['type'], "text/plain") && !strstr($HTTP_POST_FILES['filename']['type'], "text/html") && !stristr($HTTP_POST_FILES['filename']['type'], "application/octet-stream")) {
	  die("<p>Please submit the file formatted as plain text.</p>");
    } else if ($HTTP_POST_FILES['filename']['size'] >= 30000) {
	  die("<p>Sorry, file size currently is limited to 30 kb to minimize server load.</p>");
    } else {
        $strarray=file($HTTP_POST_FILES['filename']['tmp_name']);
    }

} else if ($HTTP_POST_VARS['submit']=='Submit'){
    if (!$HTTP_POST_VARS['code']){
        die("<p>No input received. Please go <a href=\"index.html\">back</a> and submit some PHP code you want to have beautified.</p>");
    } else {
        if (get_magic_quotes_gpc()){
            $HTTP_POST_VARS['code']=stripslashes($HTTP_POST_VARS['code']);
        }
        $strarray=explode("\n", $HTTP_POST_VARS['code']);
    }
} else {
    die ("<p>Please go to <a href=\"index.html\">the submission form</a> and submit some PHP code you want to have beautified.</p>");
}

// trim each line and concatenate to one string
for($i=0;$i<count($strarray);$i++){
    $strarray[$i]=trim($strarray[$i]);
}
$str=implode("\n", $strarray);
$str=substr($str, 0, 100000);

//process code
$str=publicProcessHandler($str, $indent);

// output
echo "<html>\n";
echo "<body>\n";
echo "<pre>\n";
echo htmlentities(($str));
echo "\n</pre>";
echo "</body>\n";
echo "</html>\n";
?>