# Y-link
## [Website](http://y-link.ml)

This code shurten links.

### the files:
* bot.php - create shortened links in telegram ([the bot](https://t.me/YLINKBot)) 
    * shorten link and get count of clicks of the link.
    * work in inline!
    * config:
        * set token in func curlPost
        * set webHook (https://api.telegram.org/bot^Token^/setwebhook?url=YOUR-DOMAIN.COM/bot.php)
* index.php - create shortened links on the web
* link.php  - don't touch.
* .htaccess - don't touch.
* table.sql - config the DB (import to an existing db)
* **api.php** - api...
    * config:
        * in the func LinkTool, set:
            * hostname
            * username
            * password
            * database
        * change ^y-link.ml^ to your domain
        * set tokens in line 132 (Optional) 
    * The api options:
        * create shortened link
        * edit shortened link
        * get num of clicks of the shortened links
        * create custom shortened links
        * receive help

## API methods

### create 
**creating new link.**

- params
    * method    = "create"
    * password  = "{creator_pass} - to get num of clicks / edit the link"
    * link      = "{the link}"

- Example:

       params = {
        "method" : "create",
            "password": "12345678",
            "link": "https://www.google.com"
      }
  
### get_click
**get num of clicks.**

- params:
  * method = "get_click"
  * password = "{Creator verification}"
  * link  = "{the shorten link}"

- Example

       params = {
        "method" : "get_click",
            "password": "12345678",
            "link": "http://y-link.ml/LinkID"
      }

### edit_link
**edit link...**

- params:
  * method = "edit_link"
  * password = "{Creator verification}"
  * shorten_link = "the shortened link"
  * link  = "{the new link}"

- Example

       params = {
        "method" : "edit_link",
            "password": "12345678",
            "shorten_link": "http://y-link.ml/LinkID",
            "link": "https://www.google.com"
      }

### custom
**create custom shortened links**

- params:
  * method = "custom"
  * password = "{Creator verification}"
  * token = "{token}... (api.php - line 132)"
  * path = "custom path link (e.g. y-link.ml/path)"
  * link  = "{the link}"

- Example

       params = {
        "method" : "custom",
            "password": "12345678",
            "token": "ABC123",
            "path": "custom"
            "link": "https://www.google.com"
      }

### help
**receive help**

- params:
  * method = "help"

- Example

       params = {
        "method" : "help"
      }

## Types

* `method` 
    - type -  "string"
    - description -"The method.."

* `password`
     - type  - "string (Up to 20)"
     - description - "creator id - to get num of clicks / edit the link"

* `link`
    - type - "valid link (string)"
    - description - "The Link..."

* `path` 
    - type - "string (Up to 20)"
    - description - "shortened link path: y-link.ml/path"

* `shorten_link`
    - type - "y-link.ml link (string)"
    - description - "api output shortened link"


# Code example  

## command line
```bash
$ curl 'http://y-link.ml/api.php?method=create&password=123&link=google.com
```

## php
```php
echo file_get_contents("http://y-link.ml/api.php?method=create&password=123&link=google.com");
```
**- return**
```
 {
     'ok': True,
     'res': {
         'password': '123', 
         'link': 'http://y-link.ml/rGUye3'
     }
 }
```


## python

```python
import requests


def make_requests(method, password, link):
    params = {
        'method': method,
        'password': password,
        'link': link
    }
    return requests.get('http://y-link.ml/api.php', params=params).json()


print(make_requests(method='create', password=1234, link='www.google.com'))

```
**- return**
```python
 {
     'ok': True,
     'res': {
         'password': '1234', 
         'link': 'http://y-link.ml/rGUye3'
     }
 }
```