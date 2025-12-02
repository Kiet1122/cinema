<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('Payment', function (Blueprint $table) {
            // Thêm cột mới
            $table->string('PaymentCode')->unique()->nullable()->after('PaymentID');
            $table->string('TransactionNo')->nullable()->after('PaymentCode');
            $table->json('PaymentInfo')->nullable()->after('TransactionNo');
            $table->timestamp('PaymentDate')->nullable()->change();

            // Thêm indexes
            $table->index('PaymentCode');
            $table->index('Status');
            $table->index('PaymentMethod');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('Payment', function (Blueprint $table) {
            $table->dropColumn(['PaymentCode', 'TransactionNo', 'PaymentInfo']);
            $table->dropIndex(['PaymentCode', 'Status', 'PaymentMethod']);
        });
    }
};
