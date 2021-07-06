#!/usr/bin/env python
import json as json_lib
import time, threading
import busio
import digitalio
import board
import adafruit_mcp3xxx.mcp3008 as MCP
import requests
import mysht21
from adafruit_mcp3xxx.analog_in import AnalogIn
from datetime import datetime
#predefined ports
port = {}
port[0] = {}
port[1] = {}
port[2] = {}
port[3] = {}
port[0]["Moisture"] = MCP.P7
port[0]["Temperature"] = MCP.P6
port[0]["Motor"] = 4
port[1]["Moisture"] = MCP.P5
port[1]["Temperature"] = MCP.P6
port[1]["Motor"] = 17
port[2]["Moisture"] = MCP.P3
port[2]["Temperature"] = MCP.P4
port[2]["Motor"] = 27
port[3]["Moisture"] = MCP.P1
port[3]["Temperature"] = MCP.P2
port[3]["Motor"] = 22
#getting all plants from mysql
r = requests.get('http://127.0.0.1/api/Plant/read.php', auth=('GardenWAM','XXXXXX'))
plants = r.json()
for x in plants:
	plants[plants.index(x)]["Port"] = port[int(plants[plants.index(x)]["Port"])]
#getting settings from mysql
for x in plants:
	headers = {'plant': plants[plants.index(x)]["Name"]}
	r = requests.get('http://127.0.0.1/api/Settings/read.php', auth=('GardenWAM','XXXXXX'), headers=headers)
	if(r.text != "0 results"):
		settings = r.json()
		plants[plants.index(x)]["Settings"] = {}
		for setting in settings:
			if(setting["SettingGroup"] == "Raspberry"):
				plants[plants.index(x)]["Settings"][setting["Name"]] = {}
				plants[plants.index(x)]["Settings"][setting["Name"]]["Value"] = setting["Current"]
				plants[plants.index(x)]["Settings"][setting["Name"]]["Unit"] = setting["Unit"]
# create the spi bus
spi = busio.SPI(clock=board.SCK, MISO=board.MISO, MOSI=board.MOSI)
# create the cs (chip select)
cs = digitalio.DigitalInOut(board.CE0)
# create the mcp object
mcp = MCP.MCP3008(spi, cs)
# create an analog input channel on pin 0
sensors = {}
#create sensor objects for each plant
for x in plants:
	sensors[plants[plants.index(x)]["Name"]] = {}
	for y in plants[plants.index(x)]["Port"]:
		if(y != "Motor"):
			sensors[plants[plants.index(x)]["Name"]][y] = AnalogIn(mcp, plants[plants.index(x)]["Port"][y])
#sht = mysht21.sht21()
#function which checks for changes in mysql database
def checker():
	lastTimeStamp = 0
	nextCheckerTime = 0
	global plants
	while True:
		CheckerTime = time.time()
		if(CheckerTime > nextCheckerTime):
			for x in plants:
				headers = {'plant': plants[plants.index(x)]["Name"], 'type': 'Raspberry'}
				r = requests.get('http://127.0.0.1/api/Information/read.php', auth=('GardenWAM','xxxxxxx'), headers=headers)
				if(r.text != "0 results"):
					info = r.json()
					timestamp = int(time.mktime(datetime.strptime(info["Time"], "%Y-%m-%d %H:%M:%S").timetuple()))
					#if there is a change in timestamps change current value in setting object
					if(timestamp != lastTimeStamp):
						headers = {'plant': plants[plants.index(x)]["Name"]}
						r = requests.get('http://127.0.0.1/api/Settings/read.php', auth=('GardenWAM','xxxxxxx'), headers=headers)
						data = {}
						if(r.text != "0 results"):
							settings = r.json()
							for setting in settings:
								if(setting["SettingGroup"] == "Raspberry"):
									if(setting["Current"] != plants[plants.index(x)]["Settings"][setting["Name"]]["Value"]):
										plants[plants.index(x)]["Settings"][setting["Name"]]["Value"] = setting["Current"]
						lastTimeStamp = timestamp
			nextCheckerTime = time.time()+1
			
		


