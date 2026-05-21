<?php
include "../db.php";
if($_SERVER['REQUEST_METHOD']=='POST'){
    $username=$_POST['username']; // 입력한 아이디 받음
    $password=$_POST['password'];

    $sql="INSERT INTO users (username, password) VALUES ('$username', '$password')";
    mysqli_query($conn, $sql);
    header("Location:login.php"); // 회원가입 후 로그인 페이지로 이동
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>회원가입</title>
</head>
<body>
    <h1>회원가입</h1>
    <form method="POST" action="register.php">
        <div>
            <label>아이디</label>
            <input type="text" name="username"> <!-- -> $_POST['username']으로 받게 됨-->
        </div>
        <div>
            <label>비밀번호</label>
            <input type="password" name="password">
        </div>
        <button type="submit">회원가입</button>
    </form>
    <a href="login.php">로그인</a>
</body>
</html>