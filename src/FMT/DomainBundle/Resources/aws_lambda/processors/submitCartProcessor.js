'use strict';

const NebookApiClient = require('../components/nebookApiClient'),

    nebookTenderId = process.env.NEBOOK_TENDER_ID,
    nebookTenderAccountNumber = process.env.NEBOOK_TENDER_ACCOUNT_NUMBER,

    nebookBillingAddress1 = process.env.NEBOOK_BILLING_ADDRESS_1,
    nebookBillingAddress2 = process.env.NEBOOK_BILLING_ADDRESS_2,
    nebookBillingCity = process.env.NEBOOK_BILLING_ADDRESS_CITY,
    nebookBillingCountry = process.env.NEBOOK_BILLING_COUNTRY,
    nebookBillingPhone = process.env.NEBOOK_BILLING_PHONE,
    nebookBillingState = process.env.NEBOOK_BILLING_STATE,
    nebookBillingZip = process.env.NEBOOK_BILLING_ZIP,
    nebookBillingFirstName = process.env.NEBOOK_BILLING_FIRST_NAME,
    nebookBillingLastName = process.env.NEBOOK_BILLING_LAST_NAME,

    maxCallAttempts = 3;

class SubmitCartProcessor {

    constructor(callback) {
        const self = this;

        self.nebookClient = new NebookApiClient();
        // Current number of tries to request the API method
        self.attemptsCount = 0;
        self.callback = callback;
    }

    process(args) {
        const self = this;

        self.checkoutAddPayment(args.shopperId, SubmitCartProcessor.getPaymentInfo());
    }

    checkoutAddPayment(shopperId, paymentInfo) {
        const self = this;
        let errorMessage;

        self.attemptsCount++;
        self.nebookClient
            .checkoutAddPayment(shopperId, paymentInfo)
            .then(function (response) {
                if (response['IsSuccess'] !== true) {
                    if (self.attemptsCount < maxCallAttempts) {
                        // recursively call the API
                        self.checkoutAddPayment(shopperId, paymentInfo);

                        return;
                    }

                    errorMessage = SubmitCartProcessor.getTooManyAttemptsMessage('checkoutAddPayment', self.attemptsCount);
                    console.error(errorMessage);
                    self.returnError(errorMessage, response);

                    return;
                }

                self.attemptsCount = 0;
                self.checkoutSubmitOrder(shopperId);
            })
            .catch(function (error) {
                if (self.attemptsCount < maxCallAttempts) {
                    self.checkoutAddPayment(shopperId, paymentInfo);

                    return;
                }

                errorMessage = SubmitCartProcessor.getTooManyAttemptsMessage('checkoutAddPayment', self.attemptsCount);
                console.error(errorMessage);
                self.returnError(errorMessage, error);
            });
    }

    checkoutSubmitOrder(shopperId) {
        const self = this;
        let errorMessage,
            successMessage;

        self.attemptsCount++;
        self.nebookClient
            .checkoutSubmitOrder(shopperId)
            .then(function (response) {
                if (response['IsSuccess'] !== true) {
                    if (self.attemptsCount < maxCallAttempts) {
                        self.checkoutSubmitOrder(shopperId);

                        return;
                    }

                    errorMessage = SubmitCartProcessor.getTooManyAttemptsMessage('checkoutSubmitOrder', self.attemptsCount);
                    console.error(errorMessage);
                    self.returnError(errorMessage, response);

                    return;
                }

                successMessage = SubmitCartProcessor.getSuccessCheckoutMessage(shopperId);
                console.log(successMessage);
                self.returnSuccess(successMessage, response);
            })
            .catch(function (error) {
                if (response['IsSuccess'] !== true) {
                    if (self.attemptsCount < maxCallAttempts) {
                        self.checkoutSubmitOrder(shopperId);

                        return;
                    }
                }

                errorMessage = SubmitCartProcessor.getTooManyAttemptsMessage('checkoutSubmitOrder', self.attemptsCount);
                console.error(errorMessage);
                self.returnError(errorMessage, error);
            });
    }

    static getPaymentInfo() {
        let mainTender,
            billingAddress;

        mainTender = {
            TenderId: nebookTenderId,
            AccountNumber: nebookTenderAccountNumber
        };

        billingAddress = {
            Address1: nebookBillingAddress1,
            Address2: nebookBillingAddress2,
            City: nebookBillingCity,
            Country: nebookBillingCountry,
            Phone: nebookBillingPhone,
            State: nebookBillingState,
            Zip: nebookBillingZip,
            FirstName: nebookBillingFirstName,
            LastName: nebookBillingLastName,
        };

        return {
            BillingAddress: billingAddress,
            MainTender: mainTender,
        };
    }

    returnError(message, object) {
        const self = this;

        self.callback({
            message: message,
            object: object
        });
    }

    returnSuccess(message, object) {
        const self = this;

        self.callback(
            null,
            {
                message: message,
                object: object
            }
        );
    }

    static getTooManyAttemptsMessage(method, number) {
        return 'Call of [Nebook API].' + method + ' failed ' + number + ' times. Abort.'
    }

    static getSuccessCheckoutMessage(shopperId) {
        return 'Successful checkout for shopper ID#' + shopperId;
    }
}

module.exports = SubmitCartProcessor;
