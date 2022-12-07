import json
import paho.mqtt.client as mqtt
import sys
import os
import signal

# get the path of the directory where the script is
pwd = sys.path[0]
# Data to write in data.json
data = {}

# Payload received from devices
payload = {}

# Read the config file
def get_config():
    global pwd
    try:
        res = ""
        file = os.open(f"{pwd}/config.json", os.O_RDONLY)
        reader = os.read(file, 1024)
        while len(reader) > 0:
            res += reader.decode("utf-8")
            reader = os.read(file, 1024)
        os.close(file)
        return json.loads(res)
    except FileNotFoundError:
        # If the config.json file does not exist
        return {}
    

# Write the data received in a data.json file
def write_data(data):
    global pwd
    file = os.open(f"{pwd}/data.json", os.O_WRONLY | os.O_TRUNC | os.O_CREAT, 0o666)
    os.write(file, json.dumps(data).encode("utf-8"))
    os.close(file)
    
# Fonction on connection
def on_connect(client, userdata, flags, rc):
    print(f"Connected with result code {rc}")
    # Subscribe to the device
    client.subscribe("application/1/device/+/event/up")

# When data is received
def on_message(client, userdata, msg):
    global payload
    payload = json.loads(msg.payload)

# When a signal of type SIGALRM is received
def on_alarm(signum, frame):
    print("Alarm received, 60 second, I'm writing data ...")
    global data, payload, config
    # Reload the config
    config = get_config()
    # Read the payload with the keys in the config
    for key, value in config.items():
        if value:
            data[key] = payload["object"][key]
        else:
            # if the key is false remove it if exists
            if key in data:
                del data[key]
    # Write the data in data.json file
    write_data(data)
    # Reset the timer of 60 seconds
    signal.alarm(60)

# Launch the client
client = mqtt.Client()

# Set  handlers
client.on_connect = on_connect
client.on_message = on_message
signal.signal(signal.SIGALRM, on_alarm)

# Get the config
config = get_config()

# Create a default data.json file if it does not exist
write_data(data)

# Connect to the server
client.connect("chirpstack.iut-blagnac.fr", 1883, 60)

# Start the timer of 60 seconds
signal.alarm(60)

# Infinity loop
client.loop_forever()