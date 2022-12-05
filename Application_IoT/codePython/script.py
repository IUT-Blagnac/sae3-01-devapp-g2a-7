import json
import paho.mqtt.client as mqtt

# Read the config file
def getConfig():
    res = open('config.json', "r", 0o444)
    return json.load(res)

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
        for key, value in config.items():
            if (value == True):
                print(f"{key}: {message['object'][key]}")
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