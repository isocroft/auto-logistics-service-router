# auto-logistics-service-router

A PHP frontend & backend for routing last-mile deliveries to dispatch riders in traffic using PUSH SMS, OpenCellID data and REST APIs.

## Online Resources (Read Up)

- [OpenCellID Home Page](https://www.opencellid.org/#zoom=16&lat=37.77889&lon=-122.41942)
- [OpenCellID API](http://wiki.opencellid.org/wiki/API)
- [CellID Finder](https://cellidfinder.com/)
- [Tutorial on CellID Finder](https://cellidfinder.com/articles/how-to-find-cellid-location-with-mcc-mnc-lac-i-cellid-cid)

Current stats on the **Open Cell ID** database show that Nigeria has a total 199,620 number of cells (as of 8th August 2018) distributed into the following radio standard(s).

| GSM     |	CDMA  |	UMTS    | LTE   |
| ------  | ----  | ------  | ----  |
| 101,574 |	0     |	96,082  |	1,964 |

The **OpenCellID** database requires 4 parameters to successfully query it and they are as follows for the 4 major networks in Nigeria:

- Mobile Network Code (MNC)
- Mobile Country Code (MCC)
- Location Area Code (LAC)
- Cell Id (CID)

> The Mobile Network Code (MNC) is a unique 2-digit number to identify a mobile network. It is used within the International Mobile Subcriber Identity (IMSI) to uniquely identify mobile subscribers.

| MTN   | GLO | AIRTEL  | 9MOBILE | VISAFONE  |
| ----  | --- | ------  | ------  | --------  |
| 30    | 50  | 20      | 60      | 25        |

> The Mobile Country Code (MCC) is a unique 3-digit number to identify a country. It is used within the International Mobile Subcriber Identity (IMSI) to uniquely identify mobile subscribers.

| MTN   | GLO | AIRTEL  | 9MOBILE | VISAFONE  |
| ----  | --- | ------  | ------  | --------  |
| 621   | 621 | 621     | 621     | 621       |

> The Location Area Code (LAC) is a 16 bit number (hexaecimal) with two special values which represents a unique number of a given location area. A location area is a set of base stations that are grouped together to optimize signalling. To get current LAC value (manually) for any network in Nigeria, dial **\*#\*#4636#\*#\***.

> The Cell Id (CID) is a generally unique number used to identify each base transceiver station (BTS) or sector of a BTS within a Location Area Code (LAC) if not within a GSM network. To get current CID value (manually) for any network in Nigeria, dial **\*#\*#4636#\*#\***.

This project (auto service router) will need to work with an offline/online native mobile app which will "programmatically" extract the LAC and CID from the delivery riders' mobile smart-phone/device. Here is a **Stack Overflow** brief discussion on how it's done using the [_Android Native Telephony API_](https://stackoverflow.com/questions/4152373/how-to-know-location-area-code-and-cell-id-in-android-phone/).

## Implementation Diagrams

![FULLY ONLINE DIAGRAM](https://raw.githubusercontent.com/isocroft/auto-logistics-service-router/master/_research_docs/FULLY_ONLINE.jjpg)

![OFFLINE_ONLY_DIAGRAM](https://raw.githubusercontent.com/isocroft/auto-logistics-service-router/master/_research_docs/OFFLINE_ONLY.jjpg)
