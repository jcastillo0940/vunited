<?php
namespace App\Support\Auth;

final class Totp
{
    private const ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    public static function secret(int $length = 20): string
    {
        $bytes = random_bytes($length); $bits = '';
        foreach (str_split($bytes) as $byte) $bits .= str_pad(decbin(ord($byte)), 8, '0', STR_PAD_LEFT);
        $out = ''; foreach (str_split($bits, 5) as $chunk) $out .= self::ALPHABET[bindec(str_pad($chunk, 5, '0'))];
        return substr($out, 0, 32);
    }
    public static function verify(?string $secret, string $code, int $window = 1): bool
    {
        if (!$secret || !preg_match('/^\d{6}$/', $code)) return false;
        $key = self::decode($secret); $counter = intdiv(time(), 30);
        for ($i = -$window; $i <= $window; $i++) {
            $bin = pack('N*', 0).pack('N*', $counter + $i); $hash = hash_hmac('sha1', $bin, $key, true); $offset = ord($hash[19]) & 15;
            $value = ((ord($hash[$offset]) & 127) << 24) | (ord($hash[$offset+1]) << 16) | (ord($hash[$offset+2]) << 8) | ord($hash[$offset+3]);
            if (hash_equals(str_pad((string)($value % 1000000), 6, '0', STR_PAD_LEFT), $code)) return true;
        }
        return false;
    }
    public static function uri(string $secret, string $email): string { return 'otpauth://totp/Veraguas%20United:'.rawurlencode($email).'?secret='.$secret.'&issuer=Veraguas%20United'; }
    private static function decode(string $value): string { $value = strtoupper(rtrim($value, '=')); $bits=''; foreach(str_split($value) as $c){$p=strpos(self::ALPHABET,$c); if($p===false) continue; $bits.=str_pad(decbin($p),5,'0',STR_PAD_LEFT);} $out=''; foreach(str_split($bits,8) as $b) if(strlen($b)===8)$out.=chr(bindec($b)); return $out; }
}
