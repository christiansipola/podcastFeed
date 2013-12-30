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
FORCE=false
## $# is number of args
if [ $# != 0 ]; then
    if [ ${args[$INDEX]} ==  "-f" ]; then
      FORCE=true;
      let "INDEX+=1";
    fi
fi

if [ ${#args[$INDEX]} -gt 1 ]; then
  DATE=${args[$INDEX]} 
  let "INDEX+=1";
else
  #yesterday as default (=minus 1 day)
  DATE=`date -v-1d +%Y-%m-%d` 
fi

if [ ${#args[$INDEX]} -eq 1 ]; then
  PART=${args[$INDEX]} 
else
  #part 2 as defualt
  PART="2"
fi

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
BASE_IS_STREAM=0

if [ $PART == "1" ]; then
	BASE="http://lyssnaigen.sr.se/Autorec/ET2W/P3/Musikguiden_i_P3/${YEAR}/${MONTH}/SRP3_"
	START="180300"
elif [ $PART == "2" ]; then
	BASE="http://lyssnaigen.sr.se/Autorec/ET2W/P3/Musikguiden_i_P3/${YEAR}/${MONTH}/SRP3_"
	START="193000"
	#BASE="http://lyssna.sr.se/isidor/ereg/p3_stockholm/2013/10/7_musikguiden_i_p3_med_tina_2946983_a192.m4a"
	#BASE_IS_STREAM=1
elif [ $PART = "m" ]; then
	BASE="http://lyssnaigen.sr.se/Autorec/ET2W/P3/Musikguiden_i_P3/${YEAR}/${MONTH}/SRP3_"
	START="200600"
elif [ $PART = "s" ]; then
	#STREAM="http://lyssnaigen.sr.se/Autorec/P3/Musikguiden_i_P3/SRP3_${DATE}_182959_3602_a192.m4a"
	ARTIST="Luuk & Locko"
elif [ $PART = "p" ]; then
	#BASE="http://lyssnaigen.sr.se/Autorec/ET2W/P1/Sommar_i_P1/${YEAR}/${MONTH}/SRP1_"
	BASE="http://lyssnaigen.sr.se/Autorec/ET2W/P1/Vinter_i_P1/${YEAR}/${MONTH}/SRP1_"
	START="130000"
elif [ $PART = "q" ]; then
	#STREAM="http://lyssnaigen.sr.se/Autorec/ET2W/P1/Vinter_i_P1/${YEAR}/${MONTH}/SRP1_${DATE}_140000_1800_a192.m4a"
	echo "nothing to do. exit"
	exit 1
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

LIST00="1800 3600 5400 7200 9000 10800 12600"
LIST0030="5220"
LIST03="3420 5220 7020  8820"
LIST06="1440 3240"
LIST="5520"
STARTMINUTE=${START:2:4}
if [ $STARTMINUTE == "0000" ]; then
	LIST=$LIST00
elif [ $STARTMINUTE == "0030" ]; then
	LIST=$LIST0030
elif [ $STARTMINUTE == "0300" ]; then
	LIST=$LIST03
elif [ $STARTMINUTE == "0600" ]; then
  LIST=$LIST06
elif [ $STARTMINUTE == "3000" ]; then
  LIST=$LIST00
fi


for LENGTH in $LIST 
do
	echo "trying length ${LENGTH}"
	STREAM="${BASE}${DATE}_${START}_${LENGTH}_a192.m4a"
	if [ $BASE_IS_STREAM == 1 ]; then
	  STREAM=$BASE
	fi
	curl -f -v $STREAM -o $PIPE
	if [ $? != "22" ]; then
		break
	else
		echo "$LENGTH failed."
	fi
done
	

if [ $? == "22" ]; then
 echo "could not find show. exiting."
 ##exit
fi

## convert ##

ffmpeg -v 5 -y -i $PIPE -ac 2 -ab 192k "$FILE.mp3"

date

#os x
#say "beep beep"

## remove pipe ##
rm -f $PIPE

#user regular brackets here beacuse bash is wierd
if (( $PART == "p" || $PART=="q" )); then
        transfer
fi

echo "finished"
exit 0
