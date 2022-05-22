<?php

namespace App\Libraries;

class Zarinpal{

    private $merchant_id;
    private $authority;
    private $error;
    private $ref_id;
    private $url;

    private $wsdl_url = 'https://www.zarinpal.com/pg/services/WebGate/wsdl';
    private $pay_url = 'https://www.zarinpal.com/pg/StartPay/';

    public function __construct($merchant_id, $sandbox = false){
        $this->merchant_id = $merchant_id;
        if($sandbox){
        $this->wsdl_url = 'https://sandbox.zarinpal.com/pg/services/WebGate/wsdl';
        $this->pay_url = 'https://sandbox.zarinpal.com/pg/StartPay/';
        }
    }

    
    public function request($amount, $desc, $callback, $email = '', $mobile = ''){
        $params = [
            'MerchantID'  => $this->merchant_id,
            'Amount'      => $amount,
            'Description' => $desc,
            'CallbackURL' => $callback,
        ];

        if ($email) {
            $params['Email'] = $email;
        }

        if ($mobile) {
            $params['Mobile'] = $mobile;
        }

        $client = new \SoapClient($this->wsdl_url);

        $result = $client->PaymentRequest($params);

        if ($result->Status !== 100) {
            $this->error = $result->Status;

            return false;
        }

        $this->authority = $result->Authority;
        
        $this->url = $this->pay_url.$this->authority;
        // use this line for ZarinGate=> $this->url = $this->pay_url.$this->authority.'/ZarinGate';

        return true;
    }

    public function verify($amount, $authority){
        $params = [
            'MerchantID' => $this->merchant_id,
            'Amount'     => $amount,
            'Authority'  => $authority,
        ];

        $client = new \SoapClient($this->wsdl_url);

        $result = $client->PaymentVerification($params);

        if ($result->Status !== 100) {
            $this->error = $result->Status;

            return false;
        }

        $this->ref_id = $result->RefID;

        return true;
    }

    public function get_url(){
        return $this->url;
    }

    public function get_authority(){
        return $this->authority;
    }

    public function get_error(){
        return $this->error;
    }

    public function get_ref_id(){
        return $this->ref_id;
    }

}
?>