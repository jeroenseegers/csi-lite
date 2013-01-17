#!/bin/sh
chmod a+w /share
chmod -R 777 /share/Photo/_theme_
chmod -R 777 /share/Photo/_index_
chmod -R 777 /share/Photo/_waitimages_

# Check if the device is already determined
if [ ! -f device ]; then
    ./bin/getDeviceType > device
fi
