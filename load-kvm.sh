#!/bin/bash
# ============================================================
# Load custom-error-map KVM entries into APIGEE X / HYBRID
# via the management API. Error JSON is defined inline (readable),
# stringified with jq, and posted as the KVM entry value.
#
# Prereqs: gcloud (authenticated), curl, jq
#
# Usage:
#   export APIGEE_ORG="neosalpha-apigee"
#   export APIGEE_ENV="sandbox"
#   bash load-kvm.sh
# ============================================================

set -euo pipefail

ORG="${APIGEE_ORG:?Set APIGEE_ORG}"
ENV="${APIGEE_ENV:?Set APIGEE_ENV}"
KVM_NAME="custom-error-map"
PROXY="error-handling-test"

API="https://apigee.googleapis.com/v1/organizations/$ORG/environments/$ENV/keyvaluemaps"
TOKEN="$(gcloud auth print-access-token)"

echo "Org=$ORG  Env=$ENV  KVM=$KVM_NAME"

# Create the KVM (ignore if it already exists)
curl -s -o /dev/null -w "Create KVM -> HTTP %{http_code}\n" \
  -X POST "$API" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d "{\"name\":\"$KVM_NAME\",\"encrypted\":true}" < /dev/null || true

# ---------------------------------------------------------------
# upsert <key-suffix> <error-json>
#   - stringifies the error JSON as the KVM value
#   - deletes any existing entry, then creates a fresh one
# ---------------------------------------------------------------
upsert() {
  local suffix="$1"
  local errjson="$2"
  local key="AMZ_${PROXY}_${suffix}"

  # jq -Rs is not needed; pass the error json as an argument and let jq stringify it
  local payload
  payload="$(jq -n --arg n "$key" --argjson v "$errjson" '{name:$n, value:($v|tostring)}')"

  curl -s -o /dev/null -X DELETE "$API/$KVM_NAME/entries/$key" \
    -H "Authorization: Bearer $TOKEN" < /dev/null || true

  local code
  code="$(printf '%s' "$payload" | curl -s -o /dev/null -w "%{http_code}" \
    -X POST "$API/$KVM_NAME/entries" \
    -H "Authorization: Bearer $TOKEN" \
    -H "Content-Type: application/json" \
    --data-binary @-)"

  echo "  $key -> HTTP $code"
}

echo "Loading entries..."

# ================= PLATFORM ERRORS =================

upsert "401" '{"Code":"401 Unauthorized","Id":"AMZ-401-001","Message":"Authentication failed","Errors":[{"ErrorCode":"INVALID_API_KEY","Message":"The provided API key is missing or invalid","Path":"/queryparam/apikey","Url":"https://developer.barclays.com/errors/authentication"}]}'

upsert "JWKS" '{"Code":"401 Unauthorized","Id":"AMZ-401-002","Message":"Authentication failed","Errors":[{"ErrorCode":"JWKS_UNAVAILABLE","Message":"The JWKS endpoint could not be reached to verify the token signature","Path":"/headers/Authorization","Url":"https://developer.barclays.com/errors/authentication"}]}'

upsert "JWS" '{"Code":"401 Unauthorized","Id":"AMZ-401-003","Message":"Authentication failed","Errors":[{"ErrorCode":"JWS_VERIFICATION_FAILED","Message":"The JWS signature could not be verified","Path":"/headers/Authorization","Url":"https://developer.barclays.com/errors/authentication"}]}'

upsert "403" '{"Code":"403 Forbidden","Id":"AMZ-403-001","Message":"Access denied","Errors":[{"ErrorCode":"INSUFFICIENT_SCOPE","Message":"Client does not have required permissions","Path":"/api/resource","Url":"https://developer.barclays.com/errors/authorization"}]}'

upsert "404" '{"Code":"404 Not Found","Id":"AMZ-404-001","Message":"Requested resource not found","Errors":[{"ErrorCode":"RESOURCE_NOT_FOUND","Message":"No API path found that matches the request","Path":"/request/path","Url":"https://developer.barclays.com/errors/not-found"}]}'

upsert "400" '{"Code":"400 Bad Request","Id":"AMZ-400-001","Message":"Invalid request payload or parameters","Errors":[{"ErrorCode":"INVALID_INPUT","Message":"Request failed OAS validation","Path":"/request","Url":"https://developer.barclays.com/errors/invalid-input"}]}'

