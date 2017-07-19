We need to build a mysql db to hold most of the data. We would like to segregate it into different tiers of availability:
* Tier 1 - Highest availability as this data is accessed very frequently. Data is best to be managed directly on RAM.
* Tier 2 - Medium availability. This data is not accessed all the time but still needs to be available for fast access. Best managed on SSD.
* Tier 3 - Low availability. This data is accessed by an internal analyst. Sometimes. Best managed on magnetic disks.

We will try to segregate user sessions into these 3 tiers, which should be reflected in the db schema definition.
As tier 1 data tends to hold little information (less than 100MB in our project) with a low and finite cardinality, tier 3 has essentially infinite cardinality therefore should be saved on low cost magnetic disks.
In order to maintain low cardinality in tier 1 data, we need to estimate it beforehand to maximize SELECTs and UPDATEs and minimize INSERTs and DELETEs.

Tier 3 data systems can be developed as an operational historian software, like logstash, processing thousands of events per second, dividing the data further into sub tiers of availability (AWS Glacier as tier 4 data?). We would prefer mostly INSERTs on this tier, as data is likely to be saved in a time-series fashion (like an event).

Tier 2 data is somewhere in between. Should feel comfortable with lots of INSERTs and also some degree of SELECTs, but cardinality in this tier should be defined and maintained with care, as to prevent I/O blocking, especially when the db is disk-persistent like mysql (innodb/myisam).

For tier 1 data, PK columns must be defined appropriately in order to set a high bound for cardinality and make it finite. Using an INT column with AUTO_INCREMENT is a bad idea. Instead, if we already know that each ip (in the world) can have only one open session (concurrently) then maybe using the ip itself as the PK column might not be a bad idea. Setting the PK as INT with AUTO_INCREMENT is effectively removing any constraint on cardinality. Remember, we want to avoid INSERTs and DELETEs as much as possible.

Tier 2 data tables, should also have some PK. Trying to avoid an INT with AUTO INCREMENT here too because INTs (unsigned bigints as well) reach their maximum. Well.. eventually. Some other information is required here for uniqueness, preferably a number and not a string (string manipulations cost much more cpu). To retain some degree of control over the cardinality of this db table (tier 2) we
must DELETE outdated information from it. Maybe even pass it on to tier 3 systems before removing them completely.

In our example tier 3 data is kept on a remote server (so no need to define it here). Our local mysql db should hold only tier 1 and tier 2 data.

Some statements about VideoViewer's user sessions:
* only one session can be opened per ip at the same time.
* a user session remains open for only 3 hours.
* there must be a way to manually close a user's session without waiting for 3 hours.
* if the user requests "fulfill" before time is up for the video then nothing happens (it doesn't "fulfill").
* a user must view the videos ONE AT A TIME in order to get paid. opening another client app from another computer under the same ip should not close the session sooner than expected.
* every ip is allowed to open only 15 sessions per day. once 15 sessions were closed for that ip, no more sessions can be opened that day.
* there must be a way to set a total number of sessions per day for all users. a user may not open a session if the total number of closed sessions for that day is reached - even if the user didn't reach its own 15 sessions per day.
* every video has a global unique id


## Questions:
1. The server side php file has some errors - please fix them. All methods in the VideoViewerV1 interface must be exposed and invokable by a client. The implementation in the server file MUST NOT trigger any error.
2. 
