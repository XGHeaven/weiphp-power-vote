do ($) ->
    onWeixinBridge = () ->
        wx = WeixinJSBridge
        wx.call 'hideOptionMenu'
        $('img').click e ->
            $this = $(this)
            src = $this.attr 'src'
            if ~srcList.indexof src
                wx.invoke('imagePreview', {
                    current: src,
                    urls: srcList
                })

    if typeof WeixinJSBridge == "undefined"
        if document.addEventListener
            document.addEventListener 'WeixinJSBridgeReady', onWeixinBridge, false
        else if document.attachEvent
            document.attachEvent 'WeixinJSBridgeReady', onWeixinBridge
            document.attachEvent 'onWeixinJSBridgeReady', onWeixinBridge
