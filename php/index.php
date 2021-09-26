<?php
require_once('./KalturaClient/KalturaClient.php');
require_once('./AppSettings.php');

// Get Kaltura client
$config = new KalturaConfiguration();
$config->setServiceUrl('https://www.kaltura.com');
$client = new KalturaClient($config);

// Use an Admin KS to create the live stream (webcast) entry (needed for operations like listing of conversion profiles)
$adminKalturaSession = $client->generateSession(API_ADMIN_SECRET, ADMIN_USER_ID, KalturaSessionType::ADMIN, PARTNER_ID, KS_EXPIRY);
$client->setKS($adminKalturaSession);
$livestreamID = LIVESTREAM_ENTRY_ID;
if (LIVESTREAM_ENTRY_ID == '') {
  // Get the necessary conversion profiles

  // Live ingest conversion profiles
  $filter = new KalturaConversionProfileFilter();
  $filter->systemNameEqual = "Default_Live"; // systemName=Default_Live is the Cloud Transcode, and systemName=Passthrough_Live
  $liveConversionProfile = $client->conversionProfile->listAction($filter)->objects[0];


  // create the live stream entry; see documentation here:
  // * https://developer.kaltura.com/api-docs/General_Objects/Objects/KalturaLiveStreamEntry
  $liveStreamEntry = new KalturaLiveStreamEntry();
  $liveStreamEntry->name = "Newrow 2 Webcast";
  $liveStreamEntry->description = "newrow to webcast test";
  $liveStreamEntry->mediaType = KalturaMediaType::LIVE_STREAM_FLASH; //indicates rtmp/rtsp source broadcast
  $liveStreamEntry->conversionProfileId = $liveConversionProfile->id;
  $liveStreamEntry->recordStatus = KalturaRecordStatus::DISABLED;
  $liveStreamEntry->dvrStatus = KalturaDVRStatus::ENABLED; //enable or disable DVR
  $liveStreamEntry->dvrWindow = 60; // how long should the DVR be, specified in minutes
  $liveStreamEntry->explicitLive = KalturaNullableBoolean::TRUE_VALUE;
  $liveStreamEntry->entitledUsersPublish = PRESENTER_USER_ID; // give the presenter access to this live stream entry
  $liveStreamEntry->sourceType = KalturaSourceType::LIVE_STREAM;

  // Add the live stream entry; see documentation here:
  // * https://developer.kaltura.com/console/service/liveStream/action/add
  $liveStreamEntry = $client->liveStream->add($liveStreamEntry, KalturaSourceType::LIVE_STREAM);
  $livestreamID = $liveStreamEntry->id;
}
else {
  
}


// Get absolute URL
$s = $_SERVER;
$useForwardedHost = false;
$ssl      = ( ! empty( $s['HTTPS'] ) && $s['HTTPS'] == 'on' );
$sp       = strtolower( $s['SERVER_PROTOCOL'] );
$protocol = substr( $sp, 0, strpos( $sp, '/' ) ) . ( ( $ssl ) ? 's' : '' );
$port     = $s['SERVER_PORT'];
$port     = ( ( ! $ssl && $port=='80' ) || ( $ssl && $port=='443' ) ) ? '' : ':'.$port;
$host     = ( $useForwardedHost && isset( $s['HTTP_X_FORWARDED_HOST'] ) ) ? $s['HTTP_X_FORWARDED_HOST'] : ( isset( $s['HTTP_HOST'] ) ? $s['HTTP_HOST'] : null );
$host     = isset( $host ) ? $host : $s['SERVER_NAME'] . $port;
$absoluteUrl = $protocol . '://' . $host . $s['REQUEST_URI'];
$logoUrl = $absoluteUrl."kaltura-logo.png";


$expiresAt = time() + KS_EXPIRY;
$roomUserKS = $client->generateSession(API_ADMIN_SECRET, PRESENTER_USER_ID, KalturaSessionType::USER, PARTNER_ID, KS_EXPIRY, 'resourceId:'.RESOURCE_ID.',role:adminRole,userContextualRole:0,email:manager@pat.com');

// This is the page to join the newrow room
$joinRoomPageUrl = "https://".PARTNER_ID.".kaf.kaltura.com/virtualEvent/launch?ks=".$roomUserKS;

// This is the page where the webcast can be viewed.
//$playbackPageUrl = "http://localhost/kaltura-newrow2webcast/webcast-viewer/?entryId=".$liveStreamEntry->id;
$playbackPageUrl = $absoluteUrl.'/webcast-viewer/?entryId='.$livestreamID;
$playbackEntryId = $livestreamID;
$playerEmbedURL = "https://cdnapisec.kaltura.com/p/".PARTNER_ID."/embedPlaykitJs/uiconf_id/".V7_PLAYER_UI_CONF;
?>

<html>
  <head>
    <title>Kaltura Newrow2Webcast</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="icon" type="image/vnd.microsoft.icon" href="favicon.ico">
    <link href="style.css" media="screen" rel="stylesheet" type="text/css" />
  </head>
  <body>
    <img src="<?php echo $logoUrl; ?>" height="60"/>
    <h1>Newrow2Webcast</h1>
    <h4 class="divider">_______________</h4>
    <h3>Created Webcast Entry: <?php echo $livestreamID; ?></h3>
    <h4>Entry can be found in the <a target="_blank" href="https://kmc.kaltura.com/index.php/kmcng/content/entries/list">KMC</a><h4>
    <h4 class="divider">_______________</h4>
    <h2>Presenter</h2>
    <iframe src="<?php echo $joinRoomPageUrl; ?>" wmode=transparent allow="microphone *; camera *; speakers *; usermedia *; autoplay *; fullscreen *;" width="1100px" height="700px"></iframe>
    <h4 class="divider">_______________</h4>
    <h2>Viewer</h2>
    <div id="my_player" style="width: 1100px;height: 700px;margin: auto"></div>
    <script type="text/javascript" src="<?php echo $playerEmbedURL; ?>"></script>
    <script>
      
      var kalturaPlayer;

      try {
        kalturaPlayer = KalturaPlayer.setup({
          targetId: "my_player",
          provider: {
            partnerId: <?php echo PARTNER_ID; ?>,
            uiConfId: <?php echo V7_PLAYER_UI_CONF; ?>
          },
          playback: {
            autoplay: true
          }
        });
        kalturaPlayer.loadMedia({entryId: '<?php echo $playbackEntryId; ?>'});
      } catch (e) {
        console.error(e.message)
      }
      
    </script>
  </body>
</html>
