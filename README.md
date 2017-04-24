# VideoViewer

VideoViewer is a test repo designed to present questions for a job interview.
The system let a user view 20 videos and then pays the user 1$ for watching them. The system may pay additional 1$ for every 20 videos watched by the user.
The server side is implemented in php/mysql and client side in HTML/JS and also in Android as a native app.

## Definitions:
* The VideoViewer server endpoint is at: http://videoviewer.com/ws.php
* The VideoViewer HTML client side endpoint is at: http://videoviewer.com/index.php
* The VideoViewerV1 interface for client-server interaction is defined in the server php file:
  1. Optional - the user receives the HTML of the offer with "index" (not in Android).
  2. The user requests "create_session" to create a server side session.
  3. The user requests "feed" to populate an internal list with 200 videos. Each video in the list has a timer which corresponds to the number of seconds the user must view the video before the user can request "fulfill" to notify the server that a video was watched, before going to the next video. Once a video was watched it is removed from the internal list. If the internal list is empty the user should request "feed" again to repopulate the list.
  4. When the user is sure 20 videos were watched it requests "close_session" to close the current session and make the payment.
  5. The user may request "create_session" again to start another session and use its already populated internal list to continue watching videos.

## Questions:
1. The server side php file has some errors - please fix them.
2. Please write a **simple** JS library to interact with the php server side. JS flavor can be either JQuery or Vanilla (no JS framework). Bootstrap is optional. No design is needed - simple strings with current state are recommended, e.g: "Session was created successfully! Now showing video 1 out of 20". The VideoViewer client-server protocol for interaction is dictated by the VideoViewerV1 interface.
3. Please write a simple Android Java app with minimal design. The first step in the client-server interaction (fetching HTML) is not necessary. The video should be played inside the app.
4. Please write a simple Objective-C app with minimal design. The first step in the client-server interaction (fetching HTML) is not necessary. The video should be played inside the app.

Please answer question 3 OR question 4.
