<?php 
include "../db.php";

if(!isset($_SESSION['user_id'])){ // 로그인 안 된 경우
    header("Location: ../auth/login.php"); // 로그인 페이지로 강제 이동
    exit;
}

$board_id=$_GET['board_id'] ?? 1; // url로 board_id를 받음

if($_SERVER['REQUEST_METHOD']=='POST'){

    if(!verify_csrf_token($_POST['csrf_token'] ?? '')){ // 토큰이 없거나 일치하지 않음ㄴ
        die("잘못된 요청입니다.");
    }

    // POST 요청으로 데이터가 넘어온 경우
    $title=$_POST['title'];
    $content=$_POST['content'];
    $author_id=$_SESSION['user_id'];
    $board_id=$_POST['board_id'];


    $stmt=mysqli_prepare($conn,"INSERT INTO posts (title, content, author_id, board_id) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt,"ssii",$title,$content,$author_id,$board_id);
    mysqli_stmt_execute($stmt); // 쿼리를 DB에서 실행

    $post_id=mysqli_insert_id($conn); // 방금 insert된 글의 id를 가져옴

    if($_FILES['upload_file']['error']==0){ // 업로드된 파일이 에러 없이 정상 업로드됐을 때
        
        if(!validate_upload_file($_FILES['upload_file'])){ // 검증 실패 시 업로드 X
            die("허용되지 않는 파일 형식입니다.");
        }
        $original_name=$_FILES['upload_file']['name']; // 사용자가 올린 파일의 원본 이름
        $ext=strtolower(pathinfo($original_name,PATHINFO_EXTENSION)); // 저장 파일명에 쓸 확장자 추출

        $stored_name=uniqid().'.'.$ext; // 시간 기반 고유 id 생성 후 파일명+확장자 재생성
        $stored_path='/var/www/html/uploads/'.$stored_name; // 서버에 저장될 실제 경로
        $size=$_FILES['upload_file']['size']; // 파일 크기

        // 실제 저장 경로로 파일 이동
        move_uploaded_file($_FILES['upload_file']['tmp_name'], $stored_path); // php는 업로드된 파일을 tmp_name에 임시저장

        $stmt=mysqli_prepare($conn,"INSERT INTO attachments (post_id, original_name, stored_path, size_bytes) VALUES (?,?,?,?)");
        mysqli_stmt_bind_param($stmt,"issi",$post_id,$original_name,$stored_path,$size);
        mysqli_stmt_execute($stmt);

    }

    header("Location:list.php?board_id=$board_id"); // 글 작성 후 목록 페이지로 이동 + 해당 baord_id로
    exit(); // 목록 페이지로 이동한 후의 코드 실행을 막음
}


?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>글쓰기</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="container">
        <h1>글쓰기</h1>
        <form method="POST" action="write.php?board_id=<?php echo $board_id;?>" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

            <!--post로 board_id 추가 전달-->
            <input type="hidden" name="board_id" value="<?php echo $board_id;?>">
            <div class="form-group">
                <label>제목</label>
                <input type="text" name="title">
            </div>
            <div class="form-group">
                <label>글 내용</label>
                <textarea name="content"></textarea>
            </div>
            <div class="form-group">
                <label>첨부파일</label>
                <input type="file" name="upload_file">
            </div>
            <div class="form-actions">
                <button type="submit">등록</button>
                <a href="list.php?board_id=<?php echo $board_id;?>" class="btn btn-secondary">취소</a>
            </div>
        </form>
    </div>
</body>
</html>