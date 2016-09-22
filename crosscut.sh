#! /bin/sh

#
# C R O S S C U T
#
# Cron job to log the current number of clients connected to an Icecast2 server
# to a MySQL database.
#

STATUS_URL="http://axeradio.acadiau.ca:8000/status2.xsl"
DB_HOST="localhost"
DB_USER=""
DB_PASS=""
DB_NAME="wordpress"
DB_TABLE="wp_timber_icecast_stats"

clients=`wget $STATUS_URL -q -O - | sed -n 5p | cut -d , -f 4`
date=`date +"%Y-%m-%d %H:%M:%S"`
echo "INSERT INTO ${DB_TABLE}(\`clients\`, \`date\`) " \
    "VALUES('${clients}', '${date}')" \
    | mysql -h $DB_HOST -u $DB_USER -p$DB_PASS $DB_NAME