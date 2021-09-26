var express = require('express');
var router = express.Router();
var createRoom = require('../lib/createroom');
var joinRoom = require('../lib/joinroom');
var createwebcast = require('../lib/createwebcast');
const kaltura = require('kaltura-client');
var KalturaClientFactory = require('../lib/kalturaClientFactory');

/* GET home page. */
router.get('/', async function (req, res, next) {

  try {
    var adminks = await KalturaClientFactory.getKS('', { type: kaltura.enums.SessionType.ADMIN });
    var client = await KalturaClientFactory.getClient(adminks);
    let liveId = await createwebcast(client);

    res.render('index', { title: 'Kaltura Virtual Room', liveId: liveId });
  } catch (e) {
    res.render('error', { message: e, error: e });
  }

});

/* POST */
router.post('/', function (req, res, next) {
  createRoom("A Room Name", function (kalturaResponse) {
    console.log("creating room");
    joinRoom(kalturaResponse.id,
      req.body.firstName,
      req.body.lastName,
      req.body.email, function (joinLink) {
        res.redirect(joinLink);
      });
  });
});

module.exports = router;
