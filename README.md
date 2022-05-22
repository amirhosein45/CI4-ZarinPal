# CI4-ZarinPal
Codeigniter 4.x library for ZarinPal payment gateway 

This library let you make the payment procces based on ZarinPal, effortless and easy.

## Installation
***
1. [Download](https://github.com/amirhossein45/CI4-ZarinPal/archive/refs/heads/main.zip) the source files
2. Copy the folder `Libraries` to `app` folder of your CodeIgniter installation
3. That's it! Start using with the examples below 

## Quick Start 
***
Let's get started :)
First, we will load the Zarinpal Library into the system


```php
use App\Libraries\Zarinpal;
```

That was easy!

Now let's create object of Zarinpal

```php
$zarinpal = new Zarinpal("merchant_id");
```

Note: For testing purposes, you can turn on sandbox mode like this

```php
$zarinpal = new Zarinpal("merchant_id", true);
```

OK, now we can send user to gateway with zarinpal

```php
if ($zarinpal->request($amount, $description, $callback)){
  $authority = $zarinpal->get_authority();
  // do database stuff
  return redirect()->to($zarinpal->get_url());
}else {
  // Unable to connect to gateway
}
```

For verifying a user's payment, use these codes:


```php
$status = $this->request->getVar('Status');
$authority = $this->request->getVar('Authority');

if ($status !== 'OK' OR $authority === NULL){
  // payment canceled by user
}

if ($zarinpal->verify($amount, $authority)){
  $ref_id = $zarinpal->get_ref_id();
  // payment succeeded, do database stuff   
}else{
  $error = $zarinpal->get_error();
  // payment failed
}
```

You have reached the end of the Quick Start Guide, but please take a look at the Example code section

## Example code

```php
<?php

namespace App\Controllers;

use App\Libraries\Zarinpal;

class Home extends BaseController{

    public $zarinpal;
    private $amount = "10000";

    public function __construct(){
        $this->zarinpal = new Zarinpal("bd5edb7c-6e73-11e9-8cb6-000c29344814");
    }

    public function index(){

        $status = $this->request->getVar('Status');
        $authority = $this->request->getVar('Authority');

        if ($status !== 'OK' OR $authority === NULL)
        {
            // payment canceled by user
            $data['er'] = 'payment canceled by user';
        }

        $amount = $this->amount;

        if ($this->zarinpal->verify($amount, $authority)){
            $ref_id = $this->zarinpal->get_ref_id();
            // payment succeeded, do database stuff   
            $data['er'] = 'payment succeeded';
        }else{
            $error = $this->zarinpal->get_error();
            // payment failed
            $data['er'] = 'payment failed';
        }
        return view('welcome_message', $data);
    }

    public function test(){
        $data['er'] = $this->zarinpal->get_error();

        $amount = $this->amount;
        $description = "تست فارسی";
        $callback = "https://pay2.bispee.ir";

        if ($this->zarinpal->request($amount, $description, $callback)){
            $authority = $this->zarinpal->get_authority();
            // do database stuff
            return redirect()->to($this->zarinpal->get_url());
        }else {
            // Unable to connect to gateway
            $data['er'] = $this->zarinpal->get_error();
        }
    }
}

```
