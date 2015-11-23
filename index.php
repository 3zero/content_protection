<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache"); ?>
<!DOCTYPE html>
<html>
<head>
<?php
$ip = $_SERVER['REMOTE_ADDR'];
$current_date = date('dmY');
$referer = $_GET['cypher'];

function encrypt_decrypt($action, $string) {
    $output = false;
    $encrypt_method = "AES-256-CBC";
    $secret_key = '$ip';
    $secret_iv = '$current_date';
    $key = hash('sha256', $secret_key);
    $iv = substr(hash('sha256', $secret_iv), 0, 16);

    if( $action == 'encrypt' ) {
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
    }
    else if( $action == 'decrypt' ){
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }

    return $output;
}

$plain_txt = "Content Protection";

$encrypted = encrypt_decrypt('encrypt', $plain_txt);

$decrypted = encrypt_decrypt('decrypt', $encrypted_txt);

if ($referer == ''){ ?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script>
$(document).ready(function(){
$("html").load("<?php echo $_SERVER['REQUEST_URI']?>?cypher=<?php echo $encrypted ?>");  
});
</script>
</head>
<?php
     $File = "$encrypted.txt"; 
     $Handle = fopen($File, 'w');
     $Data = "$ip\n".PHP_EOL;; 
     fwrite($Handle, $Data); 
     fclose($Handle); 
   } 
else if ($encrypted == $referer && file_exists("$encrypted.txt")){
?>
<title>Protected Content</title>
</head>
<body>
<p>Protected Content goes here</p>
</body>
<?php
$File = "$encrypted.txt";
unlink($File);
} else {
?>
<title>This page has expired</title>
</head><body>
<h1>This page has expired</h1>
<p>The page has expired</p>
</body></html>
<?php
}
?>
</html>

