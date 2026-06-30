<?php
// mysql DB에 연결하는 코드

$host = "db"; // .yml 의 서비스 이름이 호스트명
$user = "root"; // mysql 접속 계정
$password = "DB";
$dbname = "board"; // 접속할 db 이름

// 이 정보를 바탕으로 mysql 연결 시도 -> 성공 시 연결 객체가 담김
$conn=mysqli_connect($host, $user, $password, $dbname);

if(!$conn){ // 연결 실패 시
    error_log("DB 연결 실패 : ". mysqli_connect_error()); // 상세 에러는 서버 로그 파일에 기록
    die("오류가 발생했습니다. 다시 시도해주세요.");
}

mysqli_set_charset($conn, "utf8"); // 문자 인코딩

// 업로드 파일 검증 함수 (확장자, mime 타입 체크)
function validate_upload_file($file){
    $allowed_ext=['jpg','jpeg','png','gif','pdf','zip','txt']; // 허용 확장자 화이트리스트
    $allowed_mime=[ // 확장자와 mime 타입 매핑
        'jpg'=>'image/jpeg','jpeg'=>'image/jpeg','png'=>'image/png',
        'gif'=>'image/gif','pdf'=>'application/pdf',
        'zip'=>'application/zip','txt'=>'text/plain' // 확장자 => 내용
    ];
    
    $ext=strtolower(pathinfo($file['name'],PATHINFO_EXTENSION)); // 파일명에서 확장자만 소문자로 추출

    if(!in_array($ext,$allowed_ext)){
        return false; // 화이트리스트에 없는 확장자면 false 리턴
    }

    $finfo=finfo_open(FILEINFO_MIME_TYPE); // 파일 내용을 읽어서 MIME 타입 판별하는 핸들 생성
    $real_mime=finfo_file($finfo,$file['tmp_name']); // 실제 바이트 내용으로 판별
    finfo_close($finfo); // 핸들 close

    if($real_mime !== $allowed_mime[$ext]){ // 확장자와 실제 내용이 다르면 false 리턴
        return false;
    }

    return true; // 화이트리스트에 있는 확장자 && 확장자와 실제 내용 일치 시 true 리턴

}

session_set_cookie_params([
    'httponly'=>true, // js로 세션 쿠키 읽지 X
    'samesite'=>'Lax', // 다른 사이트에서 시작된 요청에서는 쿠키 실어보내지 X
    'secure'=>false // https가 아니라서 (secure는 https에서만 쿠키 전송)
]);

session_start();

//csrf 토큰 생성 함수 (세션에 없으면 새로 만들고, 있으면 기존 값 재사용)
function generate_csrf_token(){
    if(!isset($_SESSION['csrf_token'])){ // 세션에 csrf_tokem이 없으면
        $_SESSION['csrf_token']=bin2hex(random_bytes(32)); // 32바이트 랜덤 값을 16진수 문자열로 생성
    }
    return $_SESSION['csrf_token']; // 생성된 토큰 리턴
}

//csrf 토큰 검증
function verify_csrf_token($token){
    if(!isset($_SESSION['csrf_token']) || !$token){ // 세션에 토큰이 없거나 받은 토큰이 존재하지 않는 경우
        return false;
    }
    return hash_equals($_SESSION['csrf_token'],$token); // 해시해서 일치여부 확인
}

?>