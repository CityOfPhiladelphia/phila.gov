/*! skinny.js v0.1.0 | Copyright 2013 Vistaprint | vistaprint.github.io/SkinnyJS/LICENSE
http://vistaprint.github.io/SkinnyJS/download-builder.html?modules=jquery.delimitedString,jquery.queryString*/

module.exports = (function ($) {

    // Takes a plain javascript object (key value pairs), and encodes it as a string
    // using the specified delimiters and encoders
    $.encodeDelimitedString = function (data, itemDelimiter, pairDelimiter, keyEncoder, valueEncoder) {
        if (!data) {
            return "";
        }

        keyEncoder = keyEncoder || function (s) {
            return s;
        };
        valueEncoder = valueEncoder || keyEncoder;

        var sb = [];

        for (var key in data) {
            if (data.hasOwnProperty(key)) {
                sb.push(keyEncoder(key) + pairDelimiter + valueEncoder(data[key]));
            }
        }

        return sb.join(itemDelimiter);
    };

    // Takes an encoded string, and parses it into a plain javascript object (key value pairs)
    // using the specified delimiters and decoders
    $.parseDelimitedString = function (delimitedString, itemDelimiter, pairDelimiter, keyDecoder, valueDecoder) {
        keyDecoder = keyDecoder || function (s) {
            return s;
        };
        valueDecoder = valueDecoder || keyDecoder;

        var ret = {};

        if (delimitedString) {
            var pairs = delimitedString.split(itemDelimiter);
            var len = pairs.length;
            for (var i = 0; i < len; i++) {
                var pair = pairs[i];

                if (pair.length > 0) {
                    var delimIndex = pair.indexOf(pairDelimiter);
                    var key, value;

                    if (delimIndex > 0 && delimIndex <= pair.length - 1) {
                        key = pair.substring(0, delimIndex);
                        value = pair.substring(delimIndex + 1);
                    } else {
                        key = pair;
                    }

                    ret[keyDecoder(key)] = valueDecoder(value);
                }
            }
        }

        return ret;
    };

})(jQuery);
;/// <reference path="jquery.delimitedString.js" />

(function ($) {
    var PLUS_RE = /\+/gi;

    var urlDecode = function (s) {
        // Specifically treat null/undefined as empty string
        if (s == null) {
            return "";
        }

        // Replace plus with space- jQuery.param() explicitly encodes them,
        // and decodeURIComponent explicitly does not.
        return decodeURIComponent(s.replace(PLUS_RE, " "));
    };

    // Given a querystring (as a string), deserializes it to a javascript object.
    $.deparam = function (queryString) {
        if (typeof queryString != "string") {
            throw new Error("$.deparam() expects a string for 'queryString' argument.");
        }

        // Remove "?", which starts querystrings
        if (queryString && queryString.charAt(0) == "?") {
            queryString = queryString.substring(1, queryString.length);
        }

        return $.parseDelimitedString(queryString, "&", "=", urlDecode);
    };

    // Alias
    $.parseQueryString = $.deparam;

    // Gets the querystring from the current document.location as a javascript object.
    $.currentQueryString = function () {
        return $.deparam(window.location.search);
    };

    // Given a url (pathname) and an object representing a querystring, constructs a full URL
    $.appendQueryString = function (url, parsedQueryString) {
        var qs = $.param(parsedQueryString);
        if (qs.length > 0) {
            qs = "?" + qs;
        }

        return url + qs;
    };

})(jQuery);
