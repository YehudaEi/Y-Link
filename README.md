# Y-link
*[The site](http://y-link.ml)*

This code shurten links.

### the files:
* bot.php - create shurten link in telegram ([the bot](https://t.me/YLINKBot)) 
    * shurten link and get count clicks of link.
    * work in inline!
    * config:
        * set token in func curlPost
        * set webHook (https://api.telegram.org/bot^Token^/setwebhook?url=YOUR-DOMAIN.COM/bot.php)
* index.php - create shurten link in the site
* link.php  - d'ont tach.
* .htaccess - d'ont tach.
* table.sql - config the DB (import to exist db)
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
        * create shurten link
        * edit shurten link
        * get clicks of shurten link
        * create custom shurten link
        * get help

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
  * method = "get_click"
  * password = "{Creator verification}"
  * shorten_link = "the shorten link"
  * link  = "{the new link}"

- Example

       params = {
		    "method" : "get_click",
            "password": "12345678",
            "shorten_link": "http://y-link.ml/LinkID",
            "link": "https://www.google.com"
		}

### custom
**create custom shurten link**

- params:
  * method = "custom"
  * password = "{Creator verification}"
  * token = "{token}... (api.php - line 132)"
  * path = "custom path link (e.g. y-link.ml/path)"
  * link  = "{the link}"

- Example

       params = {
		    "method" : "get_click",
            "password": "12345678",
            "token": "ABC123",
            "path": "custom"
            "link": "https://www.google.com"
		}

### help
**get help**

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
     - type	- "string (Up to 20)"
     - description - "creator id - to get num of clicks / edit the link"

* `link`
    - type - "valid link (string)"
    - description - "The Link..."

* `path` 
    - type - "string (Up to 20)"
    - description - "shorten link path: y-link.ml/path"

* `shorten_link`
    - type - "y-link.ml link (string)"
    - description - "api output shorten link"


# Code example
##python

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
- return
```
{
    'ok': True,
    'res': {
        'password': '1234', 
        'link': 'http://y-link.ml/rGUye3'
    }
}
```
