<?php

namespace App\Helpers;

use Web3\Web3;
use Web3\Contract;

class Web3Helper
{
    public static function verifySignature($message, $signature, $walletAddress)
    {
        // TODO: Use web3.php to verify the signature
        // Example (pseudo-code):
        // $recovered = Web3::recover($message, $signature);
        // return strtolower($recovered) === strtolower($walletAddress);

        return true;
    }

}