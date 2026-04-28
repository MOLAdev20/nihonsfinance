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
        DB::statement(
            "ALTER TABLE invoice MODIFY status ENUM('draft','unpaid','partial','paid','canceled') NOT NULL DEFAULT 'draft'"
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("UPDATE invoice SET status = 'draft' WHERE status = 'canceled'");
        DB::statement(
            "ALTER TABLE invoice MODIFY status ENUM('draft','unpaid','partial','paid') NOT NULL DEFAULT 'draft'"
        );
    }
};
