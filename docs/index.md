# Y-Link

URL Shortener writen in pure PHP.

**[WebSite](https://y-link.ml)**

**Docs coming soon 😎**

## Installation

1. Clone the project
```bash 
$ git clone https://github.com/YehudaEi/Y-Link
```
2. Config the database in [public/include/config.php](https://github.com/YehudaEi/Y-Link/blob/master/public/include/config.php) in [line 14](https://github.com/YehudaEi/Y-Link/blob/master/public/include/config.php#L14)

3. Config the site domain in [public/include/config.php](https://github.com/YehudaEi/Y-Link/blob/master/public/include/config.php) in [line 16](https://github.com/YehudaEi/Y-Link/blob/master/public/include/config.php#L16)

4. if you use apache go to 5. if you use nginx go to 6.

5. copy [apache.conf](https://github.com/YehudaEi/Y-Link/blob/master/apache.conf) to ```/etc/apache2/sites-available/```
    1. ```bash
        $ sudo mv apache.conf /etc/apache2/sites-available/servername.conf
        ```
    2. Config the [servername.conf](https://github.com/YehudaEi/Y-Link/blob/master/apache.conf)
    3. enable servername.conf
        ```bash
        $ sudo a2ensite servername.conf
        ```
    4. go to 7.
6. copy [nginx.conf](https://github.com/YehudaEi/Y-Link/blob/master/nginx.conf) to ```/etc/nginx/sites-available/```
    1. ```bash
        $ sudo mv nginx.conf /etc/nginx/sites-available/servername.conf
        ```
    2. Config the [servername.conf](https://github.com/YehudaEi/Y-Link/blob/master/nginx.conf)
    3. enable servername
        ```bash
        $ sudo ln -s /etc/nginx/sites-available/servername.conf /etc/nginx/sites-enabled/
        ```
    4. go to 7.

7. Done! Enjoy 😁

## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

## License
[AGPL-3](https://github.com/YehudaEi/Y-Link/blob/master/LICENSE)