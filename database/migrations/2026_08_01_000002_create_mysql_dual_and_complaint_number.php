<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::unprepared('DROP FUNCTION IF EXISTS GEN_COMPL_NO');
        DB::unprepared('DROP VIEW IF EXISTS `dual`');

        DB::unprepared(<<<'SQL'
CREATE FUNCTION GEN_COMPL_NO() RETURNS VARCHAR(20) DETERMINISTIC
BEGIN
    DECLARE seq INT;
    SELECT COALESCE(MAX(CAST(SUBSTRING(complaint_number, 9) AS UNSIGNED)), 0) + 1
    INTO seq
    FROM customer_complaints
    WHERE complaint_number LIKE CONCAT('CP', DATE_FORMAT(NOW(), '%Y%m'), '%');
    RETURN CONCAT('CP', DATE_FORMAT(NOW(), '%Y%m'), LPAD(seq, 4, '0'));
END
SQL);

        DB::unprepared('CREATE VIEW `dual` AS SELECT GEN_COMPL_NO() AS GEN_COMPL_NO');
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::unprepared('DROP VIEW IF EXISTS `dual`');
        DB::unprepared('DROP FUNCTION IF EXISTS GEN_COMPL_NO');
    }
};
