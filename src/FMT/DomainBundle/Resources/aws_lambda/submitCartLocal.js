'use strict';

/**
 * To run function locally a JSON argument with shopperId must be passed
 * Existing test value of shopper ID = 3bb863c275cf4ee5a80e017ef914c2d3
 */

const submitCart = require('./submitCart').submitCart,
    event = JSON.parse(process.argv[2]);

let callback = function (error, result) {
    let response = JSON.stringify({error: error, result: result});

    // Logging here works as @return: writes into output anr PHP reads the message
    console.log(response);

    return [error, result]
};

submitCart(event, null, callback);
