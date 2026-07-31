<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

class SqlHelper
{
    public const TABLE_CLIENTS = 'clients';

    public const TABLE_ENGINEERS = 'engineers';

    public const TABLE_SAP_MODULES = 'sap_modules';

    public const TABLE_PORTAL_USERS = 'portal_users';

    public const TABLE_COMPLAINTS = 'customer_complaints';

    public static function isOracle(): bool
    {
        return DB::connection()->getDriverName() === 'oracle';
    }

    /**
     * Run a raw SELECT and normalize column names to lowercase snake_case.
     *
     * @return array<int, object>
     */
    public static function select(string $query, array $bindings = []): array
    {
        return self::normalizeRows(DB::select($query, $bindings));
    }

    public static function selectOne(string $query, array $bindings = []): ?object
    {
        $rows = self::select($query, $bindings);

        return $rows[0] ?? null;
    }

    /**
     * @param  array<int, object>  $rows
     * @return array<int, object>
     */
    public static function normalizeRows(array $rows): array
    {
        return array_map(static fn ($row) => self::normalizeRow($row), $rows);
    }

    public static function normalizeRow(?object $row): ?object
    {
        if ($row === null) {
            return null;
        }

        $normalized = new \stdClass();

        foreach ((array) $row as $key => $value) {
            $normalized->{strtolower((string) $key)} = $value;
        }

        return $normalized;
    }

    public static function dashboardSummary(?object $row): object
    {
        $row = self::normalizeRow($row);

        return (object) [
            'total_count' => (int) ($row->total_count ?? 0),
            'count_pend' => (int) ($row->count_pend ?? 0),
        ];
    }

    public static function table(string $name): string
    {
        if (self::isOracle()) {
            return 'MAWAI."'.$name.'"';
        }

        return '`'.$name.'`';
    }

    public static function column(string $name): string
    {
        if (str_contains($name, '.')) {
            [$table, $column] = explode('.', $name, 2);

            return $table.'.'.self::bareColumn($column);
        }

        return self::bareColumn($name);
    }

    public static function bareColumn(string $name): string
    {
        if (self::isOracle()) {
            return '"'.$name.'"';
        }

        return '`'.$name.'`';
    }

    public static function currentMonthFilter(string $column): string
    {
        $qualified = self::column($column);

        if (self::isOracle()) {
            return "TRUNC({$qualified}, 'MM') = TRUNC(SYSDATE, 'MM')";
        }

        return "DATE_FORMAT({$qualified}, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')";
    }

    public static function likeContains(string $column, string $parameter): string
    {
        $qualified = self::column($column);

        if (self::isOracle()) {
            return "LOWER({$qualified}) LIKE '%' || LOWER({$parameter}) || '%'";
        }

        return "LOWER({$qualified}) LIKE CONCAT('%', LOWER({$parameter}), '%')";
    }

    public static function genComplaintNoQuery(): string
    {
        return self::isOracle()
            ? 'SELECT GEN_COMPL_NO as data FROM DUAL'
            : 'SELECT GEN_COMPL_NO() as data FROM `dual`';
    }
}
