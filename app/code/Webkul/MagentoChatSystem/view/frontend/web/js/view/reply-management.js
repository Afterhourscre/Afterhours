/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_MagentoChatSystem
 * @author    Webkul
 * @copyright Copyright (c)  Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define([
    'jquery',
    'mage/template',
    'uiComponent',
    'mage/validation',
    'ko',
    'Webkul_MagentoChatSystem/js/model/reply',
    'Webkul_MagentoChatSystem/js/socket.io',
    'Webkul_MagentoChatSystem/js/action/save-message',
    'Webkul_MagentoChatSystem/js/action/assign-chat',
    'Webkul_MagentoChatSystem/js/action/assignment-check'
], function (
    $,
    mageTemplate,
    Component,
    validation,
    ko,
    replyModel,
    io,
    saveMessageAction,
    assignChatAction,
    assignCheckAction
) {
    'use strict';
    var canChat = ko.observable(window.chatboxConfig.customerData.chatStatus);
    return Component.extend({
        options: {},
        customerId: replyModel.customerId(),
        uniqueId: window.chatboxConfig.customerData.uniqueId,
        chatHistory: window.chatboxConfig.customerData.messages,
        initialize: function () {
            var self = this;
            this._super();
            this.resTmpl = mageTemplate('#reply_admin_template');
            this.repTmpl = mageTemplate('#reply_client_template');
            this.notifyTmpl = mageTemplate('#notification-template');
            replyModel.clientReply.subscribe(function (clientReply) {
                var saveMsgData = {
                    customer_id: replyModel.customerId(),
                    unique_id: replyModel.customerUniqueId(),
                    receiver_id: replyModel.receiverId(),
                    receiver_unique_id: replyModel.receiverUniqueId()
                }
                // check if assigned agent changed.
                assignCheckAction(saveMsgData).always(function (params) {
                    self._createReplyData(clientReply);
                });

            });
            replyModel.adminResponse.subscribe(function (response) {
                self._createResponseData(response);
            });
            replyModel.profileImageUrl.subscribe(function (profileData) {
                self._updateProfile(profileData);
            });
            replyModel.clientStatusChange.subscribe(function (status) {
                self._updateStatusSend(status);
            });
            self._setChatHistory();
            if ($("#chatbox-component div.chat-history").length) {
                $("#chatbox-component div.chat-history").animate({
                    scrollTop: $("#chatbox-component div.chat-history")[0].scrollHeight
                });
            }

        },
        _createReplyData: function (clientReply) {
            var self = this;
            var clientData = {
                sender: 'customer',
                customerId: replyModel.customerId(),
                uniqueId: replyModel.customerUniqueId(),
                customerName: replyModel.customerName(),
                email: replyModel.customerEmail(),
                message: clientReply.message,
                image: replyModel.profileImageUrl(),
                time: this.getFormateTime(),
                date: this.getDate(),
                receiverUniqueId: replyModel.receiverUniqueId(),
                receiver: replyModel.receiverId(),
                chat_status: clientReply.status,
                type: clientReply.type
            };
            if ($.trim(clientData.message.replace(/[<]br[^>]*[>]/gi, "")).length) {
                var saveMsgData = {
                    customer_id: replyModel.customerId(),
                    receiver_id: replyModel.receiverId(),
                    receiver_unique_id: replyModel.receiverUniqueId(),
                    dateTime: this.getDate() + this.getTime(),
                    message: clientReply.message,
                    type: clientData.type
                };

                if (clientData.type != 'text') {
                    saveMessageAction(saveMsgData).always(function (response) {
                        var data = $.parseJSON(response);
                        if (data.errors) {
                            replyModel.agentGoesOff(true);
                            replyModel.agentGoesOffError(data.msg);
                        } else {
                            replyModel.agentGoesOff(false);
                            replyModel.agentGoesOffError('');
                        }
                        replyModel.receiverId(data.receiver_id);
                        replyModel.receiverUniqueId(data.receiver_unique_id);
                        clientData.message = data.message;
                        self._sendNewMessage(clientData);
                    });
                } else {
                    this._sendNewMessage(clientData);
                    saveMessageAction(saveMsgData).always(function (response) {
                        var data = $.parseJSON(response);
                        if (data.errors) {
                            replyModel.agentGoesOff(true);
                            replyModel.agentGoesOffError(data.msg);
                        } else {
                            replyModel.agentGoesOff(false);
                            replyModel.agentGoesOffError('');
                        }
                        replyModel.receiverId(data.receiver_id);
                        replyModel.receiverUniqueId(data.receiver_unique_id);
                    });
                }
            }
        },
        _sendNewMessage: function (reply) {
            var data = {},
                repTmpl;

            if (data !== 'undefined') {
                if (parseInt(replyModel.adminStatus()) == 0) {
                    reply.statusError = true;
                } else {
                    reply.statusError = false;
                }

                repTmpl = this.repTmpl({
                    data: reply
                });
                $(repTmpl).appendTo($('.chat-history'));

                replyModel.callEmojify('chatbox-component');
            }
            if ($("#chatbox-component div.chat-history").length) {
                $("#chatbox-component div.chat-history").animate({
                    scrollTop: $("#chatbox-component div.chat-history")[0].scrollHeight
                });
            }
            var socket = replyModel.getSocketObject();
            /*if (reply.type == 'file') {
                socket.emit('user file', reply);
            }*/
            socket.emit('newCustomerMessageSumbit', reply);
        },

        /**
         * display response to customer sent by Admin
         */
        _createResponseData: function (response) {
            var data = {},
                resTmpl,
                notifyTmpl;
            response.adminImage = window.chatboxConfig.adminImage;
            if (response !== 'undefined') {
                $('body').find('.chat-message-notification').remove();
                resTmpl = this.resTmpl({
                    data: response
                });
                $(resTmpl)
                    .appendTo($('.chat-history'));

                replyModel.callEmojify('chatbox-component');
                if (response.receiver == replyModel.customerId()) {
                    replyModel.showNotification(response);
                }

                if ($('#chatbox-component').find('.wk_chat_sound').hasClass('enable')) {
                    $('#chatbox-component').find('#myAudio').get(0).play();
                }

                this.blinkTab(response.message);
            }
            if ($("#chatbox-component div.chat-history").length) {
                $("#chatbox-component div.chat-history").animate({
                    scrollTop: $("#chatbox-component div.chat-history")[0].scrollHeight
                });
            }
            replyModel.callEmojify('chatbox-component');
        },

        /**
         * blink browser tab with message
         */
        blinkTab: function (message) {
            var oldTitle = document.title,
                timeoutId,
                blink = function () {
                    document.title = document.title == message ? ' ' : message;
                },
                clear = function () {
                    clearInterval(timeoutId);
                    document.title = oldTitle;
                    window.onmousemove = null;
                    timeoutId = null;
                };

            if (!timeoutId) {
                timeoutId = setInterval(blink, 1000);
                window.onmousemove = clear;
            }
            replyModel.callEmojify('chatbox-component');
        },
        /**
         * set chat history, on page load
         */
        _setChatHistory: function () {
            var self = this;
            if (!$.isEmptyObject(self.chatHistory)) {
                $.each(self.chatHistory, function (i, v) {
                    var sender = 'admin';
                    var name = v.senderName;
                    if (self.uniqueId === v.sender_unique_id) {
                        sender = 'customer'
                        name = window.chatboxConfig.customerData.firstname + ' ' +
                            window.chatboxConfig.customerData.lastname;
                    }
                    var data = {
                        customerName: name,
                        message: v.message,
                        time: v.time,
                        date: v.date,
                        sender: sender,
                        type: v.type,
                        changeDate: v.changeDate
                    };
                    replyModel.setChatHistory(data);
                });
            }

            replyModel.callEmojify('chatbox-component');
        },
        _updateProfile: function (profileData) {
            /*$('#chatbox-component div.chat-history .chat-message-client img').each(function (i, value) {
                 $(this).attr('src', profileData);
             });*/
        },

        /**
         * send event when chat status changed by customer
         */
        _updateStatusSend: function (status) {
            var self = this;
            var statusData = {
                status: status,
                customerId: self.customerId,
                receiver: replyModel.receiverId()
            };
            var socket = replyModel.getSocketObject();
            socket.emit('updateStatus', statusData);
        },
        getDate: function () {
            var now = new Date();
            var year = "" + now.getFullYear();
            var month = "" + (now.getMonth() + 1);
            if (month.length == 1) {
                month = "0" + month;
            }
            var day = "" + now.getDate();
            if (day.length == 1) {
                day = "0" + day;
            }
            return year + "-" + month + "-" + day + " ";
        },
        getTime: function () {
            var now = new Date();
            var hour = "" + now.getHours();
            if (hour.length == 1) {
                hour = "0" + hour;
            }
            var minute = "" + now.getMinutes();
            if (minute.length == 1) {
                minute = "0" + minute;
            }
            var second = "" + now.getSeconds();
            if (second.length == 1) {
                second = "0" + second;
            }
            return hour + ":" + minute;
        },
        getFormateTime: function () {
            var now = new Date();
            var hours = now.getHours() > 12 ? now.getHours() - 12 : now.getHours();
            var am_pm = now.getHours() >= 12 ? "PM" : "AM";
            hours = hours < 10 ? "0" + hours : hours;

            var minute = "" + now.getMinutes();
            if (minute.length == 1) {
                minute = "0" + minute;
            }
            var second = "" + now.getSeconds();
            if (second.length == 1) {
                second = "0" + second;
            }
            return hours + ":" + minute + " " + am_pm;;
        }
    });
});