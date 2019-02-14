Windowing Algorithm Implementation
==================================

Original code taken from: https://pastebin.com/NbCzDPj0?fbclid=IwAR1K5C_SfS-rz9BuIghg6Iu1XOBTvilRnwLrCA1EiMiZM8tQafzUMlpc-7E

Referer: https://www.facebook.com/notes/eko-heri-susanto/deteksi-plagiarisme-memanfaatkan-algoritma-winnowing/1069534186400026/

Usage
=====

```php
$windowing = new Windowing();
$result = $windowing->compare('foobar', 'foobaz');

echo $result->getCoefficient(); // prints

```

Example
=======
Access `windowing.php` via your browser.
