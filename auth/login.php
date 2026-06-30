<?php
include "../db.php";

if($_SERVER['REQUEST_METHOD']=='POST'){

    if(!verify_csrf_token($_POST['csrf_token'] ?? '')){ // 토큰이 없거나 일치하지 않는 경우
        die("잘못된 요청입니다."); // csrf 토큰 불일치할 경우
    }

    $username=$_POST['username'];
    $password=$_POST['password'];    

    $stmt=mysqli_prepare($conn, "SELECT * FROM users WHERE username=?"); // ? 자리에 값을 나중에 바인딩
    mysqli_stmt_bind_param($stmt,"s", $username); // $stmt의 ?에 $username이 string의 형태로 들어감
    mysqli_stmt_execute($stmt); // 값이 바인딩 된 후 실제 쿼리를 DB에서 실행
    $result=mysqli_stmt_get_result($stmt); // execute()로 실행된 결과를 fetch 가능한 결과셋 객체로 바꿔줌
    $user=mysqli_fetch_assoc($result); // 결과 셋에서 한 줄 꺼냄
   

    if($user && password_verify($password, $user['password'])){ // 유저가 존재 && 비밀번호 해시해서 일치하는지 비교
        session_regenerate_id(true); // 기존 세션 폐기 후 새 세션 ID 발급
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
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="auth-container">
        <h1>로그인</h1>
        <?php if(isset($error)) echo "<p class='error-msg'>$error</p>"; ?>
        <form method="POST" action="login.php">
            
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <div class="form-group">
                <label>아이디</label>
                <input type="text" name="username">
            </div>
            <div class="form-group">
                <label>비밀번호</label>
                <input type="password" name="password">
            </div>
            <button type="submit">로그인</button>
        </form>
        <div class="auth-footer">
            <a href="register.php">회원가입</a>
        </div>
    </div>
</body>
</html>