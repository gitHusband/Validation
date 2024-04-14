#!/bin/bash

if which docker >/dev/null; then
    echo "Docker is installed."
else
    echo "Error: Docker is not installed." >&2
    exit 1
fi

method=$1;
sub_method_1=$2;
sub_method_2=$3;

scriptPath=$(readlink -f "$0")
scriptDirectory=$(dirname "$scriptPath")

echo ">> Test in docker container: php-5.6-cli-alpine";
# docker run -i --rm --name php-5.6-cli-alpine -v "$PWD":/validation -w /validation/src/Test php:5.6-cli-alpine php Test.php Unit run
docker run -i --rm --name php-5.6-cli-alpine -v "$scriptDirectory"/../../:/validation -w /validation/src/Test php:5.6-cli-alpine php Test.php Unit "$method" "$sub_method_1" "$sub_method_2"
returnValue=$?
if [ $returnValue -ne 0 ]; then
    exit 1
fi
echo "";


echo ">> Test in docker container: php-7.4.33-cli-alpine";
# docker run -i --rm --name php-7.4.33-cli-alpine -v "$PWD":/validation -w /validation/src/Test php:7.4.33-cli-alpine php Test.php Unit run
docker run -i --rm --name php-7.4.33-cli-alpine -v "$scriptDirectory"/../../:/validation -w /validation/src/Test php:7.4.33-cli-alpine php Test.php Unit "$method" "$sub_method_1" "$sub_method_2"
returnValue=$?
if [ $returnValue -ne 0 ]; then
    exit 1
fi
echo "";

echo ">> Test in docker container: php-latest";
# docker run -i --rm --name php-latest -v "$PWD":/validation -w /validation/src/Test php:latest php Test.php Unit run
docker run -i --rm --name php-latest -v "$scriptDirectory"/../../:/validation -w /validation/src/Test php:latest php Test.php Unit "$method" "$sub_method_1" "$sub_method_2"
returnValue=$?
if [ $returnValue -ne 0 ]; then
    exit 1
fi
echo "";