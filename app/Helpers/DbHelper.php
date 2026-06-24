<?php

namespace App\Helpers;

class DbHelper
{
    private static ?\mysqli $conn = null;

    /**
     * Mengembalikan koneksi mysqli ke database KuLocker.
     * Koneksi di-cache agar tidak dibuat berkali-kali.
     */
    public static function connection(): \mysqli
    {
        if (self::$conn === null) {
            $host = config('database.connections.mysql.host', '127.0.0.1');
            $user = config('database.connections.mysql.username', 'root');
            $pass = config('database.connections.mysql.password', '');
            $db   = config('database.connections.mysql.database', 'Kulocker');
            $port = (int) config('database.connections.mysql.port', 3306);

            self::$conn = new \mysqli($host, $user, $pass, $db, $port);

            if (self::$conn->connect_error) {
                abort(500, 'Koneksi database gagal: ' . self::$conn->connect_error);
            }

            self::$conn->query("SET time_zone = '+08:00'");
            self::$conn->set_charset('utf8mb4');
        }

        return self::$conn;
    }
}
