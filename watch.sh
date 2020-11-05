#!/bin/bash
cd ./pid

for i in `ls`;
do

count=`ps -ef | grep $i | grep -v "grep" | wc -l`

if [ 0 -eq $count ]
then
  php run.php $i --daemon
fi
done