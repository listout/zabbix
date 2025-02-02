
# Cisco Meraki dashboard by HTTP

## Overview

This template is designed for the effortless deployment of Cisco Meraki dashboard monitoring by Zabbix via HTTP and doesn't require any external scripts.

## Requirements

Zabbix version: 7.0 and higher.

## Tested versions

This template has been tested on:
- Cisco Meraki API 1.24.0 

## Configuration

> Zabbix should be configured according to the instructions in the [Templates out of the box](https://www.zabbix.com/documentation/7.0/manual/config/templates_out_of_the_box) section.

## Setup

You must set {$MERAKI.TOKEN} and {$MERAKI.API.URL} macros. 

Create the token in the Meraki dashboard (see Meraki [documentation](https://developer.cisco.com/meraki/api-latest/#!authorization/authorization) for instructions). Set this token as {$MERAKI.TOKEN} macro value in Zabbix.

Set your Meraki dashboard URL as {$MERAKI.API.URL} macro value in Zabbix (e.g., api.meraki.com/api/v1).

Set filters with macros if you want to override default filter parameters.


### Macros used

|Name|Description|Default|
|----|-----------|-------|
|{$MERAKI.TOKEN}|<p>Cisco Meraki dashboard API token.</p>||
|{$MERAKI.API.URL}|<p>Cisco Meraki dashboard API URL, e.g., api.meraki.com/api/v1</p>|`api.meraki.com/api/v1`|
|{$MERAKI.ORGANIZATION.NAME.MATCHES}|<p>This macro is used in organizations discovery. Can be overridden on the host or linked template level.</p>|`.+`|
|{$MERAKI.ORGANIZATION.NAME.NOT_MATCHES}|<p>This macro is used in organizations discovery. Can be overridden on the host or linked template level.</p>|`CHANGE_IF_NEEDED`|
|{$MERAKI.DEVICE.NAME.MATCHES}|<p>This macro is used in devices discovery. Can be overridden on the host or linked template level.</p>|`.+`|
|{$MERAKI.DEVICE.NAME.NOT_MATCHES}|<p>This macro is used in devices discovery. Can be overridden on the host or linked template level.</p>|`CHANGE_IF_NEEDED`|
|{$MERAKI.DEVICE.STATUS.MATCHES}|<p>This macro is used in devices discovery. Can be overridden on the host or linked template level.</p>|`.*`|
|{$MERAKI.DEVICE.STATUS.NOT_MATCHES}|<p>This macro is used in devices discovery. Can be overridden on the host or linked template level.</p>|`CHANGE_IF_NEEDED`|
|{$MERAKI.HTTP_PROXY}|<p>HTTP proxy for API requests. You can specify it using the format [protocol://][username[:password]@]proxy.example.com[:port]. See documentation at https://www.zabbix.com/documentation/7.0/manual/config/items/itemtypes/http</p>||

### Items

|Name|Description|Type|Key and additional info|
|----|-----------|----|-----------------------|
|Meraki: Get data|<p>Item for gathering all the organizations and devices from Meraki API.</p>|Script|meraki.get.data|
|Meraki: Data item errors|<p>Item for gathering all the data item errors.</p>|Dependent item|meraki.get.data.errors<p>**Preprocessing**</p><ul><li><p>JSON Path: `$.error`</p></li><li><p>Discard unchanged with heartbeat: `1h`</p></li></ul>|

### Triggers

|Name|Description|Expression|Severity|Dependencies and additional info|
|----|-----------|----------|--------|--------------------------------|
|Meraki: There are errors in 'Get data' metric||`length(last(/Cisco Meraki dashboard by HTTP/meraki.get.data.errors))>0`|Warning||

### LLD rule Organizations discovery

|Name|Description|Type|Key and additional info|
|----|-----------|----|-----------------------|
|Organizations discovery||Dependent item|meraki.organization.discovery<p>**Preprocessing**</p><ul><li><p>JSON Path: `$.organizations`</p></li></ul>|

### LLD rule Devices discovery

|Name|Description|Type|Key and additional info|
|----|-----------|----|-----------------------|
|Devices discovery||Dependent item|meraki.devices.discovery<p>**Preprocessing**</p><ul><li><p>JSON Path: `$.devices`</p></li></ul>|

# Cisco Meraki organization by HTTP

### Macros used

|Name|Description|Default|
|----|-----------|-------|
|{$MERAKI.TOKEN}|<p>Cisco Meraki dashboard API token.</p>||
|{$MERAKI.API.URL}|<p>Cisco Meraki dashboard API URL, e.g., api.meraki.com/api/v1</p>|`api.meraki.com/api/v1`|
|{$MERAKI.LICENSE.EXPIRE}|<p>Time in seconds for license to expire.</p>|`86400`|
|{$MERAKI.CONFIG.CHANGE.TIMESPAN}|<p>Timespan for gathering configuration change log. Used in the metric configuration and in the URL query.</p>|`1200`|
|{$MERAKI.HTTP_PROXY}|<p>HTTP proxy for API requests. You can specify it using the format [protocol://][username[:password]@]proxy.example.com[:port]. See documentation at https://www.zabbix.com/documentation/7.0/manual/config/items/itemtypes/http</p>||
|{$MERAKI.LLD.UPLINK.NETWORK.NAME.MATCHES}|<p>This macro is used in uplinks discovery. Can be overridden on the host or linked template level.</p>|`.*`|
|{$MERAKI.LLD.UPLINK.NETWORK.NAME.NOT_MATCHES}|<p>This macro is used in uplinks discovery. Can be overridden on the host or linked template level.</p>|`CHANGE_IF_NEEDED`|
|{$MERAKI.LLD.UPLINK.ROLE.MATCHES}|<p>This macro is used in uplinks discovery. Can be overridden on the host or linked template level.</p>|`.*`|
|{$MERAKI.LLD.UPLINK.ROLE.NOT_MATCHES}|<p>This macro is used in uplinks discovery. Can be overridden on the host or linked template level.</p>|`CHANGE_IF_NEEDED`|
|{$MERAKI.LLD.VPN.NETWORK.NAME.MATCHES}|<p>This macro is used in VPN stats discovery. Can be overridden on the host or linked template level.</p>|`.*`|
|{$MERAKI.LLD.VPN.NETWORK.NAME.NOT_MATCHES}|<p>This macro is used in VPN stats discovery. Can be overridden on the host or linked template level.</p>|`CHANGE_IF_NEEDED`|
|{$MERAKI.LLD.VPN.PEER.NETWORK.NAME.MATCHES}|<p>This macro is used in VPN stats discovery. Can be overridden on the host or linked template level.</p>|`.*`|
|{$MERAKI.LLD.VPN.PEER.NETWORK.NAME.NOT_MATCHES}|<p>This macro is used in VPN stats discovery. Can be overridden on the host or linked template level.</p>|`CHANGE_IF_NEEDED`|
|{$MERAKI.LLD.VPN.SENDER.UPLINK.MATCHES}|<p>This macro is used in VPN stats discovery. Can be overridden on the host or linked template level.</p>|`.*`|
|{$MERAKI.LLD.VPN.SENDER.UPLINK.NOT_MATCHES}|<p>This macro is used in VPN stats discovery. Can be overridden on the host or linked template level.</p>|`CHANGE_IF_NEEDED`|
|{$MERAKI.LLD.VPN.RECEIVER.UPLINK.MATCHES}|<p>This macro is used in VPN stats discovery. Can be overridden on the host or linked template level.</p>|`.*`|
|{$MERAKI.LLD.VPN.RECEIVER.UPLINK.NOT_MATCHES}|<p>This macro is used in VPN stats discovery. Can be overridden on the host or linked template level.</p>|`CHANGE_IF_NEEDED`|

### Items

|Name|Description|Type|Key and additional info|
|----|-----------|----|-----------------------|
|Meraki: Get list of the networks|<p>Item for gathering all the networks of organization from Meraki API.</p>|Script|meraki.get.networks|
|Meraki: Networks item errors|<p>Item for gathering all the networks item errors.</p>|Dependent item|meraki.get.networks.errors<p>**Preprocessing**</p><ul><li><p>JSON Path: `$.error`</p></li><li><p>Discard unchanged with heartbeat: `1h`</p></li></ul>|
|Meraki: Get list of the VPN stats|<p>Item for gathering all the VPN stats of the organization.</p>|Script|meraki.get.vpn.stats|
|Meraki: VPN item errors|<p>Item for gathering all the VPN item errors.</p>|Dependent item|meraki.get.vpn.stats.errors<p>**Preprocessing**</p><ul><li><p>JSON Path: `$.error`</p></li><li><p>Discard unchanged with heartbeat: `1h`</p></li></ul>|
|Meraki: Get list of configuration changes|<p>Item for viewing the change log for your organization. Gathering once per 20m by default.</p>|HTTP agent|meraki.get.configuration.changes<p>**Preprocessing**</p><ul><li><p>Discard unchanged with heartbeat: `2h`</p></li></ul>|
|Meraki: Get list of adaptive policy aggregate statistics|<p>Item for adaptive policy aggregate statistics for an organization.</p>|HTTP agent|meraki.get.adaptive.policy|
|Meraki: Groups|<p>Meraki adaptive policy groups count.</p>|Dependent item|meraki.policies.groups<p>**Preprocessing**</p><ul><li><p>JSON Path: `$.counts.groups`</p></li></ul>|
|Meraki: Custom ACLs|<p>Meraki adaptive policy custom ACLs count.</p>|Dependent item|meraki.policies.custom.acls<p>**Preprocessing**</p><ul><li><p>JSON Path: `$.counts.customAcls`</p></li></ul>|
|Meraki: Policies|<p>Meraki adaptive policies count.</p>|Dependent item|meraki.policies<p>**Preprocessing**</p><ul><li><p>JSON Path: `$.counts.policies`</p></li></ul>|
|Meraki: Allow policies|<p>Meraki adaptive allow policies count.</p>|Dependent item|meraki.policies.allow<p>**Preprocessing**</p><ul><li><p>JSON Path: `$.counts.allowPolicies`</p></li></ul>|
|Meraki: Deny policies|<p>Meraki adaptive deny policies count.</p>|Dependent item|meraki.policies.deny<p>**Preprocessing**</p><ul><li><p>JSON Path: `$.counts.denyPolicies`</p></li></ul>|
|Meraki: Get licenses info|<p>Return an overview of the license state for an organization.</p>|HTTP agent|meraki.get.licenses|
|Meraki: License status|<p>Meraki license status.</p>|Dependent item|meraki.license.status<p>**Preprocessing**</p><ul><li><p>JSON Path: `$.status`</p></li><li><p>JavaScript: `The text is too long. Please see the template.`</p></li></ul>|
|Meraki: License expire|<p>Meraki license expire time, in seconds left.</p>|Dependent item|meraki.license.expire<p>**Preprocessing**</p><ul><li><p>JSON Path: `$.expirationDate`</p></li><li><p>JavaScript: `The text is too long. Please see the template.`</p></li></ul>|

### Triggers

|Name|Description|Expression|Severity|Dependencies and additional info|
|----|-----------|----------|--------|--------------------------------|
|Meraki: There are errors in 'Get networks' metric||`length(last(/Cisco Meraki organization by HTTP/meraki.get.networks.errors))>0`|Warning||
|Meraki: There are errors in 'Get VPNs' metric||`length(last(/Cisco Meraki organization by HTTP/meraki.get.vpn.stats.errors))>0`|Warning||
|Meraki: Configuration has been changed||`length(last(/Cisco Meraki organization by HTTP/meraki.get.configuration.changes))>3`|Warning||
|Meraki: License status is not OK||`last(/Cisco Meraki organization by HTTP/meraki.license.status)<>1`|Warning||
|Meraki: License expires in less than {$MERAKI.LICENSE.EXPIRE} seconds||`last(/Cisco Meraki organization by HTTP/meraki.license.expire)<{$MERAKI.LICENSE.EXPIRE} and last(/Cisco Meraki organization by HTTP/meraki.license.expire)>=0`|Warning||

### LLD rule Uplinks discovery

|Name|Description|Type|Key and additional info|
|----|-----------|----|-----------------------|
|Uplinks discovery||Dependent item|meraki.uplinks.discovery<p>**Preprocessing**</p><ul><li><p>JSON Path: `$.uplinks`</p></li></ul>|

### Item prototypes for Uplinks discovery

|Name|Description|Type|Key and additional info|
|----|-----------|----|-----------------------|
|Uplink [{#INTERFACE}]: [{#UPLINK.ROLE}]: [{#NETWORK.NAME}]: status|<p>Network uplink status.</p>|Dependent item|meraki.uplink.status[{#NETWORK.NAME}, {#INTERFACE}, {#UPLINK.ROLE}]<p>**Preprocessing**</p><ul><li><p>JSON Path: `The text is too long. Please see the template.`</p></li><li><p>JavaScript: `The text is too long. Please see the template.`</p></li></ul>|

### Trigger prototypes for Uplinks discovery

|Name|Description|Expression|Severity|Dependencies and additional info|
|----|-----------|----------|--------|--------------------------------|
|Uplink [{#INTERFACE}]: [{#UPLINK.ROLE}]: [{#NETWORK.NAME}]: status is failed||`last(/Cisco Meraki organization by HTTP/meraki.uplink.status[{#NETWORK.NAME}, {#INTERFACE}, {#UPLINK.ROLE}])=0`|Warning||

### LLD rule VPN stats discovery

|Name|Description|Type|Key and additional info|
|----|-----------|----|-----------------------|
|VPN stats discovery||Dependent item|meraki.vpn.stats.discovery<p>**Preprocessing**</p><ul><li><p>JSON Path: `$.vpnStats`</p></li></ul>|

### Item prototypes for VPN stats discovery

|Name|Description|Type|Key and additional info|
|----|-----------|----|-----------------------|
|VPN [{#NETWORK.NAME}]=>[{#PEER.NETWORK.NAME}]: stats raw|<p>VPN connection stats raw.</p>|Dependent item|meraki.vpn.stat.raw[{#NETWORK.ID}, {#SENDER.UPLINK}, {#PEER.NETWORK.ID}, {#RECEIVER.UPLINK}]<p>**Preprocessing**</p><ul><li><p>JSON Path: `The text is too long. Please see the template.`</p></li></ul>|
|VPN [{#NETWORK.NAME}][{#SENDER.UPLINK}]=>[{#PEER.NETWORK.NAME}][{#RECEIVER.UPLINK}]: latency avg|<p>VPN connection avg latency.</p>|Dependent item|meraki.vpn.stat.latency.avg[{#NETWORK.ID}, {#SENDER.UPLINK}, {#PEER.NETWORK.ID}, {#RECEIVER.UPLINK}]<p>**Preprocessing**</p><ul><li><p>JSON Path: `$.avgLatencyMs`</p></li></ul>|
|VPN [{#NETWORK.NAME}][{#SENDER.UPLINK}]=>[{#PEER.NETWORK.NAME}][{#RECEIVER.UPLINK}]: latency min|<p>VPN connection min latency.</p>|Dependent item|meraki.vpn.stat.latency.min[{#NETWORK.ID}, {#SENDER.UPLINK}, {#PEER.NETWORK.ID}, {#RECEIVER.UPLINK}]<p>**Preprocessing**</p><ul><li><p>JSON Path: `$.minLatencyMs`</p></li></ul>|
|VPN [{#NETWORK.NAME}][{#SENDER.UPLINK}]=>[{#PEER.NETWORK.NAME}][{#RECEIVER.UPLINK}]: latency max|<p>VPN connection max latency.</p>|Dependent item|meraki.vpn.stat.latency.max[{#NETWORK.ID}, {#SENDER.UPLINK}, {#PEER.NETWORK.ID}, {#RECEIVER.UPLINK}]<p>**Preprocessing**</p><ul><li><p>JSON Path: `$.maxLatencyMs`</p></li></ul>|
|VPN [{#NETWORK.NAME}][{#SENDER.UPLINK}]=>[{#PEER.NETWORK.NAME}][{#RECEIVER.UPLINK}]: loss avg, %|<p>VPN connection loss avg.</p>|Dependent item|meraki.vpn.stat.loss.avg[{#NETWORK.ID}, {#SENDER.UPLINK}, {#PEER.NETWORK.ID}, {#RECEIVER.UPLINK}]<p>**Preprocessing**</p><ul><li><p>JSON Path: `$.avgLossPercentage`</p></li></ul>|
|VPN [{#NETWORK.NAME}][{#SENDER.UPLINK}]=>[{#PEER.NETWORK.NAME}][{#RECEIVER.UPLINK}]: loss min, %|<p>VPN connection loss min.</p>|Dependent item|meraki.vpn.stat.loss.min[{#NETWORK.ID}, {#SENDER.UPLINK}, {#PEER.NETWORK.ID}, {#RECEIVER.UPLINK}]<p>**Preprocessing**</p><ul><li><p>JSON Path: `$.minLossPercentage`</p></li></ul>|
|VPN [{#NETWORK.NAME}][{#SENDER.UPLINK}]=>[{#PEER.NETWORK.NAME}][{#RECEIVER.UPLINK}]: loss max, %|<p>VPN connection loss max.</p>|Dependent item|meraki.vpn.stat.loss.max[{#NETWORK.ID}, {#SENDER.UPLINK}, {#PEER.NETWORK.ID}, {#RECEIVER.UPLINK}]<p>**Preprocessing**</p><ul><li><p>JSON Path: `$.maxLossPercentage`</p></li></ul>|
|VPN [{#NETWORK.NAME}][{#SENDER.UPLINK}]=>[{#PEER.NETWORK.NAME}][{#RECEIVER.UPLINK}]: jitter avg|<p>VPN connection jitter avg.</p>|Dependent item|meraki.vpn.stat.jitter.avg[{#NETWORK.ID}, {#SENDER.UPLINK}, {#PEER.NETWORK.ID}, {#RECEIVER.UPLINK}]<p>**Preprocessing**</p><ul><li><p>JSON Path: `$.avgJitter`</p></li></ul>|
|VPN [{#NETWORK.NAME}][{#SENDER.UPLINK}]=>[{#PEER.NETWORK.NAME}][{#RECEIVER.UPLINK}]: jitter min|<p>VPN connection jitter min.</p>|Dependent item|meraki.vpn.stat.jitter.min[{#NETWORK.ID}, {#SENDER.UPLINK}, {#PEER.NETWORK.ID}, {#RECEIVER.UPLINK}]<p>**Preprocessing**</p><ul><li><p>JSON Path: `$.minJitter`</p></li></ul>|
|VPN [{#NETWORK.NAME}][{#SENDER.UPLINK}]=>[{#PEER.NETWORK.NAME}][{#RECEIVER.UPLINK}]: jitter max|<p>VPN connection jitter max.</p>|Dependent item|meraki.vpn.stat.jitter.max[{#NETWORK.ID}, {#SENDER.UPLINK}, {#PEER.NETWORK.ID}, {#RECEIVER.UPLINK}]<p>**Preprocessing**</p><ul><li><p>JSON Path: `$.maxJitter`</p></li></ul>|
|VPN [{#NETWORK.NAME}][{#SENDER.UPLINK}]=>[{#PEER.NETWORK.NAME}][{#RECEIVER.UPLINK}]: mos avg|<p>VPN connection mos avg.</p>|Dependent item|meraki.vpn.stat.mos.avg[{#NETWORK.ID}, {#SENDER.UPLINK}, {#PEER.NETWORK.ID}, {#RECEIVER.UPLINK}]<p>**Preprocessing**</p><ul><li><p>JSON Path: `$.avgMos`</p></li></ul>|
|VPN [{#NETWORK.NAME}][{#SENDER.UPLINK}]=>[{#PEER.NETWORK.NAME}][{#RECEIVER.UPLINK}]: mos min|<p>VPN connection mos min.</p>|Dependent item|meraki.vpn.stat.mos.min[{#NETWORK.ID}, {#SENDER.UPLINK}, {#PEER.NETWORK.ID}, {#RECEIVER.UPLINK}]<p>**Preprocessing**</p><ul><li><p>JSON Path: `$.minMos`</p></li></ul>|
|VPN [{#NETWORK.NAME}][{#SENDER.UPLINK}]=>[{#PEER.NETWORK.NAME}][{#RECEIVER.UPLINK}]: mos max|<p>VPN connection mos max.</p>|Dependent item|meraki.vpn.stat.mos.max[{#NETWORK.ID}, {#SENDER.UPLINK}, {#PEER.NETWORK.ID}, {#RECEIVER.UPLINK}]<p>**Preprocessing**</p><ul><li><p>JSON Path: `$.maxMos`</p></li></ul>|

# Cisco Meraki device by HTTP

### Macros used

|Name|Description|Default|
|----|-----------|-------|
|{$MERAKI.TOKEN}|<p>Cisco Meraki dashboard API token.</p>||
|{$MERAKI.API.URL}|<p>Cisco Meraki dashboard API URL, e.g., api.meraki.com/api/v1</p>|`api.meraki.com/api/v1`|
|{$MERAKI.DEVICE.LOSS}|<p>Devices uplink loss threshold, in percents.</p>|`15`|
|{$MERAKI.DEVICE.LATENCY}|<p>Devices uplink latency threshold, in seconds.</p>|`0.15`|
|{$MERAKI.HTTP_PROXY}|<p>HTTP proxy for API requests. You can specify it using the format [protocol://][username[:password]@]proxy.example.com[:port]. See documentation at https://www.zabbix.com/documentation/7.0/manual/config/items/itemtypes/http</p>||
|{$MERAKI.DEVICE.UPLINK.MATCHES}|<p>This macro is used in loss and latency checks discovery. Can be overridden on the host or linked template level.</p>|`.*`|
|{$MERAKI.DEVICE.UPLINK.NOT_MATCHES}|<p>This macro is used in loss and latency checks discovery. Can be overridden on the host or linked template level.</p>|`^null$`|
|{$MERAKI.DEVICE.LOSS.LATENCY.IP.MATCHES}|<p>This macro is used in loss and latency checks discovery. Can be overridden on the host or linked template level.</p>|`^((25[0-5]\|(2[0-4]\|1\d\|[1-9]\|)\d)\.?\b){4}$`|
|{$MERAKI.DEVICE.LOSS.LATENCY.IP.NOT_MATCHES}|<p>This macro is used in loss and latency checks discovery. Can be overridden on the host or linked template level.</p>|`^null$`|

### Items

|Name|Description|Type|Key and additional info|
|----|-----------|----|-----------------------|
|Meraki: Get device data|<p>Item for gathering device data from Meraki API.</p>|Script|meraki.get.device|
|Meraki: Device data item errors|<p>Item for gathering errors of the device item.</p>|Dependent item|meraki.get.device.errors<p>**Preprocessing**</p><ul><li><p>JSON Path: `$.error`</p></li><li><p>Discard unchanged with heartbeat: `1h`</p></li></ul>|
|Meraki: status|<p>Device operational status</p><p>Network: {$NETWORK.ID} </p><p>MAC: {$MAC}</p>|Dependent item|meraki.device.status<p>**Preprocessing**</p><ul><li><p>JSON Path: `$.device[0].status`</p></li><li><p>JavaScript: `The text is too long. Please see the template.`</p></li></ul>|
|Meraki: public IP|<p>Device public IP</p><p>Network: {$NETWORK.ID} </p><p>MAC: {$MAC}</p>|Dependent item|meraki.device.public.ip<p>**Preprocessing**</p><ul><li><p>JSON Path: `$.device[0].publicIp`</p></li></ul>|

### Triggers

|Name|Description|Expression|Severity|Dependencies and additional info|
|----|-----------|----------|--------|--------------------------------|
|Meraki: There are errors in 'Get Device data' metric||`length(last(/Cisco Meraki device by HTTP/meraki.get.device.errors))>0`|Warning||
|Meraki: Status is not online||`last(/Cisco Meraki device by HTTP/meraki.device.status)<>1`|Warning||

### LLD rule Uplinks loss and quality discovery

|Name|Description|Type|Key and additional info|
|----|-----------|----|-----------------------|
|Uplinks loss and quality discovery||Dependent item|meraki.device.uplinks.discovery<p>**Preprocessing**</p><ul><li><p>JSON Path: `$.uplinksLL`</p></li></ul>|

### Item prototypes for Uplinks loss and quality discovery

|Name|Description|Type|Key and additional info|
|----|-----------|----|-----------------------|
|Uplink [{#IP}]: [{#UPLINK}]: Loss, %|<p>Loss percent of the device uplink. </p><p>Network: {#NETWORK.ID}. </p><p>Device serial: {#SERIAL}.</p>|Dependent item|meraki.device.loss.pct[{#IP},{#UPLINK}]<p>**Preprocessing**</p><ul><li><p>JSON Path: `The text is too long. Please see the template.`</p><p>⛔️Custom on fail: Set value to: `-1`</p></li></ul>|
|Uplink [{#IP}]: [{#UPLINK}]: Latency|<p>Latency of the device uplink. </p><p>Network: {#NETWORK.ID}. </p><p>Device serial: {#SERIAL}.</p>|Dependent item|meraki.device.latency[{#IP},{#UPLINK}]<p>**Preprocessing**</p><ul><li><p>JSON Path: `The text is too long. Please see the template.`</p><p>⛔️Custom on fail: Set value to: `-1000`</p></li><li><p>Custom multiplier: `0.001`</p></li></ul>|

### Trigger prototypes for Uplinks loss and quality discovery

|Name|Description|Expression|Severity|Dependencies and additional info|
|----|-----------|----------|--------|--------------------------------|
|Uplink [{#IP}]: [{#UPLINK}]: loss > {$MERAKI.DEVICE.LOSS}%||`min(/Cisco Meraki device by HTTP/meraki.device.loss.pct[{#IP},{#UPLINK}],#3)>{$MERAKI.DEVICE.LOSS}`|Warning||
|Uplink [{#IP}]: [{#UPLINK}]: latency > {$MERAKI.DEVICE.LATENCY}||`min(/Cisco Meraki device by HTTP/meraki.device.latency[{#IP},{#UPLINK}],#3)>{$MERAKI.DEVICE.LATENCY}`|Warning||

## Feedback

Please report any issues with the template at [`https://support.zabbix.com`](https://support.zabbix.com)

You can also provide feedback, discuss the template, or ask for help at [`ZABBIX forums`](https://www.zabbix.com/forum/zabbix-suggestions-and-feedback)

