<?php
include "../db.php";

session_start(); // 세션 시작
if(!isset($_SESSION['user_id'])){ // 로그인 안 된 경우
    header("Location: ../auth/login.php"); // 로그인 페이지로 강제 이동
    exit;
}

$comment_id=$_GET['id']; // URL에서 댓글 번호 받음
$post_id=$_GET['post_id']; // 수정 후 돌아갈 게시글 번호

//POST 요청으로 데이터가 넘어온 경우
if($_SERVER['REQUEST_METHOD']=='POST'){
    $content=$_POST['content']; // 수정할 댓글 내용 받기

    $sql="UPDATE comments SET content='$content' WHERE id=$comment_id";
    mysqli_query($conn, $sql);

    // 수정 완료 후 해당 게시글 상세 페이지로 이동
    header("Location:../posts/detail.php?id=$post_id");

    exit;
}

// 기존 댓글 데이터 가져옴
$result=mysqli_query($conn, "SELECT * FROM comments WHERE id=$comment_id");
$row=mysqli_fetch_assoc($result); // 결과에서 한 줄 꺼냄
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>댓글 수정</title>
</head>
<body>
    <h1>댓글 수정</h1>
    <form method="POST" action="edit.php?id=<?php echo $comment_id;?>&post_id=<?php echo $post_id?>">
        <!--기존의 댓글 내용으로 채움-->
        <textarea name="content"><?php echo $row['content'];?></textarea>

        <button type="submit">수정 완료</button>
    </form>
    <!--취소 시 상세페이지로-->
    <a href="../posts/detail.php?id=<?php echo $post_id;?>">취소</a>

    
</body>
</html>