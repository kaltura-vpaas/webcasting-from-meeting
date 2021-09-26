# kme-to-webcast

This app highlights how one broadcast the contents of a Kaltura Meetings Experience (KME) room to a large audience.

### Prerequisites

1. [Nodejs](https://nodejs.org/en/) 
2. [Kaltura VPaaS account](https://corp.kaltura.com/video-paas/registration?utm_campaign=Meetabout&utm_medium=affiliates&utm_source=GitHub). Once you've opened an account, send an email to <VPaaS@kaltura.com> to activate Meetings.
3. Kaltura account configured with both KAF and KME modules enabled

### Install and Run

1. Clone the github repo: https://github.com/kaltura-vpaas/minimal-virtual-room-nodejs
2. Run `npm install`
3. Copy `.env.template` to `.env` and populate the following required fields (other fields in the file are not required to run this app):

## Key Features

- Creating a Kaltura LiveStream Entry, which houses the webcast
- Launch into a KME room
- Start broadcasting the webcast into the Kaltura LiveStream Entry
- View the webcast in a Kaltura Player

## Start/Stop Webcast from KME

- After joining the room as Presenter, click Tools, then Webcast Stream. Select a LiveStream entry and click Go Live to start broadcasting.
- Broadcast should be viewable in the player within 20 to 50 seconds after it started.
- To stop broadcasting, click on the Webcast icon on the top menu bar and click Stop Webcast.