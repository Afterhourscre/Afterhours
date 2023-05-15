/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
define([
    'underscore',
    'Magento_Ui/js/dynamic-rows/dynamic-rows',
    'Mageside_MultipleCustomForms/js/components/form/strategy'
], function (_, dynamicRows, strategy) {
    'use strict';

    return dynamicRows.extend(strategy).extend({

        defaults: {
            modules: {
                editModal: '',
                editForm: ''
            }
        },

        /**
         * @param {Object} data - Response data object.
         * @returns {Object}
         */
        setRecord: function (data) {
            if (data.record.id) {
                var obj = data.record;

                var indexRecord = _.findIndex(this.recordData(), function (record, index) {
                    if (record.id === obj.id) {
                        return true;
                    }
                });

                if (indexRecord >= 0) {
                    this.source.set(this.dataScope + '.' + this.index + '.' + indexRecord, obj);
                } else {
                    if (obj.hasOwnProperty('position') && obj.position == 0) {
                        this.setMaxPosition();
                        obj.position = this.maxPosition;
                    }
                    this.source.set(this.dataScope + '.' + this.index + '.' + this.recordData().length, obj);
                }

                this.reload();
            }
        },

        processingEditRecord: function (index, recordId) {
            if (recordId) {
                this.editForm().destroyInserted();
                this.editForm().render({record_id: recordId, store: this.source.data.form.store_id || '0'});
                this.editModal().openModal();
            }
        },

        processingDeleteRecord: function (index, recordId) {
            this.deleteRecord(index, recordId);

            if (this.getChildItems().length <= 0 && this.pages() !== 1) {
                this.pages(this.pages() - 1);
                this.currentPage(this.pages());
            }
        },

        deleteRecord: function (index, recordId) {
            var recordInstance,
                lastRecord,
                recordsData,
                childs;

            if (this.deleteProperty) {
                recordInstance = _.find(this.elems(), function (elem) {
                    return elem.index === index;
                });
                recordInstance.destroy();
                this.elems([]);
                this._updateCollection();
                this.removeMaxPosition();
                this.recordData()[recordInstance.index][this.deleteProperty] = this.deleteValue;
                this.recordData.valueHasMutated();
                childs = this.getChildItems();

                if (childs.length > this.elems().length) {
                    this.addChild(false, childs[childs.length - 1][this.identificationProperty], false);
                }
            } else {
                this.update = true;

                if (~~this.currentPage() === this.pages()) {
                    lastRecord =
                        _.findWhere(this.elems(), {
                            index: this.startIndex + this.getChildItems().length - 1
                        }) ||
                        _.findWhere(this.elems(), {
                            index: (this.startIndex + this.getChildItems().length - 1).toString()
                        });

                    lastRecord.destroy();
                }

                this.removeMaxPosition();
                recordsData = this._getDataByProp(recordId);
                this._updateData(recordsData);
                this.update = false;
            }

            if (this.pages() < ~~this.currentPage()) {
                this.currentPage(this.pages());
            }

            this._sort();
        }
    });
});
