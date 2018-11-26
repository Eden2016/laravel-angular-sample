#!/bin/bash

if [ -f config.sh ]; then
    source config.sh

    cd ..
    php artisan migrate

    cd -
fi