#!/bin/bash

if [ ! -f cache/initialized ]; then
    source config.sh
    cd ..

    # Run composer install
    composer install

    cd public

    bower install --allow-root

    cd ../build_scripts

    echo "CREATE DATABASE $MYSQL_DATABASE COLLATE utf8_general_ci;" | mysql -u$MYSQL_USERNAME -p$MYSQL_PASSWORD

    # Insert initial data
    for SQL in data/initial.*.sql ; do
        mysql -u$MYSQL_USERNAME -p$MYSQL_PASSWORD $MYSQL_DATABASE < $SQL
    done

    #./build.sh
    routines/migrate.sh

    touch cache/initialized
fi
