We need to build a mysql db to hold most of the data. We would like to segregate it into different tiers of availability:
* Tier 1 - Highest availability as this data is accessed very frequently. Data is best to be managed directly on RAM.
* Tier 2 - Medium availability. This data is not accessed all the time but still needs to be available for fast access. Best managed on SSD.
* Tier 3 - Low availability. This data is accessed by an internal analyst. Sometimes. Best managed on magnetic disks.

We will try to VideoViewer information into these 3 tiers, which should be reflected in the db schema definition.
As tier 1 data tends to hold little information (less than 100MB in our project) with a low and finite cardinality, tier 3 has essentially infinite cardinality therefore should be saved on low cost magnetic disks.
In order to maintain low cardinality in tier 1 data, we need to estimate it beforehand to maximize SELECTs and UPDATEs and prevent INSERTs and DELETEs as much as possible.

Tier 3 data systems can be developed as an operational historian software, like logstash, processing thousands of events per second, dividing the data further into sub tiers of availability (AWS Glacier as tier 4 data?). We would prefer mostly INSERTs on this tier, as data is likely to be saved in a time-series fashion (like an event).

Tier 2 data is somewhere in between. Should feel comfortable with lots of INSERTs and also some degree of SELECTs, but cardinality in this tier should be defined and maintained with care, as to prevent i/o blocking, especially when the db is disk-persistent like mysql (innodb/myisam).

For tier 1 data, PK columns must be defined appropriately in order to set a high bound for cardinality and make it finite. Using an INT column with AUTO_INCREMENT is a bad idea. Instead, if we already know that each ip (in the world) can have only one open session (concurrently) then maybe using the ip itself as the PK column might not be a bad idea. On the contrary, setting the PK as INT with AUTO_INCREMENT is effectively removing any constraint on cardinality thus making it infinite. Remember, we want to avoid INSERTs and DELETEs as much as possible in tier 1.

Tier 2 data tables, should also have some PK. Trying to avoid an INT with AUTO INCREMENT here too because INTs (unsigned bigints as well) reach their maximum. Well.. eventually. Some other information is required here for uniqueness, preferably a number and not a string (string manipulations cost much more cpu). To retain some degree of control over the cardinality of this db table (tier 2) we
must DELETE outdated information from it. Maybe even pass it on to tier 3 systems before performing the DELETEs.

A good index can go a long way to prevent a full table scan and should be used wisely to maximize lookup speed (index on a single column) or to help with the application logic by using composite unique indices. Generally speaking, we should never do "SELECT &#42; FROM table" because normally we wouldn't need all the columns anyway, so indices should be put only on columns that are used frequently inside our queries, no matter in which clause. Putting more than one index on the same column is generally a bad idea because changes would reflect on table and also on all indices as well thus potentially generating more i/o operations. On the other hand, a composite unique index which could be applied as a non-clustered unique index on multiple columns OR as a primary key on more than one column, is an excellent idea to save in advance aggregative information for later use. For example, say we want to keep track on how many cookies each person eats every day. So we can build a table, "daily_cookies", and have a primary key on both columns "day_date" (field type date, NOT datetime) and "person_id" (int). When a person is eating a cookie we could do "INSERT INTO daily_cookies (day_date, person_id, sum_cookies) values ('2017-07-20', 1, 1) ON DUPLICATE KEY UPDATE sum_cookies = sum_cookies + 1". When the number of cookies a person ate is needed inside the application logic (to decide whether he reached his daily cap of cookies!) then the query to daily_cookies would result in a single row fetched without employing a wasteful SELECT COUNT(&#42;).

In our example tier 3 data is kept on a remote server (so no need to define it here). Our local mysql db should hold only tier 1 and tier 2 data.

## Server side definitions:
* only one session can be opened per ip at the same time [tier 1].
* a user session remains open for only 3 hours.
* there must be a way to manually close a user's session without waiting for 3 hours.
* if the user requests "fulfill" before the time is up for the video then nothing happens (it doesn't "fulfill"). The same goes for "close_session".
* a user must view the videos ONE AT A TIME in order to get paid. opening another client app from another computer under the same ip should not allow closing the session sooner than expected (nor paying the user).
* every ip is allowed to open only 15 sessions per day. once 15 sessions were already closed for that ip, no more sessions can be opened that day. Please try to avoid doing a SELECT COUNT(&#42;) - using a mysql composite unique key on current day and user_id can go a long way [tier 1/2].
* there must be a way to set a total number of sessions per day for all users. a user may not open a session if the total number of closed sessions for that day is reached - even if the user didn't reach its own 15 sessions per day [tier 1/2].
* every video has a global unique id
* the user must send the next video object it is going to watch (properties "id", "url", "timer" fetched earlier via the "feed" request) to "create_session". every new session must be initialized with the first video the user is going to watch. the first video can be "fulfilled" once its "timer" has passed since the session was created.
* the user must pass the next video object it is going to watch to the "fulfill" request. a video can be "fulfilled" once its "timer" has passed since the last "fulfill". for example - the user has just finished watching video1 and trying to "fulfill". When requesting "fulfill" on video1 the user must also provide the details of the next video (video2 with "timer" set to 30 secs) it is about to watch. the user can "fulfill" video2 only 30 seconds after it has requested "fulfill" the last time (on video1).
* the last "fulfill" in the session doesn't have to contain an object to the next video.
* the user must request "close_session" after the last "fulfill" was requested to effectively close the session and (maybe) pay the user.
* the system saves information about all videos every user watched in the last 24 hours prior to the request. AT ANY GIVEN TIME the system should be able to detail all videos the user had seen in the last 24 hours until that time. meaning that information which is older than 24 hours should be removed periodically. to verify you got this right check last seen videos at 1am - are you getting videos seen starting from 1am the previous day? [tier 2].

## Questions:
1. The server side php file has some errors - please fix them. All methods in the VideoViewerV1 interface must be exposed and invokable by a client. The implementation in the server file MUST NOT trigger any error.
2. Please provide a definition to the mysql db schema (tables, cols, indices with PKs).
3. Please implement the following methods in the server side php file: "create_session", "fulfill", "close_session", "fetch_last_user_watched_vids". You may change their signatures by changing the parameters they receive (just don't forget to change the interface too).
4. If there's any need for some cron jobs, please add the methods you need to the crons.php file under the server folder in this repo. Don't forget to explain how often or when the crons should run.
