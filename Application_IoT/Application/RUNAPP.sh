#!/bin/sh
JDK8="chemin_jre_1.8" # TO MODIFY
# Set the JAVA_HOME temporarily to the JDK8
JAVA_HOME=$JDK8
# Set the PATH to include the JDK8
PATH=$JAVA_HOME/bin:$PATH
# Get the path to the script folder
SCRIPT_PATH="$( cd "$(dirname "$0")" ; pwd -P )"
# Go to the folder where the application is located
cd $SCRIPT_PATH
#Start python Script situated in the same folder
python3 ./script.py &
# Set the JAVA_HOME to the JDK8
java -Dfile.encoding=UTF-8 -jar ApplicationIoT.jar
# Stop the python program
pkill -f script.py