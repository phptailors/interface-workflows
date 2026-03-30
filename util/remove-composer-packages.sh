#!/usr/bin/env bash

set -e

here="$(dirname $0)";
top="$(dirname $here)";

( cd "$top" && rm -rf {.,vendor-bin/*}/{vendor,composer.lock});
