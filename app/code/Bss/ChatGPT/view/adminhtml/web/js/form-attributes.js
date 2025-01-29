/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_ChatGPT
 * @author     Extension Team
 * @copyright  Copyright (c) 2024 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
define([
    'ko',
    'underscore',
    'jquery',
    'mageUtils',
    'Magento_Ui/js/grid/massactions',
    'Magento_Ui/js/modal/modal'
], function (ko, _,$, utils, Massactions, modal) {
    'use strict';

    var urlApi,
        formKey,
        messDefaultProduct,
        messDefaultSeoTitle,
        messDefaultSeoKeyWords,
        messDefaultSeoDescription;
    window.lastVariables = [];
    window.lastMessage = [];
    window.lastRole = [];
    window.lastUseAllAttr = false;
    var listVariables = $("#chatgpt-attributes"),
        roleChatGPT = $("#system_role"),
        promptChatGPT = $("#prompt");
    var selectorAlert = $("#chatgpt-alert");

    return Massactions.extend({
        defaults: {
            template: 'ui/grid/tree-massactions',
            submenuTemplate: 'ui/grid/submenu',
            listens: {
                opened: 'hideSubmenus'
            }
        },

        /**
         * Initializes observable properties.
         *
         * @returns {Massactions} Chainable.
         */
        initObservable: function () {
            this._super()
                .recursiveObserveActions(this.actions());
            urlApi = this.url ?? urlApi;
            formKey = this.form_key ?? formKey;
            messDefaultProduct = this.message_default ? this.message_default.product : messDefaultProduct;
            messDefaultSeoTitle = this.message_default ? this.message_default.seo_title : messDefaultSeoTitle;
            messDefaultSeoKeyWords = this.message_default ? this.message_default.seo_keyword : messDefaultSeoKeyWords;
            messDefaultSeoDescription = this.message_default ? this.message_default.seo_description : messDefaultSeoDescription;
            return this;
        },

        /**
         * Recursive initializes observable actions.
         *
         * @param {Array} actions - Action objects.
         * @param {String} [prefix] - An optional string that will be prepended
         *      to the "type" field of all child actions.
         * @returns {Massactions} Chainable.
         */
        recursiveObserveActions: function (actions, prefix) {
            _.each(actions, function (action) {
                if (prefix) {
                    action.type = prefix + '.' + action.type;
                }

                if (action.actions) {
                    action.visible = ko.observable(false);
                    action.parent = actions;
                    this.recursiveObserveActions(action.actions, action.type);
                }
            }, this);

            return this;
        },

        /**
         * Applies specified action.
         *
         * @param {String} actionIndex - Actions' identifier.
         * @returns {Massactions} Chainable.
         */
        applyAction: function (actionIndex) {
            var action = this.getAction(actionIndex),
                visibility;

            if (action.visible) {
                visibility = action.visible();

                this.hideSubmenus(action.parent);
                action.visible(!visibility);

                return this;
            }

            if (!this.getSelections()['selected'].length || !actionIndex.includes('generate-content-chatgpt')){
                return this._super(actionIndex);
            }
            var data = [];
            data['id'] = actionIndex.slice(actionIndex.indexOf('.')+1, actionIndex.length);
            data['url'] = urlApi;
            data['form_key'] = formKey;
            data['product_ids'] = this.getSelections()['selected'];
            switch(action.type) {
                case "generate-content-chatgpt.meta_title":
                    data['role_default'] = messDefaultSeoTitle.role;
                    data['mess_default'] = messDefaultSeoTitle.prompt;
                    break;
                case "generate-content-chatgpt.meta_keyword":
                    data['role_default'] = messDefaultSeoKeyWords.role;
                    data['mess_default'] = messDefaultSeoKeyWords.prompt;
                    break;
                case "generate-content-chatgpt.meta_description":
                    data['role_default'] = messDefaultSeoDescription.role;
                    data['mess_default'] = messDefaultSeoDescription.prompt;
                    break;
                default:
                    data['role_default'] = messDefaultProduct.role;
                    data['mess_default'] = messDefaultProduct.prompt;
            }

            var selectorUseAll = document.getElementById("all-attr");
            var isUseAllAttr = window.lastUseAllAttr ?? false;
            selectorUseAll.addEventListener('change',  (e)=> {
                var isChecked = selectorUseAll.checked;
                listVariables.attr('disabled', isChecked);

                var messPrompt = replaceMess(listVariables, promptChatGPT.val(), isChecked);
                promptChatGPT.val(messPrompt);
            });
            selectorUseAll.dispatchEvent(new Event('change'));
            var defaultVariables = 'name';
            /* Set default variables */
            if (window.lastVariables && window.lastVariables.length > 0) {
                listVariables.val(window.lastVariables);
            } else {
                listVariables.val(defaultVariables);
            }
            /* Set default prompt */
            promptChatGPT.val(window.lastMessage[data['id']] ?? '');
            roleChatGPT.val(window.lastRole[data['id']] ?? data['role_default']);

            $(".reload-icon").click(function () {
                roleChatGPT.val(data['role_default']);
            });

            listVariables.change(function () {
                window.lastVariables = $(this).val();
                var messPrompt = replaceMess($(this), promptChatGPT.val(), selectorUseAll.checked);
                promptChatGPT.val(messPrompt);
            });
            var modalChatGPT = {
                type: 'popup',
                modalClass: 'chatgpt-modal-popup',
                responsive: true,
                clickableOverlay: true,
                buttons: [
                    {
                        text: $.mage.__('Get Default Prompt'),
                        class: 'btn-get-mess-default',
                        click: function () {
                            var messPrompt = replaceMess(listVariables, data['mess_default'], selectorUseAll.checked);
                            promptChatGPT.val(messPrompt);
                        }
                    },
                    {
                        text: $.mage.__('Send to ChatGPT'),
                        class: 'action-primary btn-send-prompt',
                        click: function () {
                            var request = [];
                            if (!promptChatGPT.val()) {
                                $('.btn-get-mess-default').click();
                            }

                            data['system_role'] = roleChatGPT.val();
                            data['search'] = promptChatGPT.val();
                            data['attributes'] = window.lastVariables;
                            data['all_attribute'] = selectorUseAll.checked;
                            request['url'] = data['url'];
                            request['form_key'] = data['form_key'];

                            this.closeModal();

                            utils.submit({
                                url: data['url'],
                                data: data
                            });

                            if (!data['search'] || !request['url'] || !request['form_key']) {
                                var message = $.mage.__('Error call API ChatGPT!');
                                selectorAlert.text(message);
                                selectorAlert.css("color", "#00CC33");
                                selectorAlert.insertAfter(element).show();

                                return '';
                            }
                        }
                    }
                ]
            };
            modal(modalChatGPT, $('.modal-chat_gpt'));
            $('.modal-chat_gpt').modal('openModal');

            function replaceMess(ele, defaultMess = '', isUseAllAtrr = false) {
                var title,
                    value,
                    result = '',
                    variable = [];

                if (!isUseAllAtrr) {
                    variable = $(ele).val();
                } else {
                    $(ele).find('option').each(function(kiz) {
                        variable.push($(this).val());
                    });
                }
                variable.forEach(function setAttributesForSelector(selector) {
                    var optionSelector = $(ele).children("option[value='" + selector + "']");
                    value = optionSelector.attr('data-value');
                    if (value) {
                        title = optionSelector.attr('data-title');
                        result += title + ';';
                    }
                });


                defaultMess = defaultMess ? defaultMess : promptChatGPT.val();
                return defaultMess.replace(/{{[\s\S]*?}}/g, "{{" + result + "}}");
            }
        },

        /**
         * Retrieves action object associated with a specified index.
         *
         * @param {String} actionIndex - Actions' identifier.
         * @param {Array} actions - Action objects.
         * @returns {Object} Action object.
         */
        getAction: function (actionIndex, actions) {
            var currentActions = actions || this.actions(),
                result = false;

            _.find(currentActions, function (action) {
                if (action.type === actionIndex) {
                    result = action;

                    return true;
                }

                if (action.actions) {
                    result = this.getAction(actionIndex, action.actions);

                    return result;
                }
            }, this);

            return result;
        },

        /**
         * Recursive hide all sub folders in given array.
         *
         * @param {Array} actions - Action objects.
         * @returns {Massactions} Chainable.
         */
        hideSubmenus: function (actions) {
            var currentActions = actions || this.actions();

            _.each(currentActions, function (action) {
                if (action.visible && action.visible()) {
                    action.visible(false);
                }

                if (action.actions) {
                    this.hideSubmenus(action.actions);
                }
            }, this);

            return this;
        }
    });
});
