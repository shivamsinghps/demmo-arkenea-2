'use strict';

const rp = require('../lib/node_modules/request-promise');

const nebookEndpoint = process.env.NEBOOK_ENDPOINT,
    nebookBookstoreId = process.env.NEBOOK_BOOKSTORE_ID,
    nebookUser = process.env.NEBOOK_USER,
    nebookPassword = process.env.NEBOOK_PASSWORD;

/**
 * API calls return promises
 */
class NebookApiClient {
    constructor() {
        const self = this;

        self.endpoint = nebookEndpoint;

        // Set Auth headers for all requests
        self.baseRequest = rp.defaults({
            headers: {
                Password: nebookPassword,
                Username: nebookUser,
                BookstoreId: nebookBookstoreId
            }
        });
    }

    prepareUri(method, params) {
        const self = this;
        let url = self.endpoint + '/' + method,
            paramStrings = [],
            paramName;

        if (params) {
            if (typeof params === 'string' || typeof params === 'number') {
                // Append param as path element
                url += ('/' + params);
            } else {
                // Add params as GET variables
                for (paramName in params) {
                    if (params.hasOwnProperty(paramName)) {
                        paramStrings.push(paramName + '=' + params[paramName]);
                    }
                }

                url += ('?' + paramStrings.join('&'));
            }
        }

        return url;
    }

    /**
     * @see https://webprism.nbcservices.com/v3.13/WebPrismService.svc/json/help/operations/CheckoutAddPayment
     */
    checkoutAddPayment(shopperId, paymentInfo) {
        const self = this;
        let options,
            uri;

        uri = self.prepareUri('CheckoutAddPayment', shopperId);
        options = {
            uri: uri,
            json: true,
            body: paymentInfo
        };

        return self.baseRequest.post(options);
    }

    /**
     * @see https://webprism.nbcservices.com/v3.13/WebPrismService.svc/json/help/operations/CheckoutSubmitOrder
     */
    checkoutSubmitOrder(shopperId) {
        const self = this;
        let options,
            uri;

        uri = self.prepareUri('CheckoutSubmitOrder', shopperId);
        options = {uri: uri};

        return self.baseRequest.post(options);
    }
}

module.exports = NebookApiClient;
