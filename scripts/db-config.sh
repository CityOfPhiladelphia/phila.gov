#!/bin/sh

source /home/ubuntu/.ssh/environment

echo '$REPLICA_DB_HOST' > '/home/ubuntu/app/wp/test.txt'

# echo "'host'     => $DB_HOST,
# 	'user'     => $DB_USER,
# 	'password' => $DB_PASS,
# 	'name'     => 'wp',
# 	'write'    => 1,
# 	'read'     => 0,
# ) );
# /**
#  * Register replica database server if it's available in this environment
#  */
# if ( ! empty( $REPLICA_DB_HOST ) ) {"  >> '/home/ubuntu/app/wp/db-config.php'

# echo '$wpdb->add_database(array(' >> '/home/ubuntu/app/wp/db-config.php'

# echo "'host'     => $REPLICA_DB_HOST,
# 	'user'     => $DB_USER,
# 	'password' => $DB_PASS,
# 	'name'     => 'wp',
# 	'write'    => 0,
# 	'read'     => 1,
# 	));
# }
# " >> '/home/ubuntu/app/wp/db-config.php'