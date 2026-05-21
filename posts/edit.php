<?php 
include "../db.php";

session_start(); // 세션 시작
if(!isset($_SESSION['user_id'])){ // 로그인 안 된 경우
    header("Location: ../auth/login.php"); // 로그인 페이지로 강제 이동
    exit;
}

$id=$_GET['id']; // GET URL에서 id 받기
if($_SERVER['REQUEST_METHOD']=='POST'){
    $title=$_POST['title'];
    $content=$_POST['content'];

    // 수정한 내용 db에 반영
    $sql="UPDATE posts SET title='$title', content='$content' WHERE id=$id";

    mysqli_query($conn,$sql);

    header("Location: detail.php?id=$id"); // 수정 끝나면 상세 페이지로
    exit;
}

// 이전 글 불러오기
$result=mysqli_query($conn, "SELECT * FROM posts WHERE id=$id");
$row =mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>글 수정</title>
</head>
<body>
    <h1>글 수정</h1>
    <form method="post" action="edit.php?id=<?php echo $id;?>"> <!--edit.php에 post로 전달-->
        <div>
            <label>제목</label>
            <input type="text" name="title" value="<?php echo $row['title']?>"> <!--기존의 제목 채움-->
        </div>
        <div>
            <label>글 내용</label>
            <textarea name="content"><?php echo $row['content']; ?></textarea> <!--기존 내용으로 채움-->
        </div>
        <button type="submit">수정 완료</button>

    </form>
    <a href="detail.php?id=<?php echo $id;?>">취소</a>
</body>
</html>