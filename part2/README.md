We need to build a mysql db to hold all data. In this project we would like to segregate the data itslef into different tiers of availability.

Availability Levels:
* Tier 1 - Highest availability as this data is accessed very frequently. Data is best to be managed directly on RAM.
* Tier 2 - Medium availability. This data is not accessed all the time but still needs to be available for fast access. Best managed on SSD.
* Tier 3 - Low availability. This data is accessed by an internal analyst. sometimes. Best managed on magnetic disks.

We will try to segregate user sessions into these 3 tiers, which should be reflected in the db schema definition.
As tier 1 data tends to hold little information (less than 100MB in our project) with a low and finite cardinality, tier 3 has essentially infinite cardinality therefore should be saved on low cost magnetic disks.
In order to maintain low cardinality in tier 1 data, we need to estimate it beforehand to maximize SELECTs and UPDATEs and minimize INSERTs and DELETEs.
Tier 3 data systems can be developed as an operational historian software, like logstash, processing thousands of events per second, dividing the data further into sub tiers of availability (AWS Glacier as tier 4 data?). We would prefer mostly INSERTs on this tier, as data is likely to be saved in a time-series fashion (like an event).
Tier 2 data is somewhere in between. Should feel comfortable with lots of INSERTs and also some degree of SELECTs, but cardinality in this tier should be defined and maintained with care, as to prevent I/O blocking, especially when the db is disk-persistent like mysql (innodb/myisam).

For tier 1 data, PK columns must be defined appropriately in order to set a high bound for cardinality and make it finite. Using an INT column with AUTO_INCREMENT is a bad idea. Instead, if we already know that each ip (in the world) can have only one open session (concurrently) then maybe using the ip itself as the PK column might not be a bad idea. Should the ip be saved as string?
Tier 2 data tables, should also have some PK. Trying to avoid an INT with AUTO INCREMENT here too because INTs (unsigned bigints as well) reach their maximum. Well.. eventually. Some other information is required here for uniqueness, preferably a number and not a string (string manipulations cost much more cpu).



Some definitions about VideoViewer user sessions:
* only one session can be opened per ip at the same time
* a user session remains open for only 3 hours
* there must be a way to manually close a user's session without waiting for 3 hours


## Questions:
1. The server side php file has some errors - please fix them. All methods in the VideoViewerV1 interface must be exposed and invokable by a client. The implementation in the server file MUST NOT trigger any error.
2. 
