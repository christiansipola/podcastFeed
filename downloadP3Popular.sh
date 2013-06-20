#!/bin/bash
#===============================================================================
#
#          FILE:  downloadP3Populär.sh
# 
#         USAGE:  ./downloadP3Populär.sh [-f] [date] [part]
# 		  P3popular part 1 or 2. m is p3 musik. -f forces recreation
#                 
# 
#   DESCRIPTION:  Download P3Populär
#	60 min podcast takes 33 in on MBP to download and convert (without pipe)
#	30 min podcast takes 16 min
#
#       NEEDS: extra codecs to ffmpeg
#sudo wget http://www.medibuntu.org/sources.list.d/`lsb_release -cs`.list --output-document=/etc/apt/sources.list.d/medibuntu.list && sudo apt-get -q update && sudo apt-get --yes -q --allow-unauthenticated install medibuntu-keyring && sudo apt-get -q update
#sudo apt-get install ffmpeg libavcodec-extra-52
#http://ubuntuforums.org/showthread.php?t=1117283
# 
#        AUTHOR:  Christian Sipola (CS), c@sipola.se
#       COMPANY:  Mangostorm HB
#       VERSION:  1.0
#       CREATED:  19.04.2009 17:43:31 CEST
#      REVISION:  ---
#===============================================================================

function transfer {

	REMOTE="/home/podcast/podcast/$FILE.$SUFFIX"
	if ssh podcast@vps.kaustik.com 'ls "'$REMOTE'" > /dev/null  2>/dev/null ' ; then
		echo "exist on vps"
	else
		echo "copy to vps"
		scp $FILE.$SUFFIX podcast@vps.kaustik.com:.
	fi
	

}


#move arg to array
INDEX=1;
for arg in "$@"  
do
  args[$INDEX]=$arg;
  let "INDEX+=1";
done  

INDEX=1;

if [ ${args[$INDEX]} ==  "-f" ]; then
  FORCE=true;
  let "INDEX+=1";
else
  FORCE=false;
fi

