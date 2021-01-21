#!/usr/bin/env bash

# Finds to-do items in the code and exit with error code if they are found.

# get current script dir
if [ -z "${BASH_SOURCE[0]}" ]; then
    # when phpStorm calls script BASH_SOURCE will not be set
    APP_DIR=$(pwd)
else
    # http://stackoverflow.com/a/246128/846432
    DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
    APP_DIR=${DIR}/..
fi

## Match to-do items that start with "#" or "@" or "//" and are followed by any number of spaces and are followed by "to-do" or "fix-me" (without dashes)
grep -r -i -n -E '(#|@|//)[ ]*(todo|fixme)' \
    ${APP_DIR}/app ${APP_DIR}/bin ${APP_DIR}/src ${APP_DIR}/tests

EXIT_CODE=$?
if [[ EXIT_CODE -eq 1 ]]; then
    echo No todos were found, great.
    exit 0
else
    echo There are todos left!
    exit 1
fi
