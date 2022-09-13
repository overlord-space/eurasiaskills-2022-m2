#pragma once

#include "Global.h"
#include "MqttClient.h"

boolean isNetworkActive();
void routerConnect();
bool startAPMode();
boolean RouterFind(String ssid);
uint8_t RSSIquality();
extern void wifiSignalInit();
