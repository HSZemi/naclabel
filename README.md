# naclabel
Name label generator for NAC5 public viewing and Meet &amp; Greet

## Installation

Get a random 64-character string, for example by running `pwgen -s 64 1`.

Replace the SECRET in `api.php` and `grab-and-print.py` with that value.

Copy all php, html, and js files as well as the `data` folder to a webserver with PHP 8.2+.

Make sure the `data` directory is writable for your webserver process.


Run `grab-and-print.py` on the laptop that is connected to the printer.

Open `https://website#SECRET`, where website is the url of your website and SECRET is your secret value.
People can scan the QR code and generate their nametag.
