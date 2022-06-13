'use strict';

/**
 * The list of environment variables that are necessary to run function
 *
 * NEBOOK_ENDPOINT
 * NEBOOK_BOOKSTORE_ID
 * NEBOOK_USER
 * NEBOOK_PASSWORD
 *
 * NEBOOK_TENDER_ID
 * NEBOOK_TENDER_ACCOUNT_NUMBER
 *
 * NEBOOK_BILLING_ADDRESS_1
 * NEBOOK_BILLING_ADDRESS_2
 * NEBOOK_BILLING_ADDRESS_CITY
 * NEBOOK_BILLING_COUNTRY
 * NEBOOK_BILLING_PHONE
 * NEBOOK_BILLING_STATE
 * NEBOOK_BILLING_ZIP
 * NEBOOK_BILLING_FIRST_NAME
 * NEBOOK_BILLING_LAST_NAME
 */

const SubmitCartProcessor = require('./processors/submitCartProcessor');

const submitCart = function (event, context, callback) {
    let processor;

    if (!event.shopperId.match(/^[a-f0-9]{32}$/)) {
        callback({
            error: 'Invalid Shopper ID',
            shopperId: event.shopperId,
        });
        return;
    }

    processor = new SubmitCartProcessor(callback);
    processor.process({shopperId: event.shopperId});
};

exports.submitCart = submitCart;
