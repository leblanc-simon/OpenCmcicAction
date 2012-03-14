PROJECT
=======

This project allowed to send request to CMCIC admin interface.

This project use some components (symfony and sfWebBrowserPlugin), see README in the lib folder

USAGE
=====

Get all payments between 2 dates : 
```
php get_recurrent.php "2011-01-01" "2011-12-31"
```

Get the number of actives recurrents payments :
```
php get_nb_recurrent.php
```

Stop all active recurrents payments with more or equal x occurences :
```
php del_recurrent.php 12
```


LICENSE
=======

MIT License <http://www.opensource.org/licenses/mit-license.php>

AUTHORS
=======

* Simon Leblanc contact@leblanc-simon.eu
