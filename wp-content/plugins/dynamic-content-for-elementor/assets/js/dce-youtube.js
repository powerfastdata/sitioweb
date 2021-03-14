var ytplayer = null;
var ytplayers = [];

function setVideoCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+ d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}
function getVideoCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

function videoPlay(ytplayer) {
  if (ytplayer) {
    ytplayer.playVideo();
    jQuery('.dce-youtube-play').removeClass('paused');
  }
}
function videoPause(ytplayer) {
    ytplayer.pauseVideo(ytplayer);
    jQuery('.dce-youtube-play').addClass('paused');
}
function videoStop(ytplayer) {
  if (ytplayer) {
    ytplayer.stopVideo(ytplayer);
  }
}
function videoPauseToggle(ytplayer) {
  if (ytplayer) {
    if (ytplayer.getPlayerState() == 2 || ytplayer.getPlayerState() == 5 ) {
        videoPlay(ytplayer);
    } else {
        videoPause(ytplayer);
    }
  }
}

function videoMuteToggle(ytplayer) {
  if (ytplayer) {
    if (ytplayer.isMuted()) {
        setVideoCookie("video-mute", 0, 1);
        ytplayer.unMute();
        jQuery('.dce-youtube-mute').removeClass('mute');
    } else {
        setVideoCookie("video-mute", 1, 1);
        ytplayer.mute();
        jQuery('.dce-youtube-mute').addClass('mute');
    }
  }
}

function setVideoHeight(ytplayer) {
    if (ytplayer) {
        var ytid = getVideoId(ytplayer);
        var ww = jQuery(window).width();
        var vw = jQuery('#'+ytid).parent().width();
        var wh = jQuery(window).height();
        var vh = Math.round((720*vw) / 1280);
        var wvh = vh;
        jQuery('#'+ytid).height(vh+'px');
    }
}

function onPlayerReady(event) {
    ytplayer = event.target;
    var ytid = getVideoId(ytplayer);
    ytplayers[ytid] = ytplayer;
    var elementSettings = jQuery('#'+ytid).closest('.elementor-widget-dce-ytvideo').data('settings') || {};

    if (elementSettings.autoplay) {
        ytplayer.playVideo();
    }

    // MUTE
    if (elementSettings.mute) {
        ytplayer.mute();
        jQuery('.dce-youtube-mute').addClass('mute');
    }
    if (getVideoCookie("video-mute") == '1') {
        ytplayer.mute();
        jQuery('.dce-youtube-mute').addClass('mute');
    }
    jQuery('.dce-youtube-mute').on('click', function(){
        videoMuteToggle(ytplayer);
        return false;
    });


    if (ytplayer) {
        if (ytplayer.getPlayerState() == 5) {
            // il video non è partito automaticamente
            // probabilmente sono su mobile
            videoPlay(ytplayer);
        }
        jQuery('.dce-youtube-play').on('click tap', function(){
            videoPauseToggle(ytplayer);
            return false;
        });
    }

    setVideoHeight(ytplayer);

    jQuery(window).scroll(function () {
        if (ytplayer) {
            var ytid = getVideoId(ytplayer);
            var vscroll = jQuery(window).scrollTop();
            var offset = jQuery('#'+ytid).offset();
            var maxh = offset.top + Math.round(jQuery('#'+ytid).height() / 2);
            if (maxh && vscroll >= maxh) {
                videoPause(ytplayer);
            } else {
            }
        }
    });

}

function getVideoId(ytplayer) {
    return jQuery(ytplayer.a).attr('id');
}

jQuery(window).load(function () {
    setVideoHeight(ytplayer);
});

jQuery(window).resize(function () {
    setVideoHeight(ytplayer);
});
