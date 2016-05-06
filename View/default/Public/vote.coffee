do ($) ->
    onWeixinBridge = () ->
        wx = WeixinJSBridge
        wx.call 'hideOptionMenu'

    $ ->
        $('img').click (e) ->
            id = $(this). data 'id'
            src = @src
            if ~imageIDList.indexOf id
                WeixinJSBridge.invoke('imagePreview', {
                    current: src,
                    urls: srcList
                })

    if typeof WeixinJSBridge == "undefined"
        if document.addEventListener
            document.addEventListener 'WeixinJSBridgeReady', onWeixinBridge, false
        else if document.attachEvent
            document.attachEvent 'WeixinJSBridgeReady', onWeixinBridge
            document.attachEvent 'onWeixinJSBridgeReady', onWeixinBridge
