<?php

namespace LinusU\Bitcoin;

class AddressValidator {

    CONST MAINNET = "MAINNET";
    CONST TESTNET = "TESTNET";

    CONST MAINNET_PUBKEY = "00";
    CONST MAINNET_SCRIPT = "05";

    CONST TESTNET_PUBKEY = "6F";
    CONST TESTNET_SCRIPT = "C4";

    CONST GENERATOR = [0x3b6a57b2, 0x26508e6d, 0x1ea119fa, 0x3d4233dd, 0x2a1462b3];
    CONST CHARKEY_KEY = [
        -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
        -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
        -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
        15, -1, 10, 17, 21, 20, 26, 30,  7,  5, -1, -1, -1, -1, -1, -1,
        -1, 29, -1, 24, 13, 25,  9,  8, 23, -1, 18, 22, 31, 27, 19, -1,
        1,  0,  3, 16, 11, 28, 12, 14,  6,  4,  2, -1, -1, -1, -1, -1,
        -1, 29, -1, 24, 13, 25,  9,  8, 23, -1, 18, 22, 31, 27, 19, -1,
        1,  0,  3, 16, 11, 28, 12, 14,  6,  4,  2, -1, -1, -1, -1, -1
    ];
    static public function typeOf($addr) {
        
        
        
        // bech32 https://github.com/bitcoin/bips/blob/master/bip-0173.mediawiki
        if(preg_match('/^(bc|tb)1([023456789acdefghjklmnpqrstuvwxyz]+[023456789acdefghjklmnpqrstuvwxyz]{6})$/', $addr, $match)) {
            
            $hrp = $match[1];
            $chars = array_values(unpack('C*', $match[2]));
            
            $data = [];
            foreach ($chars as $char) {
                $data[] = ($char & 0x80) ? -1 : self::CHARKEY_KEY[$char];
            }
            
            $polyMod = self::polyMod(array_merge(self::hrpExpand($hrp),$data));
            
            if($hrp=='bc') {
                return self::MAINNET_PUBKEY;
            }
            if($hrp=='tb') {
                return self::TESTNET_PUBKEY;
            }
        }
        
        
        if (preg_match('/[^1-9A-HJ-NP-Za-km-z]/', $addr) ) {
        
            return false;
        }

        
        
        $decoded = self::decodeAddress($addr);
        
        
        if (strlen($decoded) != 50) {
            return false;
        }

        $version = substr($decoded, 0, 2);

        $check = substr($decoded, 0, strlen($decoded) - 8);
        $check = pack("H*", $check);
        $check = hash("sha256", $check, true);
        $check = hash("sha256", $check);
        $check = strtoupper($check);
        $check = substr($check, 0, 8);

        $isValid = ($check == substr($decoded, strlen($decoded) - 8));

        return ($isValid ? $version : false);
    }

    static public function isValid($addr, $version = null) {

        $type = self::typeOf($addr);

        if ($type === false) {
            return false;
        }

        if (is_null($version)) {
            $version = self::MAINNET;
        }

        switch ($version) {
            case self::MAINNET:
              $valids = [self::MAINNET_PUBKEY, self::MAINNET_SCRIPT];
              break;
            case self::TESTNET:
              $valids = [self::TESTNET_PUBKEY, self::TESTNET_SCRIPT];
              break;
            case self::MAINNET_PUBKEY:
            case self::MAINNET_SCRIPT:
            case self::TESTNET_PUBKEY:
            case self::TESTNET_SCRIPT:
              $valids = [$version];
              break;
            default:
              throw new \Exception('Unknown version constant');
        }

        return in_array($type, $valids);
    }

    static protected function decodeAddress($data) {

        $charsetHex = '0123456789ABCDEF';
        $charsetB58 = '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz';

        $raw = "0";
        for ($i = 0; $i < strlen($data); $i++) {
            $current = (string) strpos($charsetB58, $data[$i]);
            $raw = (string) bcmul($raw, "58", 0);
            $raw = (string) bcadd($raw, $current, 0);
        }

        $hex = "";
        while (bccomp($raw, 0) == 1) {
            $dv = (string) bcdiv($raw, "16", 0);
            $rem = (integer) bcmod($raw, "16");
            $raw = $dv;
            $hex = $hex . $charsetHex[$rem];
        }

        $withPadding = strrev($hex);
        for ($i = 0; $i < strlen($data) && $data[$i] == "1"; $i++) {
            $withPadding = "00" . $withPadding;
        }

        if (strlen($withPadding) % 2 != 0) {
            $withPadding = "0" . $withPadding;
        }

        return $withPadding;
    }

    static protected function polyMod(array $values)
    {
        $numValues = count($values);
        $chk = 1;
        for ($i = 0; $i < $numValues; $i++) {
            $top = $chk >> 25;
            $chk = ($chk & 0x1ffffff) << 5 ^ $values[$i];
            for ($j = 0; $j < 5; $j++) {
                $value = (($top >> $j) & 1) ? self::GENERATOR[$j] : 0;
                $chk ^= $value;
            }
        }
        return $chk;
    }
    static protected function hrpExpand($hrp)
    {
        $hrpLen = strlen($hrp);
        $expand1 = [];
        $expand2 = [];
        for ($i = 0; $i < $hrpLen; $i++) {
            $o = \ord($hrp[$i]);
            $expand1[] = $o >> 5;
            $expand2[] = $o & 31;
        }
        return \array_merge($expand1, [0], $expand2);
    }
    
}
