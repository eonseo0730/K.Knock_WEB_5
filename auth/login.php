<?php
include "../db.php";
session_start(); // 세션 시작 (로그인 상태를 서버에 저장)

if($_SERVER['REQUEST_METHOD']=='POST'){
    $username=$_POST['username'];
    $password=$_POST['password'];

    // 입력한 id가 db에 있는지 확인
    $result=mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
    $user=mysqli_fetch_assoc($result); // 헤당 유저 데이터 한 줄 꺼냄

    if($user && $user['password']==$password){ // 유저가 존재 && 비밀번호 일치
        $_SESSION['user_id']=$user['id']; // 세션에 유저 id 저장
        $_SESSION['username']=$user['username']; // 세션에 유저 아이디 저장
        header("Location: ../posts/list.php"); // 로그인 성공 시 게시판으로 이동

        exit;
    }else{
        $error="아이디 또는 비밀번호가 일치하지 않습니다."; // 로그인 실패
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>로그인</title>
</head>
<body>
    <h1>로그인</h1>
    <?php if(isset($error)) echo "<p>$error</p>"?> <!-- 변수가 존재하는지 확인하고 로그인 실패 시 에러 메시지 출력-->
    
    <form method="POST" action="login.php">
        <div>
            <label>아이디</label>
            <input type="text" name="username">
        </div>
        <div>
            <label>비밀번호</label>
            <input type="password" name="password">
        </div>
        <button type="submit">로그인</button>
    </form>
    <a href="register.php">회원가입</a>
</body>
</html>