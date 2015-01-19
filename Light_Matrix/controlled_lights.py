#!/usr/bin/python

import MySQLdb
import time
from Adafruit_LED_Backpack import Matrix8x8

width = 8
height = 8
matrix_size = width * height


# create a display and start it
display = Matrix8x8.Matrix8x8()
display.begin()

flicker = 0
flow = 0
led_matrix = []
run = 1
while run:

  # clear display
  display.clear()

  db = MySQLdb.connect(host = "localhost", user = "checker", passwd = "12345", db = "matrix_control")
  # cursor for db queries
  mat_cursor = db.cursor()
  flow_cursor = db.cursor()

  mat_cursor.execute("SELECT * FROM led_matrix")
  for row in mat_cursor.fetchall():
    led_matrix.append(row[1])

  # decide if to flow or flicker
  flow_cursor.execute("SELECT * FROM led_flow")
  for row in flow_cursor.fetchall():
    if row[0] == "flicker":
      if row[1] == 1:
        flicker = 1
      else:
        flicker = 0
    if row[0] == "flow": 
      if row[1] == 1:
        flow = 1
      else:
        flow = 0
  
  # internal loop to avoid accessing the db every other second
  for rep in range(0, 8): 
    time.sleep(0.3)

    # turn on/off pixels
    if flow == 0:
      for cell in range(0, matrix_size):
        x = cell % width
        y = cell // height

        display.set_pixel(y, x, led_matrix[cell])

      display.write_display()

    time.sleep(0.3)

    # flicker the leds
    if flicker == 1:
      for x in range(0, 8):
        for y in range(0, 8):
          display.set_pixel(x, y, 0)
      display.write_display()

    # move the leds
    if flow == 1:
      for cell in range(0, matrix_size):
        x = cell % width
        y = cell // height

        display.set_pixel(y, x + rep, led_matrix[cell])

      display.write_display()

  led_matrix = []
  db.close()

