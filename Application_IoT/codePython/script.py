import json
import paho.mqtt.client as mqtt
import sys

# get the path of the directory where the script is
pwd = sys.path[0]

# default config
defaultConfig = """{
    "activity": true,
    "co2": true,
    "humidity": true,
    "illumination": true,
    "infrared": true,
    "infrared_and_visible": true,
    "pressure": true,
    "temperature": true,
    "tvoc": true
}"""

# Check if the config.json exists. If not, create it
def checkConfigExist():
    global pwd
    global defaultConfig
    try:
        file = open(f"{pwd}/config.json", 'r', 0o444)
        file.close()
    except FileNotFoundError:
        file = open(f"{pwd}/config.json", 'w', 0o222)
        file.write(defaultConfig)
        file.close()

# Read the config file
def getConfig():
    global pwd
    res = open(f"{pwd}/config.json", 'r', 0o444)
    return json.load(res)

# Write the data received in a data.json file
def writeData(data):
    global pwd
    file = open(f"{pwd}/data.json", 'w', 0o222)
    file.write(json.dumps(data))
    file.close()
    
# Fonction on connection
def on_connect(client, userdata, flags, rc):
    print("Connected with result code "+str(rc))
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
checkConfigExist()
config = getConfig()

# Connect to the server
client.connect("chirpstack.iut-blagnac.fr", 1883, 60)

# Infinity loop
client.loop_forever()