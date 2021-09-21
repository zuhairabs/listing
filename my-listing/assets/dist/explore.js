!function(e){"function"==typeof define&&define.amd?define("explore",e):e()}(function(){"use strict";function l(e){return(l="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e})(e)}Vue.component("results-header",{data:function(){return{currentPage:0,totalPages:0,hasPrevPage:!1,hasNextPage:!1,foundPosts:null,resultCountText:"",target:null}},mounted:function(){var i=this;this.$nextTick(function(){i.$root.$on("update-results",function(e){var t=i.$root.activeType;i.target=t.taxonomies[t.tab]?t.taxonomies[t.tab]:t.filters,i.resultCountText=e.showing,i.totalPages=parseInt(e.max_num_pages,10),i.currentPage=parseInt(i.target.page,10),i.hasPrevPage=0<=i.currentPage-1,i.hasNextPage=i.currentPage+1<i.totalPages,i.foundPosts=parseInt(e.found_posts,10)})})},methods:{getNextPage:function(){this.target.page=this.currentPage+1,this.target.preserve_page=!0,this.$root._getListings("next-page")},getPrevPage:function(e){this.target.page=this.currentPage-1,this.target.preserve_page=!0,this.$root._getListings("previous-page")}}}),MyListing.Explore_Init=function(){var e=document.querySelector(".cts-explore");e&&!e.dataset.inited&&(e.dataset.inited=!0,MyListing.Explore=new Vue({el:e,data:{activeType:!1,types:CASE27_Explore_Settings.ListingTypes,template:CASE27_Explore_Settings.Template,loading:!1,last_request:null,found_posts:null,state:{mobileTab:CASE27_Explore_Settings.ActiveMobileTab},isMobile:window.matchMedia("screen and (max-width: 1200px)").matches,map:!1,mapExpanded:!1,baseUrl:CASE27_Explore_Settings.ExplorePage?CASE27_Explore_Settings.ExplorePage:window.location.href.replace(window.location.search,"")},beforeMount:function(){var i=this;window.matchMedia("screen and (max-width: 1200px)").addListener(function(e){return i.isMobile=e.matches}),this.setType(CASE27_Explore_Settings.ActiveListingType),this.isMobile&&Object.keys(this.types).forEach(function(e){var t=this.types[e],i=t.taxonomies[t.tab];i&&i.activeTermId||(t.tab="search-form")}.bind(this));function e(e){var t=i.isMobile?jQuery(document).scrollTop():jQuery(".finder-listings").scrollTop();window.history.replaceState(null,null,i.updateUrlParameter(window.location.href,"sp",Math.round(t)))}window.addEventListener("beforeunload",e),window.addEventListener("unload",e),this.jQueryReady()},methods:{setType:function(e){this.types[e]&&(this.activeType=this.types[e])},getListings:MyListing.Helpers.debounce(function(e,t){this.isMobile&&!0!==t||this._getListings(e)},500),getListingsShort:MyListing.Helpers.debounce(function(e,t){this.isMobile&&!0!==t||this._getListings(e)},250),filterChanged:function(e,t){this.isMobile&&"primary-filter"!==t.location&&!t.forceGet&&!this.activeType.is_first_load||(!1===t.shouldDebounce?this._getListings("".concat(t.filterType,":").concat(t.filterKey)):this.getListings("".concat(t.filterType,":").concat(t.filterKey),!0))},_getListings:function(t){"dev"===CASE27.env&&console.log("%c Get Listings ["+t+"]","background-color: darkred; color: #fff;"),this.loading=!0;var i=this,a=this.activeType;this.activeType.filters.preserve_page||(this.activeType.filters.page=0);var e=this.activeType.taxonomies[this.activeType.tab];if(void 0!==e&&0!==e.activeTermId)var s={context:"term-search",taxonomy:e.tax,term:e.activeTermId,page:e.page,sort:this.activeType.filters.sort,search_location:this.activeType.filters.search_location,lat:this.activeType.filters.lat,lng:this.activeType.filters.lng,proximity:this.activeType.filters.proximity,proximity_units:this.activeType.filters.proximity_units};else s=this.activeType.filters;var r={form_data:s,listing_type:this.activeType.slug,listing_wrap:CASE27_Explore_Settings.ListingWrap},n=JSON.stringify(r);if(this.activeType.last_response&&n===this.activeType.last_request_body){"dev"===CASE27.env&&console.warn("Ignoring call to getListings, no search arguments have changed.");var o=this.activeType.last_response;return this.updateUrl(),void setTimeout(function(){this.loading=!1,this.activeType.last_response&&this.updateView(o,t)}.bind(this),200)}"dev"===CASE27.env&&this.activeType.last_request_body&&(console.log("%c Getting listings, arguments diff:","color: #a370ff"),console.table(objectDiff(JSON.parse(this.activeType.last_request_body).form_data,JSON.parse(n).form_data))),this.updateUrl(),jQuery.ajax({url:CASE27.mylisting_ajax_url+"&action=get_listings&security="+CASE27.ajax_nonce,type:"GET",dataType:"json",data:r,beforeSend:function(e,t){i.last_request&&i.last_request.abort(),i.last_request=e},success:function(e){"object"===l(e)&&("search-form"===a.tab&&(a.last_response=e,a.last_request_body=n),a.slug===i.activeType.slug&&(i.loading=!1,i.updateView(e,t)))}})},updateUrl:function(){if(!window.history||CASE27_Explore_Settings.DisableLiveUrlUpdate)return!1;var s=this.activeType.filters,r={};if(!window.location.search&&CASE27_Explore_Settings.IsFirstLoad)return!1;if(0!==this.activeType.index&&(r.type=this.activeType.slug),this.activeType.tab!==this.activeType.defaultTab&&(r.tab=this.activeType.tab),"search-form"===this.activeType.tab&&Object.keys(s).forEach(function(e){var t=s[e],i=e;if("proximity_units"==e)return!1;if(("lat"===e||"lng"===e)&&t&&void 0!==s.search_location&&s.search_location.length){i=e;var a=-1<t.toString().indexOf("-")?9:8;t=t.toString().substr(0,a)}if(!("proximity"!=e||s.lat&&s.lng))return!1;"page"===e&&0<t&&(t+=1,i="pg"),t&&void 0!==t.length&&t.length?r[i]=t:"number"==typeof t&&t&&(r[i]=t)}),this.currentTax&&this.currentTax.activeTerm){var e=this.currentTax.activeTerm.link;if(0<this.currentTax.page)e=this.updateUrlParameter(e,"pg",this.currentTax.page+1);window.history.replaceState(null,null,e)}else{var t=jQuery.param(r).replace(/%2C/g,",");window.history.replaceState(null,null,this.baseUrl+(t.trim().length?"?"+t:""))}},updateView:function(e,t){var i=this,a=e,s=this;CASE27_Explore_Settings.IsFirstLoad=!1,this.activeType.is_first_load=!1,this.found_posts=e.found_posts,this.$emit("update-results",e,t),this.activeType.filters.preserve_page=!1,jQuery(".finder-listings .results-view").length&&jQuery(".finder-listings .results-view").html(a.html),jQuery(".fc-type-2-results").length&&jQuery(".fc-type-2-results").html(a.html),setTimeout(function(){void 0!==jQuery(".results-view.grid").data("isotope")&&jQuery(".results-view.grid").isotope("destroy");var e={itemSelector:".grid-item"};jQuery("body").hasClass("rtl")&&(e.originLeft=!1),jQuery(".results-view.grid").isotope(e)},10),jQuery(".lf-background-carousel").owlCarousel({margin:20,items:1,loop:!0}),jQuery('[data-toggle="tooltip"]').tooltip({trigger:"hover"}),jQuery(".c27-explore-pagination").length&&(jQuery(".c27-explore-pagination").html(a.pagination),jQuery(".c27-explore-pagination a").each(function(){var e=jQuery(this).data("page"),t=window.location.href;jQuery(this).attr("href",s.updateUrlParameter(t,"pg",e))})),50<CASE27_Explore_Settings.ScrollPosition?setTimeout(function(){var e=CASE27_Explore_Settings.ScrollPosition;i.isMobile?jQuery(document).scrollTop(e):jQuery(".finder-listings").scrollTop(e),CASE27_Explore_Settings.ScrollPosition=0},30):(jQuery(".finder-container .fc-one-column").length&&CASE27_Explore_Settings.ScrollToResults&&jQuery(".finder-container .fc-one-column").animate({scrollTop:jQuery(".finder-search").outerHeight()}),window.matchMedia("(min-width: 1200px)").matches?(jQuery(".finder-container .fc-default .finder-listings").length&&jQuery(".finder-container .fc-default .finder-listings").animate({scrollTop:0}),"pagination"===t&&!CASE27_Explore_Settings.ScrollToResults&&jQuery(".finder-container .fc-one-column").length&&jQuery(".finder-container .fc-one-column").animate({scrollTop:jQuery(".finder-search").outerHeight()}),"pagination"===t&&jQuery("html, body").animate({scrollTop:jQuery(this.$el).offset().top})):"results"===this.state.mobileTab&&this._resultsScrollTop()),this.updateMap()},_resultsScrollTop:function(){jQuery("html, body").animate({scrollTop:jQuery("#c27-explore-listings").offset().top-100},"slow")},setupMap:function(){var e=jQuery(this.$el).find(".finder-map .map").attr("id");if(!MyListing.Maps.getInstance(e))return!1;this.map=MyListing.Maps.getInstance(e).instance;var t=this.map;if(MyListing.Geocoder.setMap(t),navigator.geolocation){var i=!1,a=!1,s=document.getElementById("explore-map-location-ctrl");s.addEventListener("click",function(){navigator.geolocation.getCurrentPosition(function(e){a=a||new MyListing.Maps.Marker({position:new MyListing.Maps.LatLng(e.coords.latitude,e.coords.longitude),map:t,template:{type:"user-location"}}),t.setZoom(CASE27_Explore_Settings.Map.default_zoom),t.setCenter(a.getPosition())},function(e){(i=i||new MyListing.Dialog({message:CASE27.l10n.geolocation_failed})).visible||(i.refresh(),i.show())})}),this.map.addControl(s)}},updateMap:function(){var s=this;if(s.map){s.map.$el.removeClass("mylisting-map-loading"),s.map.removeMarkers(),s.map.trigger("updating_markers");var r=new MyListing.Maps.LatLngBounds;jQuery(this.$el).find(".results-view .lf-item-container").each(function(e,t){var i=jQuery(t);if(i.data("latitude")&&i.data("longitude")){var a=new MyListing.Maps.Marker({position:new MyListing.Maps.LatLng(i.data("latitude"),i.data("longitude")),map:s.map,popup:new MyListing.Maps.Popup({content:'<div class="lf-item-container lf-type-2">'+i.html()+"</div>"}),template:{type:"advanced",thumbnail:i.data("thumbnail"),icon_name:i.data("category-icon"),icon_background_color:i.data("category-color"),icon_color:i.data("category-text-color"),listing_id:i.data("id")}});s.map.markers.push(a),r.extend(a.getPosition())}}),r.empty()||s.map.fitBounds(r),17<s.map.getZoom()&&s.map.setZoom(17),(s.map.markers.length<1||r.empty())&&(s.map.setZoom(CASE27_Explore_Settings.Map.default_zoom),s.map.setCenter(new MyListing.Maps.LatLng(CASE27_Explore_Settings.Map.default_lat,CASE27_Explore_Settings.Map.default_lng))),s.map.trigger("updated_markers")}else if(document.getElementsByClassName("finder-map").length)var e=setInterval(function(){s.map&&(clearInterval(e),s.updateMap())},200)},resetFilters:function(e){if(e&&e.target){var t=jQuery(e.target).find("i");t.removeClass("fa-spin"),setTimeout(function(){t.addClass("fa-spin")},5)}this.$emit("reset-filters"),this.$emit("reset-filters:"+this.activeType.slug)},jQueryReady:function(){var a=this;jQuery(function(i){i("body").on("click",".c27-explore-pagination a",function(e){e.preventDefault();var t=parseInt(i(this).data("page"),10)-1;a.activeType.taxonomies[a.activeType.tab]&&(a.activeType.taxonomies[a.activeType.tab].page=t),a.activeType.filters.page=t,a.activeType.filters.preserve_page=!0,a._getListings("pagination")}),jQuery(".col-switch").click(function(e){a.map.trigger("resize")}),jQuery("body").on("mouseenter",".results-view .lf-item-container.listing-preview",function(){jQuery(".marker-container .marker-icon."+jQuery(this).data("id")).addClass("active")}),jQuery("body").on("mouseleave",".results-view .lf-item-container.listing-preview",function(){jQuery(".marker-container .marker-icon."+jQuery(this).data("id")).removeClass("active")})})},termsExplore:function(e,t,a){var s=this;this.activeType.tab=e;var r=this.activeType.taxonomies[this.activeType.tab],i=(a=a||!1,this.activeType.tabs[this.activeType.tab]||{});if(!r.termsLoading){"active"===t&&(t=r.activeTerm),r.activeTerm=!1,a||(r.terms=!1),"object"===l(t)&&t.term_id?(r.activeTermId=t.term_id,r.activeTerm=t):isNaN(parseInt(t,10))?r.activeTermId=0:r.activeTermId=t,this.activeType.filters.preserve_page||(this.currentTax.page=0),jQuery(".search-filters.type-id-"+this.activeType.id+" .orderby-filter").hasClass("has-proximity-clause")?this.$emit("request-location:"+this.activeType.slug):this._getListings("terms-explore"),void 0===CASE27_Explore_Settings.TermCache[this.activeType.slug]&&(CASE27_Explore_Settings.TermCache[this.activeType.slug]={}),void 0===CASE27_Explore_Settings.TermCache[this.activeType.slug][r.tax]&&(CASE27_Explore_Settings.TermCache[this.activeType.slug][r.tax]={});var n=CASE27_Explore_Settings.TermCache[this.activeType.slug][r.tax][r.activeTermId];if(n){if((!a||a&&!n.hasMore)&&void 0!==n.pages[r.termsPage])return r.activeTerm=n.details,r.hasMore=n.hasMore,r.terms=[],Object.keys(n.pages).forEach(function(t){Object.keys(n.pages[t]).forEach(function(e){r.terms.push(n.pages[t][e])})}),void this.updateUrl();r.termsPage=n.currentPage+1}else r.termsPage=0;r.termsLoading=!0,jQuery.ajax({url:CASE27.mylisting_ajax_url+"&action=explore_terms&security="+CASE27.ajax_nonce,type:"GET",dataType:"json",data:{taxonomy:r.tax,parent_id:r.activeTermId,type_id:this.activeType.id,page:r.termsPage,per_page:CASE27_Explore_Settings.TermSettings.count,orderby:i.orderby,order:i.order,hide_empty:i.hide_empty?"yes":"no"},success:function(t){if(r.termsLoading=!1,1!=t.success)return new MyListing.Dialog({message:t.message});var i=CASE27_Explore_Settings.TermCache[s.activeType.slug][r.tax];i[r.activeTermId]||(i[r.activeTermId]={details:{},pages:{}}),r.activeTerm=t.details,r.hasMore=t.more,i[r.activeTermId].details=t.details,i[r.activeTermId].hasMore=t.more,i[r.activeTermId].currentPage=r.termsPage,i[r.activeTermId].pages[r.termsPage]=t.children,a?Object.keys(t.children).forEach(function(e){r.terms.push(t.children[e])}):(r.terms=[],Object.keys(i[r.activeTermId].pages).forEach(function(t){Object.keys(i[r.activeTermId].pages[t]).forEach(function(e){r.terms.push(i[r.activeTermId].pages[t][e])})})),s.updateUrl()}})}},termsGoBack:function(e){this.termsExplore(this.activeType.tab,e.parent),0!==parseInt(e.parent,10)&&(this.currentTax.page=0,this.getListings("terms-go-back"))},updateUrlParameter:function(e,t,i){var a=e.indexOf("#"),s=-1===a?"":e.substr(a);e=-1===a?e:e.substr(0,a);var r=new RegExp("([?&])"+t+"=.*?(&|$)","i"),n=-1!==e.indexOf("?")?"&":"?";return(e=e.match(r)?e.replace(r,"$1"+t+"="+i+"$2"):e+n+t+"="+i)+s},hasValidLocation:function(e){if(!this.types[e])return!1;var t=this.types[e].filters;return t.lat&&t.lng&&t.search_location},toggleMap:function(e){var t=this;this.mapExpanded=e,setTimeout(function(){return t.map.trigger("refresh")},5)}},computed:{currentTax:function(){return this.activeType.taxonomies[this.activeType.tab]},showBackToFilters:function(){var e=this.activeType.tabs;return e["search-form"]&&(this.isMobile||1===Object.keys(e).length)},containerStyles:function(){var e="top:0;";if(!this.isMobile){var t=document.querySelector("header.header");if(t){var i=t.getBoundingClientRect();e+="height: calc(100vh - ".concat(Math.round(i.height+i.y),"px);")}}return e}},watch:{activeType:function(){if(this.activeType)if("search-form"===this.activeType.tab){var e=jQuery(".search-filters.type-id-"+this.activeType.id+" .orderby-filter");jQuery(".search-filters.type-id-"+this.activeType.id+" .form-location-autocomplete");this.activeType.last_response?(this.loading=!1,this.updateUrl(),this.updateView(this.activeType.last_response,"switch-listing-type")):e.hasClass("has-proximity-clause")||this.activeType.filters.search_location?this.$emit("request-location:"+this.activeType.slug):this._getListings("switch-listing-type")}else{var t=parseInt(this.activeType.taxonomies[this.activeType.tab].activeTermId,10);isNaN(t)||0===t?this.termsExplore(this.activeType.tab):this.termsExplore(this.activeType.tab,"active")}}}}))},MyListing.Explore_Init(),document.addEventListener("DOMContentLoaded",MyListing.Explore_Init)});
