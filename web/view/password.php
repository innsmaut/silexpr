<!DOCTYPE html>
<html>
<head>
    <title>Insert links</title>
</head>
<body>
<h4>You are trying to access link behind placeholder <?= $item['claimedLink'];?></h4>
    <form method="post">
        Type yor password:<br>
        <input type="text" name="password_acc" value="">
        <br><br>
        <input type="submit" value="Submit">
    </form>
</body>
</html>
<?php return ''; // otherwise including this file will return '1' for success into main page
?>