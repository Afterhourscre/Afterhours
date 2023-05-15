/*!
 * jquery.instagramFeed
 *
 * @version 1.2.0
 *
 * @author Javier Sanahuja Liebana <bannss1@gmail.com>
 * @contributor csanahuja <csanahuja@gmail.com>
 *
 * https://github.com/jsanahuja/jquery.instagramFeed
 *
 */
(function (factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module depending on jQuery.
        define(['jquery'], factory);
    } else {
        // No AMD. Register plugin with global jQuery object.
        factory(jQuery);
    }
}(function($){
    var defaults = {
        'host': "https://www.instagram.com/",
        'username': '',
        'tag': '',
        'container': '',
        'after': null,
        'items': 6,
        'image_new_tab': '',
        'image_padding': '',
        'image_size': 640,
        'image_alt_tag': 0,
        'image_alt_label': ''
    };
    var image_sizes = {
        "150": 0,
        "240": 1,
        "320": 2,
        "480": 3,
        "640": 4
    };

    $.instagramFeed = function(opts){
        var options = $.fn.extend({}, defaults, opts);
        if(options.username == "" && options.tag == ""){
            console.error("Instagram Feed: Error, no username or tag found.");
            return false;
        }
        if(typeof options.get_raw_json !== "undefined"){
            console.warn("Instagram Feed: get_raw_json is deprecated. See use get_data instead");
            options.get_data = options.get_raw_json;
        }
        if(options.container == ""){
            console.error("Instagram Feed: Error, no container found.");
            return false;
        }

        var is_tag = options.username == "",
            url = is_tag ? options.host + "explore/tags/"+ options.tag : options.host + options.username;

        $.get(url, function(data){
            data = data.split("window._sharedData = ")[1].split("<\/script>")[0];
            data = JSON.parse(data.substr(0, data.length - 1));
            data = data.entry_data.ProfilePage || data.entry_data.TagPage;
            data = data[0].graphql.user || data[0].graphql.hashtag;

            var html = "";

            //image size
            var image_index = 'original';
            if (options.image_size != 'original') {
                image_index = typeof image_sizes[options.image_size] !== "undefined" ? image_sizes[options.image_size] : image_sizes[640];
            }

            if(typeof data.is_private !== "undefined" && data.is_private === true){
                html += "<p class='instagram_private'><strong>This profile is private</strong></p>";
            }else{
                var imgs = (data.edge_owner_to_timeline_media || data.edge_hashtag_to_media).edges;
                max = (imgs.length > options.items) ? options.items : imgs.length;

                for(var i = 0; i < max; i++){
                    var url = "https://www.instagram.com/p/" + imgs[i].node.shortcode;
                    var image = imgs[i].node.display_url;

                    if (image_index != 'original') {
                        image = imgs[i].node.thumbnail_resources[image_index].src;
                    }

                    html +=     "    <a href='" + url + "' rel='noopener'" + options.image_new_tab + ">";
                    html +=     "       <img class='"+ options.image_padding + "'" + " src='" + image + "' ";
                    switch (options.image_alt_tag) {
                        case 1:
                            html +=     " alt='" + imgs[i].node.accessibility_caption + "'";
                            break;
                        case 2:
                            html +=     " alt='" + options.image_alt_label + "'";
                            break;
                    }
                    html +=     " />";
                    html +=     "    </a>";
                }
            }

            $(options.container).html(html);
        }).fail(function(e){
            console.error("Instagram Feed: Unable to fetch the given user/tag. Instagram responded with the status code: ", e.status);
        }).done(function (e) {
            if ((options.after != null) && typeof options.after === 'function') {
                var that = this;
                setTimeout(function(){ options.after.call(that); }, 1000);
            }
        });
        return true;
    };

}));
