#!/bin/sh
JDK8="/usr/lib/jvm/jdk1.8.0_202"
# Set the JAVA_HOME temporarily to the JDK8
JAVA_HOME=$JDK8
# Set the PATH to include the JDK8
PATH=$JAVA_HOME/bin:$PATH
# Set the JAVA_HOME to the JDK8
java -Dfile.encoding=UTF-8 -jar ApplicationIoT.jar