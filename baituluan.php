<?php
/**
 * Bài 1: Hiển thị dãy số Fibonacci
 *
 * Yêu cầu:
 *  - Tạo hàm generateFibonacci nhận số nguyên dương n
 *    và trả về dãy Fibonacci gồm n phần tử đầu tiên.
 *  - Hiển thị dãy Fibonacci gồm 10 phần tử đầu tiên.
 */

declare(strict_types=1);

/**
 * Sinh dãy Fibonacci gồm n phần tử đầu tiên.
 *
 * Quy ước: F(0) = 0, F(1) = 1, F(i) = F(i-1) + F(i-2) với i >= 2.
 *
 * @param int $n Số phần tử cần sinh (số nguyên dương).
 * @return int[] Mảng chứa n phần tử đầu tiên của dãy Fibonacci.
 * @throws InvalidArgumentException Nếu n không phải số nguyên dương.
 */
function generateFibonacci(int $n): array
{
    // Kiểm tra dữ liệu đầu vào
    if ($n <= 0) {
        throw new InvalidArgumentException("n phải là số nguyên dương, nhận được: $n");
    }

    // Trường hợp đặc biệt: chỉ cần 1 phần tử
    if ($n === 1) {
        return [0];
    }

    // Khởi tạo hai phần tử đầu tiên
    $fibonacci = [0, 1];

    // Mỗi phần tử tiếp theo bằng tổng hai phần tử liền trước
    for ($i = 2; $i < $n; $i++) {
        $fibonacci[] = $fibonacci[$i - 1] + $fibonacci[$i - 2];
    }

    return $fibonacci;
}

// ================== CHƯƠNG TRÌNH CHÍNH ==================

$n = 10;

try {
    $result = generateFibonacci($n);

    // Xuống dòng phù hợp khi chạy trên trình duyệt hoặc dòng lệnh
    $br = (PHP_SAPI === 'cli') ? PHP_EOL : '<br>';

    echo "Dãy Fibonacci gồm $n phần tử đầu tiên:" . $br;
    echo implode(', ', $result) . $br . $br;

    // Hiển thị chi tiết từng phần tử
    foreach ($result as $index => $value) {
        echo "F($index) = $value" . $br;
    }
} catch (InvalidArgumentException $e) {
    echo 'Lỗi: ' . $e->getMessage();
}