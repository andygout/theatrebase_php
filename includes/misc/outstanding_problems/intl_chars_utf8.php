<?php
//http://stackoverflow.com/questions/5906585/change-database-collation
ALTER DATABASE theatrebase CHARACTER SET utf8 COLLATE utf8_unicode_ci;

//http://stackoverflow.com/questions/3513773/change-mysql-default-character-set-to-utf-8-in-my-cnf
SHOW VARIABLES LIKE 'char%';
show variables like 'collation%';

https://groups.google.com/forum/#!msg/ica-atom-users/fB6OarYEgik/ZPEEonthm_8J
mysql> charset utf8
Charset changed

mysql> SET character_set_server = utf8;
Query OK, 0 rows affected (0.00 sec)

mysql> SET collation_connection = utf8_unicode_ci;
Query OK, 0 rows affected (0.00 sec)

mysql> SET collation_server = utf8_unicode_ci;
Query OK, 0 rows affected (0.00 sec)

mysql> SHOW VARIABLES LIKE 'character_set%';
+--------------------------+-----------------------------------------------+
| Variable_name | Value |
+--------------------------+-----------------------------------------------+
| character_set_client | utf8 |
| character_set_connection | utf8 |
| character_set_database | utf8 |
| character_set_filesystem | binary |
| character_set_results | utf8 |
| character_set_server | utf8 |
| character_set_system | utf8 |
| character_sets_dir | c:\wamp\bin\mysql\mysql5.5.24\share\charsets\ |
+--------------------------+-----------------------------------------------+
8 rows in set (0.00 sec)

mysql> SHOW VARIABLES LIKE 'collation%';
+----------------------+-----------------+
| Variable_name | Value |
+----------------------+-----------------+
| collation_connection | utf8_unicode_ci |
| collation_database | utf8_unicode_ci |
| collation_server | utf8_unicode_ci |
+----------------------+-----------------+
3 rows in set (0.00 sec)
?>