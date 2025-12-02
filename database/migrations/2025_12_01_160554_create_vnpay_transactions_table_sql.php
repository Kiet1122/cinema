<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Kiểm tra nếu bảng VNPayTransactions chưa tồn tại
        $tableExists = DB::select("SHOW TABLES LIKE 'VNPayTransactions'");
        
        if (empty($tableExists)) {
            DB::statement("
                CREATE TABLE VNPayTransactions (
                    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    PaymentID INT UNSIGNED NOT NULL,
                    BookingID INT UNSIGNED NOT NULL,
                    UserID INT UNSIGNED NOT NULL,
                    Amount DECIMAL(15,2) NOT NULL,
                    PaymentCode VARCHAR(100) NOT NULL UNIQUE,
                    TransactionNo VARCHAR(100) NULL,
                    PaymentInfo TEXT NULL,
                    Status VARCHAR(20) DEFAULT 'pending',
                    ResponseCode VARCHAR(10) NULL,
                    BankCode VARCHAR(20) NULL,
                    PaidAt DATETIME NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    INDEX idx_payment_id (PaymentID),
                    INDEX idx_booking_id (BookingID),
                    INDEX idx_payment_code (PaymentCode),
                    INDEX idx_status (Status)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        }
        
        // Kiểm tra và thêm cột vào bảng Payment nếu cần
        $columns = DB::select("SHOW COLUMNS FROM Payment");
        $columnNames = array_column($columns, 'Field');
        
        if (!in_array('PaymentCode', $columnNames)) {
            DB::statement("ALTER TABLE Payment ADD COLUMN PaymentCode VARCHAR(100) NULL UNIQUE");
        }
        
        if (!in_array('TransactionNo', $columnNames)) {
            DB::statement("ALTER TABLE Payment ADD COLUMN TransactionNo VARCHAR(100) NULL");
        }
        
        if (!in_array('PaymentInfo', $columnNames)) {
            DB::statement("ALTER TABLE Payment ADD COLUMN PaymentInfo TEXT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Xóa bảng VNPayTransactions
        DB::statement("DROP TABLE IF EXISTS VNPayTransactions");
        
        // Xóa các cột đã thêm vào Payment (nếu có)
        DB::statement("ALTER TABLE Payment DROP COLUMN IF EXISTS PaymentCode");
        DB::statement("ALTER TABLE Payment DROP COLUMN IF EXISTS TransactionNo");
        DB::statement("ALTER TABLE Payment DROP COLUMN IF EXISTS PaymentInfo");
    }
};