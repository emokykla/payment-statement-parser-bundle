#!/usr/bin/env bash

# save current dir
OLD_DIR=`pwd`
# get current script dir
if [ -z "${BASH_SOURCE[0]}" ]; then
    # when phpStorm calls script BASH_SOURCE will not be set
    APP_DIR=$(pwd)
else
    # http://stackoverflow.com/a/246128/846432
    DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
    APP_DIR=${DIR}/..
fi

cd ${APP_DIR} || exit 1

php -d memory_limit=-1 ${APP_DIR}/vendor/bin/phpstan analyse ${APP_DIR}/src
# get status code from phpstan process
phpstanProcessExitCode=$?
# restore saved dir
cd ${OLD_DIR} || exit 1
# return phpstan process code
exit ${phpstanProcessExitCode}
