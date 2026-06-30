<?php 
include "../db.php";

if(!isset($_SESSION['user_id'])){ // 로그인 안 된 경우
    header("Location: ../auth/login.php"); // 로그인 페이지로 강제 이동
    exit;
}

$id=$_GET['id']; // GET URL에서 id 받기
$board_id=$_GET['board_id'] ?? 1;

// 작성자 권한 검증
$stmt=mysqli_prepare($conn,"SELECT * FROM posts WHERE id=?"); // 글 id 찾기
mysqli_stmt_bind_param($stmt,"i",$id);
mysqli_stmt_execute($stmt);
$result=mysqli_stmt_get_result($stmt);
$row=mysqli_fetch_assoc($result);

if(!$row || $row['author_id']!=$_SESSION['user_id']){ // 글이 존재 X || 작성자 아이디가 로그인한 유저가 아닌 경우
    header("Location:detail.php?id=$id&board_id=$board_id"); // 상세페이지로
    exit;

}



if($_SERVER['REQUEST_METHOD']=='POST'){

    if(!verify_csrf_token($_POST['csrf_token'] ?? '')){
        die("잘못된 요청입니다.");
    }

    $title=$_POST['title'];
    $content=$_POST['content'];
    $board_id=$_POST['board_id']; // POST로도 받음

    $stmt=mysqli_prepare($conn,"UPDATE posts SET title=?, content=? WHERE id=?");
    mysqli_stmt_bind_param($stmt,"ssi",$title,$content,$id);
    mysqli_stmt_execute($stmt); // 실행


    // 새 파일이 첨부된 경우
    if(isset($_FILES['upload_file']) && $_FILES['upload_file']['error']==0){

        if(!validate_upload_file($_FILES['upload_file'])){
            die("허용되지 않는 파일 형식입니다.");
        }

        // 기존 첨부파일 조회 및 삭제
        $stmt=mysqli_prepare($conn,"SELECT * FROM attachments WHERE post_id=?");
        mysqli_stmt_bind_param($stmt,"i",$id);
        mysqli_stmt_execute($stmt);
        $old_result=mysqli_stmt_get_result($stmt);

        // 기존 첨부파일 정보를 가져옴
        while($old_file=mysqli_fetch_assoc($old_result)){
            unlink($old_file['stored_path']); // 서버에서 기존 파일 삭제
        }
        // DB에서 파일 정보 삭제
        $stmt=mysqli_prepare($conn,"DELETE FROM attachments WHERE post_id=?");
        mysqli_stmt_bind_param($stmt,"i",$id);
        mysqli_stmt_execute($stmt);

        // 새 파일 저장
        $original_name=$_FILES['upload_file']['name']; // 새 파일 원본명
        $ext=strtolower(pathinfo($original_name,PATHINFO_EXTENSION)); // 확장자 추출 후 소문자 변환
        $stored_name=uniqid().'.'.$ext; // 파일 이름 재생성
        $stored_path='/var/www/html/uploads/'.$stored_name; // 저장 경로
        $size=$_FILES['upload_file']['size']; // 파일 크기

        move_uploaded_file($_FILES['upload_file']['tmp_name'], $stored_path);
        
        // 새 파일 정보를 DB에 저장
        $stmt=mysqli_prepare($conn,"INSERT INTO attachments (post_id, original_name, stored_path, size_bytes) VALUES (?,?,?,?)");
        mysqli_stmt_bind_param($stmt,"issi",$id,$original_name,$stored_path,$size);
        mysqli_stmt_execute($stmt);
        
    }

    header("Location: detail.php?id=$id&board_id=$board_id"); // 수정 끝나면 상세 페이지로
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>글 수정</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="container">
        <h1>글 수정</h1>
        <form method="POST" action="edit.php?id=<?php echo $id; ?>&board_id=<?php echo $board_id;?>" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

            <input type="hidden" name="board_id" value="<?php echo $board_id;?>">
            <div class="form-group">
                <label>제목</label>
                <input type="text" name="title" value="<?php echo htmlspecialchars($row['title']); ?>">
            </div>
            <div class="form-group">
                <label>첨부파일 변경</label>
                <input type="file" name="upload_file">
            </div>
            <div class="form-group">
                <label>글 내용</label>
                <textarea name="content"><?php echo htmlspecialchars($row['content']); ?></textarea>
            </div>
            <div class="form-actions">
                <button type="submit">수정 완료</button>
                <a href="detail.php?id=<?php echo $id; ?>&board_id=<?php echo $board_id;?>" class="btn btn-secondary">취소</a>
            </div>
        </form>
    </div>
</body>
</html>