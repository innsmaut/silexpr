<!DOCTYPE html>
<html>
<head>
    <title>Insert links</title>
</head>
<body>
<?php if (isset($redirectLink)){
    $lnk = 'http://'.$_SERVER['SERVER_NAME'].strstr($_SERVER['SCRIPT_NAME'], 'index.php', true).$redirectLink;
    $lnk = '<a href='.$lnk.'>'.$lnk.'</a>';
    echo "New link to {$_POST['claimed_link']} is available at {$lnk} in next {$_POST['expired_on']} min.";
} else { ?>
<form method="post">
    Type your link:<br>
    <input type="text" name="claimed_link" value="">
    <br>
    Active time(minutes):<br>
    <input type="text" name="expired_on" value="">
    <br>
    Type yor password:<br>
    <input type="text" name="password" value="">
    <br><br>
    <input type="submit" value="Submit">
</form>
<?php };?>
</body>
</html>
<?php return ''; // otherwise including this file will return '1' for success into main page
?>