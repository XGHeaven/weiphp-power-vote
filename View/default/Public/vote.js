// Generated by CoffeeScript 1.10.0
(function() {
  (function($) {
    var onWeixinBridge;
    onWeixinBridge = function() {
      var wx;
      wx = WeixinJSBridge;
      return wx.call('hideOptionMenu');
    };
    $(function() {
      return $('img').click(function(e) {
        var src;
        src = $(this).data('id');
        if (~srcList.indexOf(src)) {
          return WeixinJSBridge.invoke('imagePreview', {
            current: src,
            urls: srcList
          });
        }
      });
    });
    if (typeof WeixinJSBridge === "undefined") {
      if (document.addEventListener) {
        return document.addEventListener('WeixinJSBridgeReady', onWeixinBridge, false);
      } else if (document.attachEvent) {
        document.attachEvent('WeixinJSBridgeReady', onWeixinBridge);
        return document.attachEvent('onWeixinJSBridgeReady', onWeixinBridge);
      }
    }
  })($);

}).call(this);

//# sourceMappingURL=vote.js.map
