#!/usr/bin/env bash

TMPDIR=${TMPDIR-/tmp}
TMPDIR=$(echo $TMPDIR | sed -e "s/\/$//")
RELEASE_VERSION=${1-master}
DOWNLOAD_LOCATION=${2-$HOME/tmp}
FILE_NAME=${3-convertkit-gravity-forms}

download() {
    curl -s "$1" > "$2";
}

download https://github.com/ConvertKit/convertkit-gravity-forms/archive/$RELEASE_VERSION.zip $DOWNLOAD_LOCATION/$FILE_NAME.zip

cd $DOWNLOAD_LOCATION

unzip $FILE_NAME.zip

rm $FILE_NAME.zip

NEW_DIR=$(echo $RELEASE_VERSION | sed -e 's/\//-/g')
cd $FILE_NAME-$NEW_DIR

if [ -e composer.json ]
then
    composer install --no-dev
fi

if [ -e .distignore ]
then
    wp dist-archive ./ $DOWNLOAD_LOCATION/$FILE_NAME-packaged.zip
fi