upsert "500" '{"Code":"500 Internal Server Error","Id":"AMZ-500-001","Message":"Unexpected server error","Errors":[{"ErrorCode":"INTERNAL_ERROR","Message":"An unknown error occurred at the gateway","Path":"/apigee/gateway","Url":"https://developer.barclays.com/errors/internal"}]}'

# ================= BACKEND ERRORS =================

upsert "BE_400_400" '{"Code":"400 Bad Request","Id":"AMZ-BE-400","Message":"Backend rejected the request","Errors":[{"ErrorCode":"BACKEND_BAD_REQUEST","Message":"The backend service returned a 400 Bad Request","Path":"/target/endpoint","Url":"https://developer.barclays.com/errors/bad-request"}]}'

upsert "BE_401_500" '{"Code":"500 Internal Server Error","Id":"AMZ-BE-401","Message":"Unexpected server error","Errors":[{"ErrorCode":"BACKEND_AUTH_ERROR","Message":"The backend rejected the gateway credentials","Path":"/target/endpoint","Url":"https://developer.barclays.com/errors/internal"}]}'

upsert "BE_404_500" '{"Code":"500 Internal Server Error","Id":"AMZ-BE-404","Message":"Unexpected server error","Errors":[{"ErrorCode":"BACKEND_NOT_FOUND","Message":"The backend resource was not found","Path":"/target/endpoint","Url":"https://developer.barclays.com/errors/internal"}]}'

upsert "BE_405_500" '{"Code":"500 Internal Server Error","Id":"AMZ-BE-405","Message":"Unexpected server error","Errors":[{"ErrorCode":"BACKEND_METHOD_ERROR","Message":"The backend does not support the method","Path":"/target/endpoint","Url":"https://developer.barclays.com/errors/internal"}]}'

upsert "BE_406_500" '{"Code":"500 Internal Server Error","Id":"AMZ-BE-406","Message":"Unexpected server error","Errors":[{"ErrorCode":"BACKEND_NOT_ACCEPTABLE","Message":"The backend could not produce an acceptable response","Path":"/target/endpoint","Url":"https://developer.barclays.com/errors/internal"}]}'

upsert "BE_412_500" '{"Code":"500 Internal Server Error","Id":"AMZ-BE-412","Message":"Unexpected server error","Errors":[{"ErrorCode":"BACKEND_PRECONDITION_FAILED","Message":"A backend precondition failed","Path":"/target/endpoint","Url":"https://developer.barclays.com/errors/internal"}]}'

upsert "BE_415_500" '{"Code":"500 Internal Server Error","Id":"AMZ-BE-415","Message":"Unexpected server error","Errors":[{"ErrorCode":"BACKEND_UNSUPPORTED_MEDIA","Message":"The backend rejected the media type","Path":"/target/endpoint","Url":"https://developer.barclays.com/errors/internal"}]}'

upsert "BE_500_500" '{"Code":"500 Internal Server Error","Id":"AMZ-BE-500","Message":"Unexpected server error","Errors":[{"ErrorCode":"BACKEND_INTERNAL_ERROR","Message":"The backend service returned an internal error","Path":"/target/endpoint","Url":"https://developer.barclays.com/errors/internal"}]}'

upsert "BE_502_500" '{"Code":"500 Internal Server Error","Id":"AMZ-BE-502","Message":"Unexpected server error","Errors":[{"ErrorCode":"BACKEND_BAD_GATEWAY","Message":"The backend service returned a bad gateway error","Path":"/target/endpoint","Url":"https://developer.barclays.com/errors/internal"}]}'

upsert "BE_500" '{"Code":"500 Internal Server Error","Id":"AMZ-BE-GENERIC","Message":"Unexpected server error","Errors":[{"ErrorCode":"BACKEND_ERROR","Message":"The backend service returned an unexpected error","Path":"/target/endpoint","Url":"https://developer.barclays.com/errors/internal"}]}'

echo ""
echo "Done. Verifying _500 entry..."
curl -s -X GET "$API/$KVM_NAME/entries/AMZ_${PROXY}_500" \
  -H "Authorization: Bearer $TOKEN" < /dev/null
echo ""