if [ ${#args[$INDEX]} -gt 1 ]; then
  DATE=${args[$INDEX]} 
  let "INDEX+=1";
else
  DATE=`date +%Y-%m-%d`
fi

if [ ${#args[$INDEX]} -eq 1 ]; then
  PART=${args[$INDEX]} 
else
  PART="1"
fi

#echo "$FORCE $DATE $PART"
#exit
##set manually
#DATE="2009-09-09"
#PART="1"
## Uncomment below for special shows
#DATE="2007-10-06"
#PART="s"
## UTF-8 does not seem to work 
ARTIST="P3Popular"
ALBUM="P3Popular"
TITLE="$DATE-$PART"
FILE="podcast-$DATE-$PART"
SUFFIX="mp3"

YEAR=${DATE:0:4}
MONTH=${DATE:5:2}
DAY=${DATE:8:2}

#%u day of week (1..7); 1 is Monday, j does not change date
WEEKDAY=`date -j -v${YEAR}y -v${MONTH}m -v${DAY}d +%u`
#WEEKDAY=5

if [ $PART == "1" ]; then
	 
	if [ $WEEKDAY == "1" ]; then
		#monday 
		STREAM="http://lyssnaigen.sr.se/Autorec/ET2W/P3/Musikguiden_i_P3/${YEAR}/${MONTH}/SRP3_${DATE}_193000_9000_a192.m4a"
		## on a monday 18 mars
		#STREAM="http://lyssnaigen.sr.se/Autorec/ET2W/P3/Musikguiden_i_P3/${YEAR}/${MONTH}/SRP3_${DATE}_193000_12600_a192.m4a"

	elif [ $WEEKDAY == "2" ]; then
		#tuesday
		STREAM="http://lyssnaigen.sr.se/Autorec/ET2W/P3/Musikguiden_i_P3/${YEAR}/${MONTH}/SRP3_${DATE}_193000_5400_a192.m4a"
			
	elif [ $WEEKDAY == "3" ]; then
		#wednesday
		STREAM="http://lyssnaigen.sr.se/Autorec/ET2W/P3/Musikguiden_i_P3/${YEAR}/${MONTH}/SRP3_${DATE}_193000_3600_a192.m4a"
		
	elif [ $WEEKDAY == "4" ]; then
		#thursday? 
		STREAM="http://lyssnaigen.sr.se/Autorec/ET2W/P3/Musikguiden_i_P3/${YEAR}/${MONTH}/SRP3_${DATE}_193000_9000_a192.m4a"
		
	else
		#used?
		#STREAM="http://lyssnaigen.sr.se/Autorec/ET2W/P3/Musikguiden_i_P3/${YEAR}/${MONTH}/SRP3_${DATE}_193000_7200_a192.m4a"
		STREAM="http://lyssnaigen.sr.se/Autorec/ET2W/P3/Musikguiden_i_P3/${YEAR}/${MONTH}/SRP3_${DATE}_193000_1800_a192.m4a"
		
	fi


elif [ $PART == "2" ]; then
	## TODO add variable for year and month
	#STREAM="http://lyssnaigen.sr.se/Autorec/ET2W/P3/Musikguiden_i_P3/${YEAR}/${MONTH}/SRP3_${DATE}_200600_3240_a192.m4a" #format 1
	STREAM="http://lyssnaigen.sr.se/Autorec/ET2W/P3/Musikguiden_i_P3/${YEAR}/${MONTH}/SRP3_${DATE}_200600_1440_a192.m4a" #format 2, seem to be on wednesdays

elif [ $PART = "m" ]; then
	#STREAM="rtsp://lyssna-rm.sr.se/autorec/p3/popnonstop/SRP3_${DATE}_130159_3482_r3.rm"
	#STREAM="http://lyssnaigen.sr.se/Autorec/P3/P3_Musik/SRP3_${DATE}_130159_3482_a192.m4a"
	#STREAM="http://lyssnaigen.sr.se/Autorec/P3/P3_Musik/SRP3_${DATE}_130159_3482_a192.m4a"
	#STREAM="http://lyssnaigen.sr.se/Autorec/P3/Musikguiden_i_P3/SRP3_${DATE}_120259_3422_a192.m4a"
	STREAM="http://lyssnaigen.sr.se/Autorec/ET2W/P3/Musikguiden_i_P3/${YEAR}/${MONTH}/SRP3_${DATE}_210300_3420_a192.m4a"
	#STREAM="http://lyssnaigen.sr.se/Autorec/ET2W/P3/Musikguiden_i_P3/2012/09/SRP3_${DATE}_130300_3420_a192.m4a"
elif [ $PART = "s" ]; then
	#STREAM="http://lyssnaigen.sr.se/Autorec/P3/Musikguiden_i_P3/SRP3_${DATE}_182959_3602_a192.m4a"
	ARTIST="Luuk & Locko"
elif [ $PART = "p" ]; then
	STREAM="http://lyssnaigen.sr.se/Autorec/P1/Sommar_i_P1/SRP1_${DATE}_125959_3602_a192.m4a"
	#STREAM="http://lyssnaigen.sr.se/Autorec/P1/Sommar_i_P1/SRP1_${DATE}_221159_2882_a192.m4a" 
	STREAM="http://lyssnaigen.sr.se/Autorec/ET2W/P1/Vinter_i_P1/${YEAR}/${MONTH}/SRP1_${DATE}_130000_3600_a192.m4a"
elif [ $PART = "q" ]; then
	STREAM="http://lyssnaigen.sr.se/Autorec/P1/Sommar_i_P1/SRP1_${DATE}_135959_1802_a192.m4a"
	#STREAM="http://lyssnaigen.sr.se/Autorec/P1/Sommar_i_P1/SRP1_${DATE}_225959_2402_a192.m4a"
	STREAM="http://lyssnaigen.sr.se/Autorec/ET2W/P1/Vinter_i_P1/${YEAR}/${MONTH}/SRP1_${DATE}_140000_1800_a192.m4a"
else
	echo "PART is wrong!"
	exit 1
fi

cd ~/git/unofficial/podcastFeed/radio
if [ -a "$FILE.$SUFFIX" ] ; then
	if [ "$FORCE" == "true" ] ; then
		echo "file $FILE.$SUFFIX already exist but it wil be overwritten!"
	else	
		echo "file $FILE.$SUFFIX already exist. Use -f to force overwrite."
		#user regular brackets here beacuse bash is wierd
		if (( $PART == "p" || $PART=="q" )); then
        		transfer
		fi
		exit 1
	fi
fi

PIPE="pipe_$TITLE"
if [ -p $PIPE ]; then
	echo "using existing named pipe"
elif [ -e $PIPE ]; then
	echo "file pipe exist and is not a named pipe!"
	#exit 1;
else
	#echo "creating named pipe"
	#mkfifo $PIPE #does not work with curl
	touch $PIPE
fi

## download ##

echo "starting to download and convert..."
date
## -f is fail silently when 404 and set exit status
curl -f -v $STREAM -o $PIPE


if [ $? == "22" ]; then
	START="1803"
	echo "could not find $STREAM. trying other lengths"
	
	LIST00="1800 3600 5400 7200 9000 10800 12600"
	LIST03="8820 7020 5220 3420"
	
	for LENGTH in $LIST03 
	do
		echo "trying length ${LENGTH}"
		STREAM="http://lyssnaigen.sr.se/Autorec/ET2W/P3/Musikguiden_i_P3/${YEAR}/${MONTH}/SRP3_${DATE}_${START}00_${LENGTH}_a192.m4a"
		curl -f -v $STREAM -o $PIPE
		if [ $? != "22" ]; then
			break
		else
			echo "$LENGTH failed."
		fi
	done
	
fi

if [ $? == "22" ]; then
 echo "could not find show. exiting."
 ##exit
fi

#wget $STREAM -O $PIPE &

## convert ##

ffmpeg -v 5 -y -i $PIPE -ac 2 -ab 192k "$FILE.mp3"

date

#os x
#say "beep beep"

#if [ ! -s "$FILE.mp3" ]; then
#	echo "filesize is more then 0"
#else
#	echo "filesize is 0. aborting!
#	exit 1
#fi


#not working os os x
#ffmpeg -v 5 -y -i $PIPE -acodec libmp3lame -ac 2 -ab 192k "$FILE.mp3"
#ffmpeg -v 5 -y -i $FILE.m4a -acodec libmp3lame -ac 2 -ab 192k "$FILE.mp3"
#ffmpeg -v 5 -y -i $PIPE -ac 2 -ab 192k "$FILE.mp3"

#afconvert -f m4af -v -d aac -b 128000 $PIPE "FILE.m4a"
#afconvert -f m4af -d aac $FILE.m4a "FILE-2.m4a"
#afconvert -f mp4f -v -d aac orig.m4a "FILE-2.m4a"

#ffmpeg -v 5 -y -i "p3Populär-2011-04-13-2.m4a" -acodec libmp3lame  -ac 2 -ab 192k "test.mp3"

## remove pipe ##
rm -f $PIPE

#user regular brackets here beacuse bash is wierd
if (( $PART == "p" || $PART=="q" )); then
        transfer
fi

echo "finished"
exit 0
#mplayer -quiet -vc null -vo null -ao pcm:file=$PIPE $STREAM &
#afconvert -f m4af -v -d aac -b 128000 pipe P3Populär-2009-04-07-1.m4a
#lame -b 192 --tt "$DATE-$PART" --ta "P3Popular" --tl "P3Popular" $PIPE "p3Populär-$DATE-$PART.mp3"
#lame -b 192 --tt "$TITLE" --ta "$ARTIST" --tl "$ALBUM" test "$FILE.mp3"
#lame --scale 3 -b 192 --tt "$TITLE" --ta "$ARTIST" --tl "$ALBUM" $PIPE $FILE.$SUFFIX 
#rm -f $PIPE

echo "finished!"
exit 0


