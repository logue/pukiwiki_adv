#!/bin/sh

# xgettext --from-code=UTF-8 -k_ -o $1.pot ../../plugin/$1.inc.php
xgettext -kT_gettext -kT_ --from-code utf-8 -o %1.pot ../../plugin/%1.inc.php -L PHP --no-wrap

