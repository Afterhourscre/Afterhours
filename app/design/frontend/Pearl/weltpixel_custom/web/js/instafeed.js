/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'customjquery',
    'pagination'
], function ($) {
  'use strict';

  $(document).ready(function(){
    var feed = $("#instafeed"),
        hashtag = feed.data("hashtag"),
        token = feed.data("token"),
        num_photos = feed.data("quantity");

    $.ajax({
      url: '//www.instagram.com/explore/tags/'+hashtag+'/?__a=1',
      dataType: 'json',
      type: 'GET',
      contentType: 'text/plain',
      cache: false,
      data: {access_token: token, count: num_photos},
      success: function(data) {
        console.log(data);
        feed.append("<h2 class='general-title'></h2>");
        feed.append("<ul></ul>");
        $(data.graphql.hashtag.edge_hashtag_to_media.edges).each(function(index, value) {
          feed.find('ul').append('<li class="list-inline-item insta-foto" id="page'+index+'">' +
              '<img class="img-fluid" src="'+value.node.thumbnail_resources[4].src+'">' +
              '<a href="//www.instagram.com/p/'+value.node.shortcode+'/" class="insta-description" target="_blank"><i class="fa fa-instagram"></i><p> '+value.node.edge_media_to_caption.edges[0].node.text.slice(0, 100)+' ...</p></a>' +
              '</li>');
          return index < num_photos-1;
        });

        feed.find('h2').append('#'+data.graphql.hashtag.name);

        $('#instafeed ul li').paginate(9);

      }
    });
  });

});
