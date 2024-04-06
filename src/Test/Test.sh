#!/bin/bash

if which docker >/dev/null; then
    echo "Docker is installed."
else
    echo "Error: Docker is not installed." >&2
    exit 1
fi

scriptPath=$(readlink -f "$0")
scriptDirectory=$(dirname "$scriptPath")

# docker run -i --rm --name php-5.6-cli-alpine -v "$PWD":/validation -w /validation/src/Test php:5.6-cli-alpine php Test.php Unit run
docker run -i --rm --name php-5.6-cli-alpine -v "$scriptDirectory"/../../:/validation -w /validation/src/Test php:5.6-cli-alpine php Test.php Unit run
returnValue=$?
if [ $returnValue -ne 0 ]; then
    exit 1
fi


# docker run -i --rm --name php-7.4.33-cli-alpine -v "$PWD":/validation -w /validation/src/Test php:7.4.33-cli-alpine php Test.php Unit run
docker run -i --rm --name php-7.4.33-cli-alpine -v "$scriptDirectory"/../../:/validation -w /validation/src/Test php:7.4.33-cli-alpine php Test.php Unit run
returnValue=$?
if [ $returnValue -ne 0 ]; then
    exit 1
fi

# docker run -i --rm --name php-latest -v "$PWD":/validation -w /validation/src/Test php:latest php Test.php Unit run
docker run -i --rm --name php-latest -v "$scriptDirectory"/../../:/validation -w /validation/src/Test php:latest php Test.php Unit run
returnValue=$?
if [ $returnValue -ne 0 ]; then
    exit 1
fi