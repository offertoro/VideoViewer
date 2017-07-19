# VideoViewer

The VideoViewer system is a test repo created to test candidates in the process of a job interview.
The system lets a user watch 20 videos and then pays the user 1$ for watching them. The system may pay an additional 1$ for every 20 videos watched by the user.
The server side is implemented in php/mysql and client side in HTML/JS and also in Android or iOS as a native app.

## Definitions:
* The VideoViewer server endpoint is at: http://videoviewer.com/ws.php (not really)
* The VideoViewer HTML client side endpoint is at: http://videoviewer.com/index.php (not really)
* The VideoViewerV1 interface for client-server interaction is defined in the server php file:
  1. Optional - the user receives the HTML of the offer with "index" (not in Android/iOS).
  2. The user requests "create_session" to create a server side session.
  3. The user requests "feed" to receive a json with 200 videos from the server. This populates an internal list with 200 videos on the client side. Each video in the list has a timer which corresponds to the number of seconds the user must view the video before the user can request "fulfill" to notify the server that a video was watched and only then start watching the next video. Once a video was watched it is removed from the internal list. If the internal list is empty the user should request "feed" again to repopulate the list.
  4. When the user is sure 20 videos were watched it requests "close_session" to close the current session and make the payment to the user.
  5. The user may request "create_session" again to start another session and use its already populated internal list to continue watching videos.

All files should be sent to babin@offertoro.com

Thank you and good luck!
