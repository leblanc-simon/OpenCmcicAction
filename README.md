PROJECT
=======

This project allowed to send request to CMCIC admin interface.

This project use some components (symfony and sfWebBrowserPlugin), see README in the lib folder

USAGE
=====

Edit config.inc.php file to replace the CMCIC_* constants with your personnal value and launch a program :


Get all payments between 2 dates (begin date include and end date exclude) : 
```
php get_recurrent.php "2011-01-01" "2012-01-01"
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
