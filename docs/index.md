# Y-Link

URL Shortener writen in pure PHP.

**[WebSite](https://y-link.ml)**

## Installation

1. Clone the project
```bash 
$ git clone https://github.com/YehudaEi/Y-Link
```
2. Create Database from the dump ```create_db.sql```

3. Config the database in [public/include/config.php](https://github.com/YehudaEi/Y-Link/blob/master/public/include/config.php) in [line 14](https://github.com/YehudaEi/Y-Link/blob/master/public/include/config.php#L14)

4. Config the site domain in [public/include/config.php](https://github.com/YehudaEi/Y-Link/blob/master/public/include/config.php) in [line 16](https://github.com/YehudaEi/Y-Link/blob/master/public/include/config.php#L16)

5. Config the site url in [public/YLinkClient.php](https://github.com/YehudaEi/Y-Link/blob/master/public/YLinkClient.php) in [line 21](https://github.com/YehudaEi/Y-Link/blob/master/public/YLinkClient.php#L21)

6. if you use apache go to 7. if you use nginx go to 8.

7. copy [apache.conf](https://github.com/YehudaEi/Y-Link/blob/master/apache.conf) to ```/etc/apache2/sites-available/```
    1. ```bash
        $ sudo mv apache.conf /etc/apache2/sites-available/servername.conf
        ```
    2. Config the [servername.conf](https://github.com/YehudaEi/Y-Link/blob/master/apache.conf)
    3. enable servername.conf
        ```bash
        $ sudo a2ensite servername.conf
        ```
    4. go to 9.
8. copy [nginx.conf](https://github.com/YehudaEi/Y-Link/blob/master/nginx.conf) to ```/etc/nginx/sites-available/```
    1. ```bash
        $ sudo mv nginx.conf /etc/nginx/sites-available/servername.conf
        ```
    2. Config the [servername.conf](https://github.com/YehudaEi/Y-Link/blob/master/nginx.conf)
    3. enable servername
        ```bash
        $ sudo ln -s /etc/nginx/sites-available/servername.conf /etc/nginx/sites-enabled/
        ```
    4. go to 9.

9. Done! Enjoy 😁

## API Docs
### Methods:
1. **create**
	1. Description: `Create new shorten link`
	2. HTTP Method: `POST`
	3. Paramaters: 
		1. method:
			1. type: `string`
			2. validLength: `4...6`
			3. description: `the method (e.g. create, info, help...)`
		2. password:
			1. type: `string`
			2. validLength: `4...30`
			3. description: `Creator verification`
		3. link:
			1. type: `url`
			2. description: `the long link`

2. **info**
	1. Description: `Get info of shorten link`
	2. HTTP Method: `POST`
	3. Paramaters: 
		1. method:
			1. type: `string`
			2. validLength: `4...6`
			3. description: `the method (e.g. create, info, help...)`
		2. password:
			1. type: `string`
			2. validLength: `4...30`
			3. description: `Creator verification`
		3. shorten_link:
			1. type: `url`
			2. validLength: `21...47`
			3. description: `https://y-link.ml shortened link`

3. **stats**
	1. Description: `Get stats of shorten link`
	2. HTTP Method: `POST`
	3. Paramaters: 
		1. method:
			1. type: `string`
			2. validLength: `4...6`
			3. description: `the method (e.g. create, info, help...)`
		2. password:
			1. type: `string`
			2. validLength: `4...30`
			3. description: `Creator verification`
		3. shorten_link:
			1. type: `url`
			2. validLength: `21...47`
			3. description: `https://y-link.ml shortened link`

4. **raw_stats**
	1. Description: `Get raw stats of shorten link`
	2. HTTP Method: `POST`
	3. Paramaters: 
		1. method:
			1. type: `string`
			2. validLength: `4...6`
			3. description: `the method (e.g. create, info, help...)`
		2. password:
			1. type: `string`
			2. validLength: `4...30`
			3. description: `Creator verification`
		3. shorten_link:
			1. type: `url`
			2. validLength: `21...47`
			3. description: `https://y-link.ml shortened link`

5. **custom**
	1. Description: `Create new custom shorten link`
	2. HTTP Method: `POST`
	3. Paramaters: 
		1. method:
			1. type: `string`
			2. validLength: `4...6`
			3. description: `the method (e.g. create, info, help...)`
		2. password:
			1. type: `string`
			2. validLength: `4...30`
			3. description: `Creator verification`
		3. link:
			1. type: `url`
			2. description: `the long link`
		4. path:
			1. type: `string`
			2. validLength: `4...30`
			3. description: `shortened link path: https://y-link.ml/{path}`

6. **edit**
	1. Description: `Edit link destination`
	2. HTTP Method: `POST`
	3. Paramaters: 
		1. method:
			1. type: `string`
			2. validLength: `4...6`
			3. description: `the method (e.g. create, info, help...)`
		2. password:
			1. type: `string`
			2. validLength: `4...30`
			3. description: `Creator verification`
		3. shorten_link:
			1. type: `url`
			2. validLength: `21...47`
			3. description: `https://y-link.ml shortened link`
		4. link:
			1. type: `url`
			2. description: `the long link`

7. **help**
	1. Description: `Receive help`
	2. HTTP Method: `POST`
	3. Paramaters: 
		1. method:
			1. type: `string`
			2. validLength: `4...6`
			3. description: `the method (e.g. create, info, help...)`

### API Client
Example:
```php
<?php
include 'YLinkClient.php';

$url = "https://yehudae.net/Home/";
$path = "custom_path";

$client = new YLink("Hello World ~ Password");
$res = $client->CreateLink($url, $path);

if ($res['ok'] == true){
	echo $res['res']['link'];
}
else{
	echo $res['error']['message'];
}
```

## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

## License
[AGPL-3](https://github.com/YehudaEi/Y-Link/blob/master/LICENSE)