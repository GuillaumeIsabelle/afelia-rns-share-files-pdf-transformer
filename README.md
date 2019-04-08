Guillaume Isabelle YOURLS-Plugin--Share-Files
==========================

Upload and share files with YOURLS

## INSTALL
* Sets variable
```php
$matt_jg_url = 'http://myaccessiblefileurl.com/fichiers/'; 
$matt_jg_uploaddir = '/mylocalpath/fichiers/'; //must be writable by APACHE
```

## DEPENDENCIES

### pdftotext
* Create a Text from input

* pdftotext
```bash
sudo apt-get update && sudo apt-get install -y xpdf
```
### PHP/Apache/Yourls
