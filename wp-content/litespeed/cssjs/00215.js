!function(e){"use strict";function t(e,t,n){var i;return function(){var a=this,o=arguments,r=function(){i=null,n||e.apply(a,o)},s=n&&!i;clearTimeout(i),i=setTimeout(r,t),s&&e.apply(a,o)}}function n(t,n){var i=t.find(".hajs-filter"),a=i.data("default-filter");i.length&&(i.on("click.onFilterNav","button",function(t){t.stopPropagation();var i=e(this);i.addClass("ha-filter__item--active").siblings().removeClass("ha-filter__item--active"),n(i.data("filter"))}),i.find('[data-filter="'+a+'"]').click())}function i(t){if(t.$element.on("click",t.selector,function(e){e.preventDefault()}),e.fn.magnificPopup){if(!t.isEnabled)return void e.magnificPopup.close();var n=e(window).width(),i=elementorFrontendConfig.breakpoints.md,a=elementorFrontendConfig.breakpoints.lg;t.$element.find(t.selector).magnificPopup({key:t.key,type:"image",image:{titleSrc:function(e){return e.el.attr("title")?e.el.attr("title"):e.el.find("img").attr("alt")}},gallery:{enabled:!0,preload:[1,2]},zoom:{enabled:!0,duration:300,easing:"ease-in-out",opener:function(e){return e.is("img")?e:e.find("img")}},disableOn:function(){return!(t.disableOnMobile&&n<i)&&!(t.disableOnTablet&&n>=i&&n<a)}})}}var a=e(window);e.fn.getHappySettings=function(){return this.data("happy-settings")};var o=function(e){var t=e.find(".hajs-image-comparison"),n=t.getHappySettings();n[{on_hover:"move_slider_on_hover",on_swipe:"move_with_handle_only",on_click:"click_to_move"}[n.move_handle||"on_swipe"]]=!0,delete n.move_handle,t.imagesLoaded().done(function(){t.twentytwenty(n);var e=setTimeout(function(){a.trigger("resize.twentytwenty"),clearTimeout(e)},400)})};a.on("elementor/frontend/init",function(){var r=elementorModules.frontend.handlers.Base,s=r.extend({bindEvents:function(){this.removeArrows(),this.run()},removeArrows:function(){var e=this;this.elements.$container.on("init",function(){e.elements.$container.siblings().hide()})},getDefaultSettings:function(){return{autoplay:!0,arrows:!1,checkVisible:!1,container:".hajs-slick",dots:!1,infinite:!0,rows:0,slidesToShow:1,prevArrow:e("<div />").append(this.findElement(".slick-prev").clone().show()).html(),nextArrow:e("<div />").append(this.findElement(".slick-next").clone().show()).html()}},getDefaultElements:function(){return{$container:this.findElement(this.getSettings("container"))}},onElementChange:t(function(){this.elements.$container.slick("unslick"),this.run()},200),getSlickSettings:function(){var t={infinite:!!this.getElementSettings("loop"),autoplay:!!this.getElementSettings("autoplay"),autoplaySpeed:this.getElementSettings("autoplay_speed"),speed:this.getElementSettings("animation_speed"),centerMode:!!this.getElementSettings("center"),vertical:!!this.getElementSettings("vertical"),slidesToScroll:1};switch(this.getElementSettings("navigation")){case"arrow":t.arrows=!0;break;case"dots":t.dots=!0;break;case"both":t.arrows=!0,t.dots=!0}return t.slidesToShow=parseInt(this.getElementSettings("slides_to_show"))||1,t.responsive=[{breakpoint:elementorFrontend.config.breakpoints.lg,settings:{slidesToShow:parseInt(this.getElementSettings("slides_to_show_tablet"))||t.slidesToShow}},{breakpoint:elementorFrontend.config.breakpoints.md,settings:{slidesToShow:parseInt(this.getElementSettings("slides_to_show_mobile"))||parseInt(this.getElementSettings("slides_to_show_tablet"))||t.slidesToShow}}],e.extend({},this.getSettings(),t)},run:function(){this.elements.$container.slick(this.getSlickSettings())}}),l=function(e){elementorFrontend.waypoint(e,function(){var t=e.find(".ha-number-text");t.numerator(t.data("animation"))})},d=function(t){elementorFrontend.waypoint(t,function(){t.find(".ha-skill-level").each(function(){var t=e(this),n=t.find(".ha-skill-level-text"),i=t.data("level");t.animate({width:i+"%"},500),n.numerator({toValue:i+"%",duration:1300,onStep:function(){n.append("%")}})})})},c=r.extend({onInit:function(){r.prototype.onInit.apply(this,arguments),this.run(),this.runFilter(),a.on("resize",t(this.run.bind(this),100))},getLayoutMode:function(){var e=this.getElementSettings("layout");return"even"===e?"masonry":e},getDefaultSettings:function(){return{itemSelector:".ha-image-grid__item",percentPosition:!0,layoutMode:this.getLayoutMode()}},getDefaultElements:function(){return{$container:this.findElement(".hajs-isotope")}},getLightBoxSettings:function(){return{key:"imagegrid",$element:this.$element,selector:".ha-js-lightbox",isEnabled:!!this.getElementSettings("enable_popup"),disableOnTablet:!!this.getElementSettings("disable_lightbox_on_tablet"),disableOnMobile:!!this.getElementSettings("disable_lightbox_on_mobile")}},runFilter:function(){var e=this,t=this.getLightBoxSettings();n(this.$element,function(n){e.elements.$container.isotope({filter:n}),"*"!==n&&(t.selector=n),i(t)})},onElementChange:function(e){-1!==["layout","image_height","columns","image_margin","enable_popup"].indexOf(e)&&this.run()},run:function(){var e=this;e.elements.$container.isotope(e.getDefaultSettings()).imagesLoaded().progress(function(){e.elements.$container.isotope("layout")}),i(e.getLightBoxSettings())}}),h=r.extend({onInit:function(){r.prototype.onInit.apply(this,arguments),this.run(),this.runFilter(),a.on("resize",t(this.run.bind(this),100))},getDefaultSettings:function(){return{rowHeight:+this.getElementSettings("row_height.size")||150,lastRow:this.getElementSettings("last_row"),margins:+this.getElementSettings("margins.size"),captions:!!this.getElementSettings("show_caption")}},getDefaultElements:function(){return{$container:this.findElement(".hajs-justified-grid")}},getLightBoxSettings:function(){return{key:"justifiedgallery",$element:this.$element,selector:".ha-js-lightbox",isEnabled:!!this.getElementSettings("enable_popup"),disableOnTablet:!!this.getElementSettings("disable_lightbox_on_tablet"),disableOnMobile:!!this.getElementSettings("disable_lightbox_on_mobile")}},runFilter:function(){var e=this,t=this.getLightBoxSettings(),a={lastRow:this.getElementSettings("last_row")};n(e.$element,function(n){"*"!==n&&(a.lastRow="nojustify",t.selector=n),a.filter=n,e.elements.$container.justifiedGallery(a),i(t)})},onElementChange:function(e){-1!==["row_height","last_row","margins","show_caption","enable_popup"].indexOf(e)&&this.run()},run:function(){this.elements.$container.justifiedGallery(this.getDefaultSettings()),i(this.getLightBoxSettings())}}),u=r.extend({onInit:function(){r.prototype.onInit.apply(this,arguments),this.wrapper=this.$element.find(".ha-news-ticker-wrapper"),this.run()},onElementChange:function(e){"item_space"!==e&&"title_typography_font_size"!==e||this.run()},run:function(){var t=this.wrapper.innerHeight(),n=this.wrapper.innerWidth(),i=this.wrapper.find(".ha-news-ticker-container"),a=i.find(".ha-news-ticker-item"),o=this.wrapper.data("scroll-direction"),r="scroll"+o+t+n,s=this.wrapper.data("duration"),l="normal",d=10,c={transform:"translateX(0"+n+"px)"},h={transform:"translateX(-101%)"};"right"===o&&(l="reverse"),a.each(function(){d+=e(this).outerWidth(!0)}),i.css({width:d,display:"flex"}),e.keyframe.define([{name:r,"0%":c,"100%":h}]),i.playKeyframe({name:r,duration:s+"ms",timingFunction:"linear",delay:"0s",iterationCount:"infinite",direction:l,fillMode:"none",complete:function(){}})}}),f=function(e){elementorFrontend.waypoint(e,function(){var t=e.find(".ha-fun-factor__content-number");t.numerator(t.data("animation"))})},p=function(t){elementorFrontend.waypoint(t,function(){var t=e(this),n=t.find(".ha-bar-chart-container"),i=t.find("#ha-bar-chart"),a=n.data("settings");n.length&&new Chart(i,a)})},m=function(t){var n=t.find(".ha-twitter-load-more"),i=t.find(".ha-tweet-items");n.on("click",function(n){n.preventDefault();var a=e(this),o=a.data("settings"),r=a.data("total"),s=t.find(".ha-tweet-item").length;e.ajax({url:HappyLocalize.ajax_url,type:"POST",data:{action:"ha_twitter_feed_action",security:HappyLocalize.nonce,query_settings:o,loaded_item:s},success:function(t){r>s?e(t).appendTo(i):(a.text("All Loaded").addClass("loaded"),setTimeout(function(){a.css({display:"none"})},800))},error:function(e){}})})},g=r.extend({onInit:function(){r.prototype.onInit.apply(this,arguments),this.wrapper=this.$element.find(".ha-post-tab"),this.run()},run:function(){var n=this.wrapper.find(".ha-post-tab-filter"),i=n.find("li"),a=this.wrapper.data("event"),o=this.wrapper.data("query-args");i.on(a,t(function(t){t.preventDefault();var n=e(this),a=n.data("term"),r=n.closest(".ha-post-tab"),s=r.find(".ha-post-tab-content"),l=s.find(".ha-post-tab-loading"),d=s.find(".ha-post-tab-item-wrapper"),c=!1;0===l.length&&(i.removeClass("active"),d.removeClass("active"),n.addClass("active"),d.each(function(){var t=e(this),n=t.data("term");a===n&&(t.addClass("active"),c=!0)}),!1===c&&e.ajax({url:HappyLocalize.ajax_url,type:"POST",data:{action:"ha_post_tab_action",security:HappyLocalize.nonce,post_tab_query:o,term_id:a},beforeSend:function(){s.append('<span class="ha-post-tab-loading"><i class="eicon-spinner eicon-animation-spin"></i></span>')},success:function(e){s.find(".ha-post-tab-loading").remove(),s.append(e)},error:function(e){}}))},200))}}),v=function(t){var n=t.find(".ha-table__head-column-cell");t.find(".ha-table__body-row").each(function(t,i){e(i).find(".ha-table__body-row-cell").each(function(t,i){e(i).prepend('<div class="ha-table__head-column-cell">'+n.eq(t).html()+"</div>")})})},y=function(t){var n=t.find(".ha-threesixty-rotation-inner"),i=n.data("selector"),a=n.data("autoplay"),o=t.find(".ha-threesixty-rotation-magnify"),r=t.find(".ha-threesixty-rotation-360img"),s=o.data("zoom"),l=t.find(".ha-threesixty-rotation-play"),d=circlr(i,{play:!0});if("on"===a){var c=t.find(".ha-threesixty-rotation-autoplay");c.on("click",function(e){e.preventDefault(),d.play(),r.remove()}),setTimeout(function(){c.trigger("click"),c.remove()},1e3)}else l.on("click",function(t){t.preventDefault();var n=e(this),i=n.find("i");i.hasClass("hm-play-button")?(i.removeClass("hm-play-button"),i.addClass("hm-stop"),d.play()):(i.removeClass("hm-stop"),i.addClass("hm-play-button"),d.stop()),r.remove()});o.on("click",function(n){t.find("img").each(function(){-1!==e(this).attr("style").indexOf("block")&&(HappySimplaMagnify(e(this)[0],s),o.css("display","none"),r.remove())})}),e(document).on("click",function(i){var a=e(i.target),s=t.find(".ha-img-magnifier-glass"),l=o.find("i");s.length&&a[0]!==l[0]&&(s.remove(),o.removeAttr("style")),a[0]===n[0]&&r.remove()}),n.on("mouseup mousedown",function(e){r.remove()})},w=function(e){var t=e.find(".ha-ec"),n=e.find(".ha-ec-popup-wrapper"),i=e.find(".ha-ec-popup-close"),a=t.data("events"),o=t.data("initialview"),r=t.data("firstday"),s=t.data("locale"),l=t.data("show-popup"),d=t.data("allday-text");if(void 0!==a){var c={stickyHeaderDates:!1,locale:s,headerToolbar:{left:"prev,next today",center:"title",right:"dayGridMonth,timeGridWeek,timeGridDay,listMonth"},initialView:o,firstDay:r,eventTimeFormat:{hour:"numeric",minute:"2-digit",meridiem:"short"},events:a,height:"auto",eventClick:function(e){function t(e){return new Date(e)}function i(e){var t=e.getHours(),n=e.getMinutes(),i=t>=12?"pm":"am";return t%=12,t=t||12,n=n<10?"0"+n:n,t+":"+n+i}if(e.jsEvent.preventDefault(),"yes"==l){var a=(e.view.calendar.currentData.currentDate.toString(),e.event.allDay),o=e.event.title,r=e.event.startStr,s=e.event.endStr,c=e.event.extendedProps.guest,h=e.event.extendedProps.location,u=e.event.extendedProps.description,f=e.event.url,p=e.event.extendedProps.image,m=n.find(".ha-ec-event-title"),g=n.find(".ha-ec-event-time-wrap"),v=n.find(".ha-ec-event-guest-wrap"),y=n.find(".ha-ec-event-location-wrap"),w=n.find(".ha-ec-popup-desc"),_=n.find(".ha-ec-popup-readmore-link"),b=n.find(".ha-ec-popup-image");if(b.css("display","none"),m.css("display","none"),g.css("display","none"),v.css("display","none"),y.css("display","none"),w.css("display","none"),_.css("display","none"),n.addClass("ha-ec-popup-ready"),p&&(b.removeAttr("style"),b.find("img").attr("src",p),b.find("img").attr("alt",o)),o&&(m.removeAttr("style"),m.html(o)),c&&(v.removeAttr("style"),v.find("span.ha-ec-event-guest").html(c)),h&&(y.removeAttr("style"),y.find("span.ha-ec-event-location").html(h)),u&&(w.removeAttr("style"),w.html(u)),!0!==a){g.removeAttr("style"),r=Date.parse(t(r)),s=Date.parse(t(s));var S=i(t(r)),k="Invalid Data";r<s&&(k=i(t(s))),g.find("span.ha-ec-event-time").html(S+" - "+k)}else g.removeAttr("style"),g.find("span.ha-ec-event-time").html(d);f&&(_.removeAttr("style"),_.attr("href",f),"on"===e.event.extendedProps.external&&_.attr("target","_blank"),"on"===e.event.extendedProps.nofollow&&_.attr("rel","nofollow"))}},dateClick:function(e){itemDate=e.date.toUTCString()}};new FullCalendar.Calendar(t[0],c).render(),e.find(".ha-ec-popup-wrapper").on("click",function(e){e.stopPropagation(),e.target!==e.currentTarget&&e.target!=i[0]&&e.target!=i.find(".eicon-editor-close")[0]||n.addClass("ha-ec-popup-removing").removeClass("ha-ec-popup-ready")})}};elementorFrontend.hooks.addAction("frontend/element_ready/ha-slider.default",function(e){elementorFrontend.elementsHandler.addHandler(s,{$element:e})}),elementorFrontend.hooks.addAction("frontend/element_ready/ha-carousel.default",function(e){elementorFrontend.elementsHandler.addHandler(s,{$element:e})}),elementorFrontend.hooks.addAction("frontend/element_ready/ha-horizontal-timeline.default",function(e){elementorFrontend.elementsHandler.addHandler(s,{$element:e,autoplay:!1,container:".ha-horizontal-timeline-wrapper",navigation:"arrow",arrows:!0})}),e("[data-ha-element-link]").on("click.onWrapperLink",function(){var t,n,i=e(this),a=i.data("ha-element-link"),o=i.data("id"),r=document.createElement("a");r.id="happy-addons-wrapper-link-"+o,r.href=a.url,r.target=a.is_external?"_blank":"_self",r.rel=a.nofollow?"nofollow noreferer":"",r.style.display="none",document.body.appendChild(r),t=document.getElementById(r.id),t.click(),n=setTimeout(function(){document.body.removeChild(t),clearTimeout(n)})});var _=function(e){e.hasClass("elementor-element-edit-mode")&&e.addClass("ha-has-bg-overlay")},b={"ha-image-compare.default":o,"ha-number.default":l,"ha-skills.default":d,"ha-fun-factor.default":f,"ha-bar-chart.default":p,"ha-twitter-feed.default":m,"ha-threesixty-rotation.default":y,"ha-data-table.default":v,widget:_,"ha-event-calendar.default":w};e.each(b,function(e,t){elementorFrontend.hooks.addAction("frontend/element_ready/"+e,t)});var S={"ha-image-grid.default":c,"ha-justified-gallery.default":h,"ha-news-ticker.default":u,"ha-post-tab.default":g};e.each(S,function(e,t){elementorFrontend.hooks.addAction("frontend/element_ready/"+e,function(e){elementorFrontend.elementsHandler.addHandler(t,{$element:e})})})})}(jQuery);
;