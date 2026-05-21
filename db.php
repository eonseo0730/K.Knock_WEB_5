<?php
// mysql DB에 연결하는 코드

$host = "db"; // .yml 의 서비스 이름이 호스트명
$user = "root"; // mysql 접속 계정
$password = "DB";
$dbname = "board"; // 접속할 db 이름

// 이 정보를 바탕으로 mysql 연결 시도 -> 성공 시 연결 객체가 담김
$conn=mysqli_connect($host, $user, $password, $dbname);

if(!$conn){ // 연결 실패 시
    die("DB 연결 실패 : ". mysqli_connect_error()); // 실행 멈추고 에러 메시지 출력
}

mysqli_set_charset($conn, "utf8");

?>