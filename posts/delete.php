<?php
include "../db.php";

if(!isset($_SESSION['user_id'])){ // 로그인 안 된 경우
    header("Location: ../auth/login.php"); // 로그인 페이지로 강제 이동
    exit;
}

if(!verify_csrf_token($_POST['csrf_token']??'')){ // csrf 토큰 검증
    die("잘못된 요청입니다.");
}

$id=$_POST['id']; // URL에서 글 번호 받음
$board_id=$_POST['board_id'] ?? 1; // board_id 받음

// 글 작성자 확인
$stmt=mysqli_prepare($conn,"SELECT * FROM posts WHERE id=?");
mysqli_stmt_bind_param($stmt,"i",$id);
mysqli_stmt_execute($stmt);
$result=mysqli_stmt_get_result($stmt);
$row=mysqli_fetch_assoc($result);

// 글 작성자가 아닌 경우 상세 페이지로 이동
if($row['author_id']!=$_SESSION['user_id']){
    header("Location:detail.php?id=$id&board_id=$board_id");
    exit;
}


//  댓글 삭제
$stmt = mysqli_prepare($conn, "DELETE FROM comments WHERE post_id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

// 첨부파일 삭제
$stmt = mysqli_prepare($conn, "DELETE FROM attachments WHERE post_id=?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

// 게시글 삭제
$stmt = mysqli_prepare($conn, "DELETE FROM posts WHERE id=?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

header("Location:list.php?board_id=$board_id");
exit;
?>