#function for adding new data depended on settings
def addData():
	nextReadTime = time.time()+1
	nextWateringTime = time.time()+60
	nextDataTime = {}
	data = {}
	watering = False
	recentlyWatered = False
	for x in plants:
		print(plants[plants.index(x)]["Name"])
		if(plants[plants.index(x)]["Settings"]["Period"]["Value"] == "min"):
			nextDataTime[x["Name"]] = time.time() + int(plants[plants.index(x)]["Settings"]["Period"]["Value"])*60
		if(plants[plants.index(x)]["Settings"]["Period"]["Value"] == "s"):
			nextDataTime[x["Name"]] = time.time() + int(plants[plants.index(x)]["Settings"]["Period"]["Value"])
		data[plants.index(x)] = {}
		for y in sensors[plants[plants.index(x)]["Name"]]:
			if(y == "Moisture"):
				data[plants.index(x)][y] = (int((sensors[plants[plants.index(x)]["Name"]][y].value*(1023))/(65535)) - 1024)*(100-0)/(0-1024)+0
			if(y == "Temperature"):
				data[plants.index(x)][y] = ((int((sensors[plants[plants.index(x)]["Name"]][y].value*(1023))/(65535))*3.3)/1024)/0.01-273.15
	print(nextDataTime)
	while True:
		DataTime = time.time()
		for x in plants:
			if(DataTime > nextReadTime):
				AirTemp = sht.get_temp()
				Humidity = sht.get_humid()
				for y in sensors[plants[plants.index(x)]["Name"]]:
					if(y == "Moisture"):
						data[plants.index(x)][y] = (int((sensors[plants[plants.index(x)]["Name"]][y].value*(1023))/(65535)) - 230)*(100-0)/(700-230)+0
					if(y == "Temperature"):
						data[plants.index(x)][y] = ((int((sensors[plants[plants.index(x)]["Name"]][y].value*(1023))/(65535))*3.3)/1024)/0.01-273.15
				nextReadTime = time.time()+1
			if(DataTime > nextDataTime[x["Name"]]):
				headers = {'plant': plants[plants.index(x)]["Name"]}
				x = {'Moisture':round(data[plants.index(x)]["Moisture"],2),'Humidity':round(Humidity,2),'SoilTemp':round(round(data[plants.index(x)]["Temperature"],2),'AirTemp':round(AirTemp,2)}
				r = requests.post('http://127.0.0.1/api/Data/create.php', auth=('GardenWAM','xxxxxxx'),headers=headers, data=json_lib.dumps(x))
				headers = {'plant': plants[plants.index(x)]["Name"], 'type': 'Data'}
				r = requests.get('http://127.0.0.1/api/Information/update.php', auth=('GardenWAM','XXXXXX'), headers=headers)
				print("data updated for {}".format(plants[plants.index(x)]["Name"]))
				startTime = time.time()
				if(plants[plants.index(x)]["Settings"]["Period"]["Value"] == "min"):
					nextDataTime[x["Name"]] = time.time() + int(plants[plants.index(x)]["Settings"]["Period"]["Value"])*60
				if(plants[plants.index(x)]["Settings"]["Period"]["Value"] == "s"):
					nextDataTime[x["Name"]] = time.time() + int(plants[plants.index(x)]["Settings"]["Period"]["Value"])
			if(data[plants.index(x)]["Moisture"] < int(plants[plants.index(x)]["Settings"]["Low_moisture_level"]["Value"]) and recentlyWatered == False):
				nextWateringTime = time.time()+60
checkerThread = threading.Thread(target=checker, daemon = True)
addDataThread = threading.Thread(target=addData, daemon = True)
checkerThread.start()
addDataThread.start()
while True:
	pass
