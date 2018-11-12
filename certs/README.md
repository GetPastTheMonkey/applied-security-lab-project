Creating certificates for new domain
====================================

Existing serial numbers
-----------------------

Do not use these serial numbers, as they are already used. If you create a new certificate for a new domain, please add the serial number to this list.

  * 1: ca.api.imovie.local
  * 2: certdata.api.imovie.local
  * 3: userdata.api.imovie.local
  * 4: www.imovie.local
  * 5: admin.imovie.local

How-to
------

  * Install php with `sudo apt-get install php`
  * Change your working directoy to `GIT-REPO/certs`
  * Open `newcert.php` with the editor of your choice
  * Change `$url`, `$serial`, `$length`
  * Run `php newcert.php`

After you run the script, a new directory is created (named after the `$url` you set). It contains three files:

  * `$url_cert.pem`: The certificate for the domain
  * `$url_pkey.pem`: The corresponding encrypted private key
  * `$url_encryptkey.txt`: A pseudo-random string of length `$length` that has been used to encrypt the private key in `$url_pkey.pem`.

