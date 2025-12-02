<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Kiểm tra xem bảng Payment đã tồn tại chưa
        if (!Schema::hasTable('Payment')) {
            // Nếu chưa tồn tại, tạo bảng mới
            Schema::create('Payment', function (Blueprint $table) {
                $table->increments('PaymentID');
                $table->unsignedInteger('BookingID');
                $table->unsignedInteger('UserID');
                $table->decimal('Amount', 15, 2);
                $table->string('PaymentMethod', 50);
                $table->string('Status', 20)->default('pending');
                $table->string('PaymentCode', 100)->unique()->nullable();
                $table->string('TransactionNo', 100)->nullable();
                $table->text('PaymentInfo')->nullable();
                $table->dateTime('PaymentDate')->nullable();
                $table->timestamps();
                
                // Foreign keys
                $table->foreign('BookingID')->references('BookingID')->on('Booking')->onDelete('cascade');
                $table->foreign('UserID')->references('UserID')->on('User')->onDelete('cascade');
            });
        } else {
            // Nếu bảng đã tồn tại, thêm các cột mới bằng raw SQL để tránh lỗi Doctrine
            DB::statement("
                ALTER TABLE Payment 
                ADD COLUMN IF NOT EXISTS PaymentCode VARCHAR(100) NULL UNIQUE,
                ADD COLUMN IF NOT EXISTS TransactionNo VARCHAR(100) NULL,
                ADD COLUMN IF NOT EXISTS PaymentInfo TEXT NULL,
                ADD COLUMN IF NOT EXISTS UserID INT UNSIGNED NULL
            ");
            
            // Thêm index nếu cần
            DB::statement("CREATE INDEX IF NOT EXISTS payment_code_idx ON Payment (PaymentCode)");
            DB::statement("CREATE INDEX IF NOT EXISTS payment_status_idx ON Payment (Status)");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Chỉ xóa các cột mới thêm vào
        Schema::table('Payment', function (Blueprint $table) {
            if (Schema::hasColumn('Payment', 'PaymentCode')) {
                $table->dropColumn('PaymentCode');
            }
            
            if (Schema::hasColumn('Payment', 'TransactionNo')) {
                $table->dropColumn('TransactionNo');
            }
            
            if (Schema::hasColumn('Payment', 'PaymentInfo')) {
                $table->dropColumn('PaymentInfo');
            }
            
            if (Schema::hasColumn('Payment', 'UserID')) {
                $table->dropColumn('UserID');
            }
        });
    }
};