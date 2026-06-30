<?php
include "db.php"; // db 연결

if(!isset($_SESSION['user_id'])){ // 로그인하지 않은 경우 로그인 페이지로
    header("Location:auth/login.php");
    exit;
}

$file_id=$_GET['id']; // URL에서 파일 id 받음

//attachments 테이블에서 파일 정보 가져옴
$stmt=mysqli_prepare($conn,"SELECT * FROM attachments WHERE id=?");
mysqli_stmt_bind_param($stmt,"i",$file_id);
mysqli_stmt_execute($stmt);
$result=mysqli_stmt_get_result($stmt);
$file=mysqli_fetch_assoc($result); 

if(!$file){ // 파일이 없는 경우
    die("파일을 찾을 수 없습니다.");
}

$stored_path=$file['stored_path']; // 서버에 저장된 실제 경로
$original_name=$file['original_name']; // 원본 파일명

if(!file_exists($stored_path)){ // 경로에 파일이 없는 경우
    die("파일이 존재하지 않습니다.");
}

// 브라우저에 파일 다운로드 관련 응답 전송
header('Content-Type:application/octet-stream'); // 파일을 바이너리로 전송 -> 화면에 표시하지 X
header('Content-Disposition:attachment; filename="'.$original_name.'"'); // 다운로드로 처리, 저장할 파일명 지정
header('Content-Length: '.filesize($stored_path)); // 파일 크기 전송

readfile($stored_path); // 파일 내용을 읽어서 브라우저로 전송
exit;


?>