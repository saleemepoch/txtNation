# Laravel package for txtNation Gateway

The txtNation Gateway PHP Library works with Laravel 5+. It is an extension of Marc O'Leary's txtnation/txtnation-gateway-php.


- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
  - [Free SMS] (#free-sms)
  - [Premium SMS] (#premium-sms)
- [Support](#support)

<a name="prerequisites"></a>
## Prerequisites

This README assumes you are using the following PHP extensions:

- php-curl
- php-mbstring

<a name="installation"></a>
## Installation

* Use following command to install:

```
composer require saleemepoch/txtnation
```

* Add the service provider to your $providers array in config/app.php file like: 

```
'saleemepoch\txtNation\Providers\txtNationServiceProvider' // Laravel 5
```
```
saleemepoch\txtNation\Providers\txtNationServiceProvider::class // Laravel 5.1 or greater
```

* Add the alias to your $aliases array in config/app.php file like: 

```
'txtNation' => 'saleemepoch\txtNation\Facades\txtNation' // Laravel 5
```
```
'txtNation' => saleemepoch\txtNation\Facades\txtNation::class // Laravel 5.1 or greater
```

* Run the following command to publish configuration:

```
php artisan vendor:publish
```

<a name="configuration"></a>
## Configuration

* After installation, you will need to add your txtNation settings. Following is the code you will find in **config/txtNation.php**, which you should update accordingly.

```
return [
	/* REQUIRED */
	'username' => '',
	'ekey' => '',
	// also known as sender id.
	'title' => '',

	/* OPTIONAL */
	// if set, requests to txtNation will also consists of the supplied shortcode
	'shortcode' => '',

	// required only if sending MT to Bill based on texted keywords
	'keywords' => [
		/*
		 * keywords and their corresponding amounts.
		 *
		 * This will set the value when billing the customer based on the keyword texted by the user
		 *
		 *
		'BILL10' => 10.00,
		'BILL5' => 5.00,
		*/
		'BILL1' => 1.00
	]
];
```

<a name="usage"></a>
## Usage

<a name="free-sms"></a>
* To send a free SMS
```
$message = new txtNation;
$result = $message->msisdn('447459831491')->body('Please reply to this message with keyword PENNY!')->senderId('784645')->send();
```

<a name="premium-sms"></a>
* MT to Bill a user after opting-in

1) Make sure in config/txtNation.php you have keywords mapped to values the user is to be charged, e.g.

```
'keywords' => [
		'BILL10' => 10.00,
		'BILL5' => 5.00,
		'BILL1' => 1.00
	]
```

2) Login to your txtNation account -> APIs -> Gateway and set your responder URL, e.g. http://example.com/txtNationResponse

3) Setup a route in your routes.php:

```
Route::post('txtNationResponse', 'txtNationController@response');
```

4) Exclude this route from CSRF protection by adding the URI to the $except property of the VerifyCsrfToken middleware:
```
protected $except = [
        'txtNationResponse',
    ];
```

5) Setup a controller like txtNationController for this example and create a method:
```
public function response(Request $request) {
    
    if ($request->action == 'mpush_ir_message' &&
        (isset($request->billing) && $request->billing == 'MT')) {
         
        $keywords = config('txtNation.keywords');
        
        $message = new txtNation;
        $result = $message->msisdn($request->number)
        ->reply(1)
        ->body('Thank you for your business')
        ->id($request->id)
        ->network($request->network)
        ->currency('GBP')
        ->value($keywords[$request->message])
        ->send();
    
    }

}
```

The above controller method will accept the response from txtNation and if billing is set to MT it will reply back charging the user.

