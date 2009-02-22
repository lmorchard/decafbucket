/**
 * Some JS-based hacks and fiddlings for the bucket.
 */
var Bucket = function() {

    // Change this to point at your OPML blog.
    var BLOG_URL = 
        'http://decafbad.com/bucket/';

    var COMMENT_ICON_SRC =
        "http://decafbad.com/images/icon_reaction.gif";

    return {

        /**
         * Prepare the bucket code for launch.
         */
        init: function() {
            window.addEvent('domready', this.onReady.bind(this));
            return this;
        },

        /**
         * Once the DOM is ready, mess with it.
         */
        onReady: function() {

            this.tweakSomeContent();
            this.injectHaloscanComments();

        },

        /**
         * Tweak some content that I'm either too lazy or find too hard to
         * clean up in the content or templates.
         */
        tweakSomeContent: function() {
            $$('.entry .body li:first-child').addClass('first');
            $$('.entry .body li:last-child').addClass('last');
            $$('.entry .body li:only-child').addClass('only');

        },

        /**
         * Inject some links to haloscan comments
         */
        injectHaloscanComments: function() {

            // Look for all of the permalinks on the page.
            var permalinks = $$('.entry .title a');

            // Attach comment icons after each permalink on the page.
            permalinks.each(function(permalink) {

                // Try to derive a unique ID from post permalink that is 
                // short and not nasty with URL-funk.
                var permalink_id = permalink.get('text');
                permalink_id = encodeURIComponent(permalink_id);
                permalink_id = permalink_id.replace(/%/g, '_');
                permalink_id = permalink_id.replace(/-/g, '_');

                // Look up the comment count for this ID.
                var comment_count = hs[permalink_id] ? hs[permalink_id] : 0;

                // Build the comment link in another function, so as to preserve
                // the permalink_id and comment_count in the closure.
                var comment_link =
                    this.buildHaloscanCommentLink(permalink_id, comment_count);

                // Inject the comment link into the permalink parent.
                permalink.parentNode.grab(
                    document
                        .newElement('span', { 'class': 'comments' })
                        .adopt(
                            document.newTextNode(' '),
                            comment_link
                        )
                )

            }, this);
        },

        buildHaloscanCommentLink: function(permalink_id, comment_count) {

            // Build the DOM node for a comment link.
            // HACK: is javascript:void(null) a good thing to do here? prolly not.
            var comment_link = document
                .newElement('a', {
                    'class':  'commentlink',
                    href:   '#',
                    title:  permalink_id,
                    events: {
                        click: function() { HaloScan(permalink_id) }
                    }
                })
                .adopt(
                    document.newElement('img', {
                        src: COMMENT_ICON_SRC, border: 0
                    }), 
                    document.newTextNode('('+comment_count+
                        ' comment' + (comment_count != 1 ? 's':'') + ')')
                );

            return comment_link;
        },

        EOF:null
    }
}().init();
