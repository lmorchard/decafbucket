#!/bin/bash
###########################################################################
# Daily entry rotation script
###########################################################################
BASE=$(dirname $(dirname $0));
ENTRIES=$BASE/data/entries;
NOW=`date +%Y-%m-%d`
NOW_FN=$ENTRIES/`date +%Y/%m/%d/%H_%M_%S.txt`; 
NOW_DIR=$(dirname $NOW_FN); 

mkdir -p $NOW_DIR; 
cat <<EOF >$NOW_FN;
// bucket for $NOW

* This space left unintentionally blank.

/* vim: set formatoptions=l lbr syntax=mkd: */
EOF

open $NOW_FN;
