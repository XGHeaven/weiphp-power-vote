do ($) ->
    onWeixinBridge = () ->

    if typeof WeixinJSBridge == "undefined"
        if document.addEventListener
            document.addEventListener 'WeixinJSBridgeReady', onWeixinBridge, false
        else if document.attachEvent
            document.attachEvent 'WeixinJSBridgeReady', onWeixinBridge
            document.attachEvent 'onWeixinJSBridgeReady', onWeixinBridge
