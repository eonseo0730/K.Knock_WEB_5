<?php 
include "../db.php";

session_start(); // 세션 시작
if(!isset($_SESSION['user_id'])){ // 로그인 안 된 경우
    header("Location: ../auth/login.php"); // 로그인 페이지로 강제 이동
    exit;
}

if($_SERVER['REQUEST_METHOD']=='POST'){
    // POST 요청으로 데이터가 넘어온 경우
    $title=$_POST['title'];
    $content=$_POST['content'];
    $author_id=$_SESSION['user_id'];

    $sql="INSERT INTO posts (title, content, author_id) VALUES ('$title', '$content', '$author_id')";
    mysqli_query($conn, $sql); // conn에 실행할 sql 적용

    header("Location:list.php"); // 글 작성 후 목록 페이지로 이동
    exit(); // 목록 페이지로 이동한 후의 코드 실행을 막음
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>글쓰기</title>
</head>
<body>
    <h1>글쓰기</h1>
    <form method="POST" action="write.php"> <!--POST로 write.php에 데이터 전송-->

    <div>
        <label>제목</label>
        <input type="text" name="title"> <!--name:$_POST['title']로 받음-->
    </div>
    <div>
        <label>글 내용</label>
        <textarea name="content"></textarea>
    </div>
    <button type="submit">등록</button>
    </form>
</body>
</html>