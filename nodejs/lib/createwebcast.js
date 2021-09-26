var kaltura = require('kaltura-client');

async function createWebcast(client) {

  let filter = new kaltura.objects.ConversionProfileFilter();
  filter.systemNameEqual = "Default_Live";
  let pager = new kaltura.objects.FilterPager();

  let liveConversionProfile = await kaltura.services.conversionProfile.listAction(filter, pager)
    .execute(client)
    .then(result => {
      return result;
    });

  let liveStreamEntry = new kaltura.objects.LiveStreamEntry();
  liveStreamEntry.name = "Newrow 2 Webcast";
  liveStreamEntry.description = "newrow to webcast test";
  liveStreamEntry.mediaType = kaltura.enums.MediaType.LIVE_STREAM_FLASH;
  liveStreamEntry.recordStatus = kaltura.enums.RecordStatus.DISABLED;
  liveStreamEntry.dvrStatus = kaltura.enums.DVRStatus.ENABLED;
  liveStreamEntry.dvrWindow = 60;
  liveStreamEntry.explicitLive = kaltura.enums.NullableBoolean.TRUE_VALUE;
  liveStreamEntry.entitledUsersPublish = "hunterp@gmail.com";
  liveStreamEntry.conversionProfileId = liveConversionProfile.id;
  let sourceType = kaltura.enums.SourceType.LIVE_STREAM;

  let liveAdd = await kaltura.services.liveStream.add(liveStreamEntry, sourceType)
    .execute(client)
    .then(result => {
      return result;
    });

  return liveAdd.id;

}

module.exports = createWebcast