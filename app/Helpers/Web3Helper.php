<?php

namespace App\Helpers;

use Web3\Web3;
use Web3\Contract;

class Web3Helper
{
    private $web3;
    private $contract;

    public function __construct()
    {
        $this->web3 = new Web3(env('BLOCKCHAIN_RPC'));

        $abi = json_decode(file_get_contents(storage_path('app/AccessControlABI.json')), true);

        $this->contract = new Contract($this->web3->provider, $abi);
        $this->contract->at(env('CONTRACT_ADDRESS'));
    }

    public function hasAccess($patient, $user)
    {
        $result = null;

        $this->contract->call('hasAccess', $patient, $user, function ($err, $res) use (&$result) {
            if ($err !== null) {
                throw new \Exception($err->getMessage());
            }
            $result = $res[0];
        });

        return $result;
    }
    public static function verifySignature($message, $signature, $walletAddress)
    {
        // TODO: Use web3.php to verify the signature
        // Example (pseudo-code):
        // $recovered = Web3::recover($message, $signature);
        // return strtolower($recovered) === strtolower($walletAddress);
        //return Web3Helper::verifySignature($message, $signature, $walletAddress);
        return true;
    }

}