do ($) ->
    onWeixinBridge = () ->
        wx = WeixinJSBridge
        wx.call 'hideOptionMenu'

    $ ->
        $('img').click (e) ->
            src = @src
            alert srcList.indexOf src
            alert ~srcList.indexOf src
            if ~srcList.indexOf src
                alert 1
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
