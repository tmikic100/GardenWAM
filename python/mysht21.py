import time
import unittest
import fcntl

class sht21:

	NO_HOLD_TEMP = 0xF3
	NO_HOLD_HUMIDITY = 0xF5
	SOFT_REST = 0xFE
	
	STATUS_MASK = 0xFC
	
	I2C_BUS = 1;
	I2C_SLAVE = 0x0703
	
	ADDRESS = 0x40	
	
	TEMPERATURE_WAIT_TIME = 0.086  # (datasheet: typ=66, max=85)
	HUMIDITY_WAIT_TIME = 0.030     # (datasheet: typ=22, max=29)
	
	def __init__(self, bus_num=I2C_BUS):
		self.i2c = open('/dev/i2c-%s' % bus_num, 'r+b', 0)
		fcntl.ioctl(self.i2c, self.I2C_SLAVE, 0x40)
		self.i2c.write(self.SOFT_REST.to_bytes(1, byteorder="little"))
		time.sleep(0.050)
	
	def get_temp(self):
		self.i2c.write(self.NO_HOLD_TEMP.to_bytes(1, byteorder="little"))
		time.sleep(self.TEMPERATURE_WAIT_TIME)
		data = self.i2c.read(2)
		data = int.from_bytes(data, "big")
		data /= 1 << 16
		data *= 175.72
		data -= 46.85
		return data
		
	def get_humid(self):
		self.i2c.write(self.NO_HOLD_HUMIDITY.to_bytes(1, byteorder="little"))
		time.sleep(self.HUMIDITY_WAIT_TIME)
		data = self.i2c.read(2)
		data = int.from_bytes(data, "big")
		data /= 1 << 16
		data *= 125
		data -= 6
		return data
	
	def close(self):
		"""Closes the i2c connection"""
		self.i2c.close()

	def __enter__(self):
		"""used to enable python's with statement support"""
		return self

	def __exit__(self, type, value, traceback):
		"""with support"""
		self.close()

if __name__ == "__main__":
	sht = sht21()
	print(sht.get_temp())
	print(sht.get_humid())
	