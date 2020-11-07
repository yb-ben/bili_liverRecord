#!/bin/bash
rootpath=/usr/local/bili_liverRecord
pidpath=$rootpath/pid

if [ ! -d $pidpath ];then
mkdir $pidpath
fi

cd $rootpath

for i in `ls $pidpath`;
do

count=`ps -ef | grep $i | grep -v "grep" | wc -l`

if [ 0 -eq $count ];
then
  /usr/local/bin/php run.php $i --daemon
fi
done

echo  `date "+%Y-%m-%d %H:%M:%S"`
echo -e "finish a loop"