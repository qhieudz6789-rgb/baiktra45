<?php
/* 
 * BÀI 2: QUẢN LÝ THÔNG TIN HỌC SINH SỬ DỤNG DATABASE (MySQL + PDO)
 */
declare(strict_types=1);
const DB_HOST     = '127.0.0.1';
const DB_PORT     = '3306';
const DB_NAME     = 'quan_ly_hoc_sinh';
const DB_USERNAME = 'root';
const DB_PASSWORD = '';
function taoKetNoiDatabase(): PDO
{
    $tuyChonPDO = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        $dsnServer = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';charset=utf8mb4';
        $ketNoiServer = new PDO($dsnServer, DB_USERNAME, DB_PASSWORD, $tuyChonPDO);
        $ketNoiServer->exec(
            'CREATE DATABASE IF NOT EXISTS `' . DB_NAME . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
        );
        $dsnDatabase = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        return new PDO($dsnDatabase, DB_USERNAME, DB_PASSWORD, $tuyChonPDO);
    } catch (PDOException $loi) {
        die('Kết nối database thất bại: ' . $loi->getMessage() . PHP_EOL);
    }
}

function taoBangHocSinhNeuChuaCo(PDO $ketNoi): void
{
    $cauLenhTaoBang = "
        CREATE TABLE IF NOT EXISTS hoc_sinh (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            age INT NOT NULL,
            grade DECIMAL(4,2) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ";

    $ketNoi->exec($cauLenhTaoBang);
}
function themDuLieuMauNeuBangDangTrong(PDO $ketNoi): void
{
    $soLuongHocSinh = (int) $ketNoi->query('SELECT COUNT(*) AS tong FROM hoc_sinh')->fetch()['tong'];

    if ($soLuongHocSinh > 0) {
        return; 
    }

    $danhSachHocSinhMau = [
        ['name' => 'Nguyễn Văn An',   'age' => 15, 'grade' => 8.5],
        ['name' => 'Trần Thị Bình',   'age' => 16, 'grade' => 9.2],
        ['name' => 'Lê Hoàng Cường',  'age' => 15, 'grade' => 7.8],
        ['name' => 'Phạm Thị Dung',   'age' => 16, 'grade' => 9.2],
    ];

    $cauLenhThem = $ketNoi->prepare(
        'INSERT INTO hoc_sinh (name, age, grade) VALUES (:name, :age, :grade)'
    );

    foreach ($danhSachHocSinhMau as $hocSinh) {
        $cauLenhThem->execute([
            ':name'  => $hocSinh['name'],
            ':age'   => $hocSinh['age'],
            ':grade' => $hocSinh['grade'],
        ]);
    }
}

/**
 * Lấy toàn bộ danh sách học sinh từ database.
 *
 * @return array Mảng các mảng kết hợp thông tin học sinh
 */
function layDanhSachHocSinh(PDO $ketNoi): array
{
    $cauLenhTruyVan = $ketNoi->query('SELECT id, name, age, grade FROM hoc_sinh ORDER BY id ASC');
    return $cauLenhTruyVan->fetchAll();
}

/**
 * Hiển thị thông tin của tất cả học sinh ra màn hình.
 */
function hienThiDanhSachHocSinh(array $danhSach): void
{
    foreach ($danhSach as $hocSinh) {
        echo "ID: {$hocSinh['id']} - "
            . "Họ tên: {$hocSinh['name']} - "
            . "Tuổi: {$hocSinh['age']} - "
            . "Điểm: {$hocSinh['grade']}" . PHP_EOL;
    }
}

/**
 * Tìm học sinh có điểm (grade) 
 *
 * @return array|null 
 */
function timHocSinhDiemCaoNhat(PDO $ketNoi): ?array
{
    $cauLenhTruyVan = $ketNoi->query(
        'SELECT id, name, age, grade FROM hoc_sinh ORDER BY grade DESC LIMIT 1'
    );

    $ketQua = $cauLenhTruyVan->fetch();

    return $ketQua !== false ? $ketQua : null;
}


$ketNoiDatabase = taoKetNoiDatabase();

taoBangHocSinhNeuChuaCo($ketNoiDatabase);
themDuLieuMauNeuBangDangTrong($ketNoiDatabase);

echo '<pre>';

echo "=== DANH SÁCH TẤT CẢ HỌC SINH (TỪ DATABASE) ===" . PHP_EOL;
$danhSachHocSinh = layDanhSachHocSinh($ketNoiDatabase);
hienThiDanhSachHocSinh($danhSachHocSinh);
echo PHP_EOL;

echo "=== HỌC SINH CÓ ĐIỂM CAO NHẤT ===" . PHP_EOL;
$hocSinhGioiNhat = timHocSinhDiemCaoNhat($ketNoiDatabase);

if ($hocSinhGioiNhat !== null) {
    echo "ID: {$hocSinhGioiNhat['id']} - "
        . "Họ tên: {$hocSinhGioiNhat['name']} - "
        . "Tuổi: {$hocSinhGioiNhat['age']} - "
        . "Điểm: {$hocSinhGioiNhat['grade']}" . PHP_EOL;
} else {
    echo "Bảng học sinh đang trống." . PHP_EOL;
}

echo '</pre>';