<?php
 
function DomNextTag($e, $tagName) {
    while ($e = $e->nextSibling) {
        if (isset($e->tagName) && ($e->tagName == $tagName)) return $e;
    }
    return false;
}
 
function DomFirstTag($e, $tagName) {
    if ($e = $e->firstChild) do {
        if (isset($e->tagName) && ($e->tagName == $tagName)) return $e;
    } while ($e = $e->nextSibling);
    return false;
}
 
function processRows($section) {
    if ($tr = DomFirstTag($section, 'tr')) {
        $out = fopen('php://output', 'w');
        do {
            $result = [];
            if ($td = DomFirstTag($tr, 'td')) do {
                $result[] = trim($td->textContent);
            } while ($td = DomNextTag($td, 'td'));
            fputcsv($out, $result);
        } while ($tr = DomNextTag($tr, 'tr'));
        fclose($out);
    }
}

function process_data($value, $rank) {

    libxml_use_internal_errors(true); // suppress warnings since HTML is shit
 
    $doc = new DOMDocument();
    $page_name = "http://www.stallions.com.au/salesresults/sire_results.php?sire_name%5B%5D=". $value. "+%28AUS%29";
    $doc->loadHTMLFile($page_name, LIBXML_NOWARNING);

    $value = $value . ".csv";
    
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' . $value);
    header('Content-Transfer-Encoding: binary');
    header('Connection: Keep-Alive');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
     
    $table = $doc->getElementById('derby');
    processRows(DomFirstTag($table, 'thead'));
    processRows(DomFirstTag($table, 'tbody'));
}
 


$name = $argv[1];
$rank = $argv[2];
// Replace the space with + and the ' with %27
// Eg. Redoute's Choice -> Redoute%27s+Choice
$name = str_replace(" ", "+", $name);
$name = str_replace("'", "%27", $name);
// Get rid of the lashes
$name = stripslashes($name);
process_data($name, $rank);
     
?>
