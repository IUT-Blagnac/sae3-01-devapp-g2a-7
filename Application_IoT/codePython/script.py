import json
import paho.mqtt.client as mqtt
import sys
import os

# get the path of the directory where the script is
pwd = sys.path[0]

# Read the config file
def getConfig():
    global pwd
    res = ""
    file = os.open(f"{pwd}/config.json", os.O_RDONLY, 0o444)
    reader = os.read(file, 1024)
    while len(reader) > 0:
        res += reader.decode("utf-8")
        reader = os.read(file, 1024)
    os.close(file)
    return json.loads(res)
    

# Write the data received in a data.json file
def writeData(data):
    global pwd
    file = os.open(f"{pwd}/data.json", os.O_WRONLY | os.O_TRUNC | os.O_CREAT, 0o222)
    os.write(file, json.dumps(data).encode("utf-8"))
    os.close(file)
    
# Fonction on connection
def on_connect(client, userdata, flags, rc):
    print(f"Connected with result code {rc}")
    # Subscribe to the device
    client.subscribe("application/1/device/+/event/up")

# When data is recived
def on_message(client, userdata, msg):
    global config
    try:
        message = json.loads(msg.payload)
        data = {}
        for key, value in config.items():
            if (value == True):
                data[key] = message["object"][key]
        writeData(data)
    except Exception as e:
        print(e)

client = mqtt.Client()
client.on_connect = on_connect
client.on_message = on_message
config = getConfig()

# Connect to the server
client.connect("chirpstack.iut-blagnac.fr", 1883, 60)

# Infinity loop
client.loop_forever()