/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'jquery',
    'uiComponent',
    'mage/storage',
    'mage/translate'
], function ($, Component, storage, $t) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Aheadworks_Faq/article/voting',
            canDisplay: false,
            isRateBeforeVotingEnabled: false,
            isRateAfterVotingEnabled: false,
            canDisplayRatingMessage: false,
            votingUrl: 'faq/article/helpfulness',
            beFirstMessage: 'Be the first to vote!',
            ratingMessage: '({rating}% of other people think it was helpful)',
            articleId: null,
            imports: {
                isVoted: '${ $.provider }:data.isVoted',
                isVoteLike: '${ $.provider }:data.isVoteLike',
                helpfulnessRating: '${ $.provider }:data.helpfulnessRating'
            }
        },

        /**
         * @inheritdoc
         */
        initObservable: function () {
            this._super()
                .observe({
                    isVoted: false,
                    isVoteLike: false,
                    isLoading: false,
                    helpfulnessRating: 0
                });

            return this;
        },

        /**
         * Get rating message
         *
         * @returns {string}
         */
        getRatingMessage: function () {
            var message = $t(this.beFirstMessage);

            if (this.canDisplayRatingMessage || this.isVoted()) {
                message = $t(this.ratingMessage);
                message = message.replace('{rating}', this.helpfulnessRating());
            }
            return message;
        },

        /**
         * On like click
         */
        onLikeClick: function () {
            this._sendVote(true);
        },

        /**
         * On dislike click
         */
        onDislikeClick: function () {
            this._sendVote(false);
        },

        /**
         * Send vote
         *
         * @param {boolean} voteStatus
         * @private
         */
        _sendVote: function (voteStatus) {
            var payload = {
                    isLike: voteStatus,
                    articleId: this.articleId
                },
                serviceUrl = this.votingUrl,
                me = this;

            this.isLoading(true);

            return storage.post(
                serviceUrl,
                JSON.stringify(payload),
                true
            ).done(
                function (response) {
                    if (response.success) {
                        me.isVoted(true);
                        me.isVoteLike(voteStatus);
                        me.helpfulnessRating(response.helpfulness_rating);
                    }
                }
            ).fail(
                function () {
                    me.isVoted(false);
                }
            ).always(
                function () {
                     me.isLoading(false);
                }
            );
        }
    });
});