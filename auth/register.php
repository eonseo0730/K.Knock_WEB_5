<?php
include "../db.php";
if($_SERVER['REQUEST_METHOD']=='POST'){

    if(!verify_csrf_token($_POST['csrf_token'] ?? '')){ // 토큰이 없거나 일치하지 않는 경우
        die("잘못된 요청입니다.");
    }

    $username=$_POST['username']; // 입력한 아이디 받음
    $password=$_POST['password'];

    $hashed_password=password_hash($password,PASSWORD_DEFAULT); // 비밀번호 해시

    $stmt=mysqli_prepare($conn, "INSERT INTO users (username, password) VALUES (?,?)");
    mysqli_stmt_bind_param($stmt,"ss",$username,$hashed_password); // $stmt의 ? 자리에 username과 password를 차례로 string으로 넣음
    mysqli_stmt_execute($stmt); // DB에 보내서 실행
    
    header("Location:login.php"); // 회원가입 후 로그인 페이지로 이동
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>회원가입</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="auth-container">
        <h1>회원가입</h1>
        <form method="POST" action="register.php">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <div class="form-group">
                <label>아이디</label>
                <input type="text" name="username">
            </div>
            <div class="form-group">
                <label>비밀번호</label>
                <input type="password" name="password">
            </div>
            <button type="submit">회원가입</button>
        </form>
        <div class="auth-footer">
            <a href="login.php">로그인</a>
        </div>
    </div>
</body>
</html>