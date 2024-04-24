#!/bin/bash

if which docker >/dev/null; then
    echo "Docker is installed."
else
    echo "Error: Docker is not installed." >&2
    exit 1
fi

if [ -n "$VALIDATION_LOG_LEVEL" ]
then
   env_log_level="-e VALIDATION_LOG_LEVEL=$VALIDATION_LOG_LEVEL"
else
   env_log_level=""
fi

class=$1;
method=$2;
sub_method_1=$3;
sub_method_2=$4;

scriptPath=$(readlink -f "$0")
scriptDirectory=$(dirname "$scriptPath")

echo ">> Test in docker container: php-5.6-cli-alpine";
# docker run -i --rm --name php-5.6-cli-alpine -v "$PWD":/validation -w /validation/tests php:5.6-cli-alpine php Test.php Unit run
docker run -i --rm $env_log_level --name php-5.6-cli-alpine -v "$scriptDirectory"/../:/validation -w /validation/tests php:5.6-cli-alpine php Test.php "$class" "$method" "$sub_method_1" "$sub_method_2"
returnValue=$?
if [ $returnValue -ne 0 ]; then
    exit 1
fi
echo "";


echo ">> Test in docker container: php-7.4.33-cli-alpine";
# docker run -i --rm --name php-7.4.33-cli-alpine -v "$PWD":/validation -w /validation/tests php:7.4.33-cli-alpine php Test.php Unit run
docker run -i --rm $env_log_level --name php-7.4.33-cli-alpine -v "$scriptDirectory"/../:/validation -w /validation/tests php:7.4.33-cli-alpine php Test.php "$class" "$method" "$sub_method_1" "$sub_method_2"
returnValue=$?
if [ $returnValue -ne 0 ]; then
    exit 1
fi
echo "";

echo ">> Test in docker container: php-latest";
# docker run -i --rm --name php-latest -v "$PWD":/validation -w /validation/tests php:latest php Test.php Unit run
docker run -i --rm $env_log_level --name php-latest -v "$scriptDirectory"/../:/validation -w /validation/tests php:latest php Test.php "$class" "$method" "$sub_method_1" "$sub_method_2"
returnValue=$?
if [ $returnValue -ne 0 ]; then
    exit 1
fi
echo "";