/**
 * ResolveErrorKey.js  (per-API custom error mapping)
 *
 * Handles BOTH:
 *   A. Apigee PLATFORM errors  (policy/gateway faults)
 *   B. BACKEND errors          (target service returned an error status)
 *
 * Builds:
 *   custom.kvm.key       - KVM lookup key (see formats below)
 *   custom.kvm.status    - HTTP status code to return to the client
 *   custom.kvm.resolved  - always true (a generic 500 key always exists in the KVM)
 *
 * A separate KeyValueMapOperations policy uses custom.kvm.key to fetch the full
 * JSON error body from the KVM; an AssignMessage returns it verbatim with
 * custom.kvm.status as the HTTP status.
 *
 * ---------------------------------------------------------------------------
 * KEY FORMATS (underscore-separated everywhere):
 *   Platform:  <PREFIX>_<apiproxy.name>_<SUFFIX>
 *   Backend:   <PREFIX>_<apiproxy.name>_BE_<backendStatus>_<mappedStatus>
 *   Generic:   <PREFIX>_<apiproxy.name>_500        (lookback fallback)
 * ---------------------------------------------------------------------------
 *
 * RESOLUTION ORDER (first match wins):
 *   1. BACKEND ERROR   — fault.name = "ErrorResponseCode"
 *        backend status -> mapped status (table below)
 *   2. PLATFORM FAULT NAME CASES (fixed status)
 *        InvalidApiKey          -> 401  key: 401
 *        RF-NoJWKS              -> 401  key: JWKS
 *        JWS verification faults-> 401  key: JWS
 *   3. PLATFORM STATUS 403      — any error with HTTP 403 -> 403  key: 403
 *   4. OAS VALIDATION
 *        resource / path not found -> 404  key: 404
 *        any other OAS error       -> 400  key: 400
 *   5. GENERIC FALLBACK (lookback) -> 500  key: 500
 *
 * ---------------------------------------------------------------------------
 * TO CONFIGURE FOR A NEW API: change PREFIX and adjust the cases/tables below.
 */

// ===========================================================================
// CONFIG — change per API
// ===========================================================================

var PREFIX = "AMZ";

// Backend status -> mapped (client-facing) status.
// Any backend status NOT listed here uses the fixed fallback key BE_500 (status 500).
var BACKEND_STATUS_MAP = {
    400: 400,
    401: 500,
    404: 500,
    405: 500,
    406: 500,
    412: 500,
    415: 500,
    500: 500,
    502: 500
};

// ===========================================================================
// Read context variables
// ===========================================================================

var proxyName       = context.getVariable("apiproxy.name")     || "unknown";
var rawFaultName    = context.getVariable("fault.name")        || "";
var errorMessage    = context.getVariable("error.message")     || "";
var errorStatusCode = parseInt(context.getVariable("error.status.code") || "500", 10);

// OAS-specific policy variables (adjust "OAS-Validate" to your OAS policy name)
var oasFaultCause = context.getVariable("OASValidation.OAS-Validate.fault.cause") || "";
var oasFailed     = context.getVariable("OASValidation.OAS-Validate.failed");

// ===========================================================================
// Helpers
// ===========================================================================

function setKey(suffix, status) {
    context.setVariable("custom.kvm.key", PREFIX + "_" + proxyName + "_" + suffix);
    context.setVariable("custom.kvm.status", String(status));
    context.setVariable("custom.kvm.resolved", true);
}

// Resolve real fault name (RaiseFault hides the policy name inside error.message)
var faultName = rawFaultName;
if (rawFaultName === "RaiseFault" && errorMessage.indexOf("Fault name : ") !== -1) {
    faultName = errorMessage.split("Fault name : ")[1].trim();
}

// OAS detection
var isOAS = (oasFailed === true || oasFailed === "true" || oasFaultCause !== "");

var handled = false;

// ===========================================================================
// 1. BACKEND ERROR  (fault.name = "ErrorResponseCode")
//    Key: BE_<backendStatus>_<mappedStatus>
//    Return status = mappedStatus
// ===========================================================================

if (faultName === "ErrorResponseCode") {
    var backendStatus = errorStatusCode;
    if (BACKEND_STATUS_MAP.hasOwnProperty(backendStatus)) {
        var mappedStatus = BACKEND_STATUS_MAP[backendStatus];
        setKey("BE_" + backendStatus + "_" + mappedStatus, mappedStatus);
    } else {
        // Backend status not in the table -> fixed backend fallback key
        setKey("BE_500", 500);
    }
    handled = true;
}

// ===========================================================================
// 2. PLATFORM FAULT NAME CASES (fixed status codes)
// ===========================================================================

if (!handled) {
    switch (faultName) {

        // --- Invalid API key -> always 401 (fixed) ---
        case "InvalidApiKey":
            setKey("401", 401);
            handled = true;
            break;

        // --- NoJWKS RaiseFault -> JWKS key, 401 ---
        case "RF-NoJWKS":
            setKey("JWKS", 401);
            handled = true;
            break;

        // --- JWS verification failures (VerifyJWS policy) -> JWS key, 401 ---
        case "RF-JWSFail":
        case "JWSVerificationFailed":
        case "InvalidJWSSignature":
        case "InvalidJWSFormat":
        case "InvalidJWSVersion":
        case "MissingJWS":
        case "FailedToResolveVariable":
            setKey("JWS", 401);
            handled = true;
            break;

        default:
            break;
    }
}

// ===========================================================================
// 3. PLATFORM STATUS 403 OVERRIDE (only 403 is status-driven)
// ===========================================================================

if (!handled && errorStatusCode === 403) {
    setKey("403", 403);
    handled = true;
}

// ===========================================================================
// 4. OAS VALIDATION
//    resource / path not found -> 404 key, 404 status
//    any other OAS error       -> 400 key, 400 status
// ===========================================================================

if (!handled && isOAS) {
    var oasText = (oasFaultCause || errorMessage);

    if (oasText.indexOf("No API path found") !== -1) {
        setKey("404", 404);
    } else {
        setKey("400", 400);
    }
    handled = true;
}

// ===========================================================================
// 5. GENERIC FALLBACK (lookback) -> 500 key, 500 status
//    This key always exists in the KVM, so custom.kvm.resolved stays true.
// ===========================================================================

if (!handled) {
    setKey("500", 500);
}
