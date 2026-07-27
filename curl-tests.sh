#!/bin/bash
# ============================================================
# cURL commands to test the Attendee Search Mock Proxy
# Update BASE_URL to your Apigee host before running
# ============================================================

BASE_URL="https://sandbox.neosalpha.in/attendee-search"

echo "============================================================"
echo "FLOW 1: Test Request (XML to JSON)"
echo "============================================================"

echo ""
echo "--- Test 1: XML to JSON - Full Request ---"
echo ""

curl -s -X POST "${BASE_URL}/test-request" \
  -H "Content-Type: application/xml" \
  -d '<?xml version="1.0" encoding="UTF-8"?>
<AttendeeSearchRequest>
    <Attendee>
        <AttendeeTypeCode>GEPOC</AttendeeTypeCode>
        <FirstName/>
        <MiddleInitial/>
        <LastName/>
        <Suffix/>
        <Title/>
        <Company/>
        <ExternalID/>
        <OwnerLoginID>E01920510@barclays.concur.com</OwnerLoginID>
        <Custom1/>
        <Custom2/>
        <Custom3/>
        <Custom4/>
        <Custom5/>
        <Custom6/>
        <Custom7/>
        <Custom8>GNE-054896</Custom8>
        <Custom9/>
        <Custom10/>
        <Custom11/>
        <Custom12/>
        <Custom13/>
        <Custom14/>
        <Custom15/>
        <Custom16/>
        <Custom17/>
        <Custom18/>
        <Custom19/>
        <Custom20/>
        <Custom21/>
        <Custom22/>
        <Custom23/>
        <Custom24/>
        <Custom25/>
        <MaximumNumberRecords>500</MaximumNumberRecords>
    </Attendee>
</AttendeeSearchRequest>' | python -m json.tool

echo ""
echo ""
echo "--- Test 2: XML to JSON - With Name Fields ---"
echo ""

curl -s -X POST "${BASE_URL}/test-request" \
  -H "Content-Type: application/xml" \
  -d '<?xml version="1.0" encoding="UTF-8"?>
<AttendeeSearchRequest>
    <Attendee>
        <AttendeeTypeCode>GEPOC</AttendeeTypeCode>
        <FirstName>John</FirstName>
        <MiddleInitial>M</MiddleInitial>
        <LastName>Doe</LastName>
        <Suffix/>
        <Title>Manager</Title>
        <Company>Acme Corp</Company>
        <ExternalID>EXT-001</ExternalID>
        <OwnerLoginID>G01522172@barclays.concur.com</OwnerLoginID>
        <Custom1/>
        <Custom2/>
        <Custom3/>
        <Custom4/>
        <Custom5/>
        <Custom6/>
        <Custom7/>
        <Custom8>GNE-048629</Custom8>
        <Custom9/>
        <Custom10/>
        <Custom11/>
        <Custom12/>
        <Custom13/>
        <Custom14/>
        <Custom15/>
        <Custom16/>
        <Custom17/>
        <Custom18/>
        <Custom19/>
        <Custom20/>
        <Custom21/>
        <Custom22/>
        <Custom23/>
        <Custom24/>
        <Custom25/>
        <MaximumNumberRecords>100</MaximumNumberRecords>
    </Attendee>
</AttendeeSearchRequest>' | python -m json.tool

echo ""
echo ""
echo "============================================================"
echo "FLOW 2: Test Response (JSON to XML)"
echo "============================================================"

echo ""
echo "--- Test 3: JSON to XML - Multiple Attendees ---"
echo ""

curl -s -X POST "${BASE_URL}/test-response" \
  -H "Content-Type: application/json" \
  -H "Accept: application/xml" \
  -d '{
    "data": {
        "id": "GNE-046629",
        "type": "attendees",
        "attributes": {
            "AttendeeSearchResponse": {
                "Attendee": [
                    {
                        "AttendeeTypeCode": "GEPOC PreApproved",
                        "Company": "",
                        "Custom1": "G01522172",
                        "Custom2": "",
                        "Custom3": "",
                        "Custom4": "",
                        "Custom5": "",
                        "Custom6": "",
                        "Custom7": "",
                        "Custom8": "GNE-046629",
                        "Custom9": "0",
                        "Custom10": "",
                        "Custom11": "",
                        "Custom12": "",
                        "Custom13": "",
                        "Custom14": "",
                        "Custom15": "",
                        "Custom16": "",
                        "Custom17": "",
                        "Custom18": "",
                        "Custom19": "",
                        "Custom20": "",
                        "Custom21": "",
                        "Custom22": "",
                        "Custom23": "",
                        "Custom24": "",
                        "Custom25": "",
                        "ExternalID": "GNE-046629_1",
                        "FirstName": "Bongiovanni",
                        "LastName": "Hanick",
                        "MiddleInitial": "",
                        "Suffix": "",
                        "Title": ""
                    },
                    {
                        "AttendeeTypeCode": "GEPOC PreApproved",
                        "Company": "",
                        "Custom1": "G01178817",
                        "Custom2": "",
                        "Custom3": "",
                        "Custom4": "",
                        "Custom5": "",
                        "Custom6": "",
                        "Custom7": "",
                        "Custom8": "GNE-046629",
                        "Custom9": "0",
                        "Custom10": "",
                        "Custom11": "",
                        "Custom12": "",
                        "Custom13": "",
                        "Custom14": "",
                        "Custom15": "",
                        "Custom16": "",
                        "Custom17": "",
                        "Custom18": "",
                        "Custom19": "",
                        "Custom20": "",
                        "Custom21": "",
                        "Custom22": "",
                        "Custom23": "",
                        "Custom24": "",
                        "Custom25": "",
                        "ExternalID": "GNE-046629_2",
                        "FirstName": "Bobbins",
                        "LastName": "Risko",
                        "MiddleInitial": "",
                        "Suffix": "",
                        "Title": ""
                    },
                    {
                        "AttendeeTypeCode": "GEPOC PreApproved",
                        "Company": "Championship Inc",
                        "Custom1": "",
                        "Custom2": "40000203",
                        "Custom3": "AR",
                        "Custom4": "",
                        "Custom5": "N",
                        "Custom6": "christopher.sur@pwc.com",
                        "Custom7": "",
                        "Custom8": "GNE-046629",
                        "Custom9": "0",
                        "Custom10": "",
                        "Custom11": "",
                        "Custom12": "",
                        "Custom13": "",
                        "Custom14": "",
                        "Custom15": "",
                        "Custom16": "",
                        "Custom17": "",
                        "Custom18": "",
                        "Custom19": "",
                        "Custom20": "",
                        "Custom21": "",
                        "Custom22": "",
                        "Custom23": "",
                        "Custom24": "",
                        "Custom25": "",
                        "ExternalID": "GNE-046629_3",
                        "FirstName": "Christopher",
                        "LastName": "Sur",
                        "MiddleInitial": "",
                        "Suffix": "",
                        "Title": ""
                    }
                ]
            }
        }
    }
}'

echo ""
echo ""
echo "--- Test 4: JSON to XML - Single Attendee ---"
echo ""

curl -s -X POST "${BASE_URL}/test-response" \
  -H "Content-Type: application/json" \
  -H "Accept: application/xml" \
  -d '{
    "data": {
        "id": "GNE-054896",
        "type": "attendees",
        "attributes": {
            "AttendeeSearchResponse": {
                "Attendee": [
                    {
                        "AttendeeTypeCode": "GEPOC PreApproved",
                        "Company": "Test Corp",
                        "Custom1": "E01920510",
                        "Custom2": "",
                        "Custom3": "",
                        "Custom4": "",
                        "Custom5": "",
                        "Custom6": "",
                        "Custom7": "",
                        "Custom8": "GNE-054896",
                        "Custom9": "1",
                        "Custom10": "",
                        "Custom11": "",
                        "Custom12": "",
                        "Custom13": "",
                        "Custom14": "",
                        "Custom15": "",
                        "Custom16": "",
                        "Custom17": "",
                        "Custom18": "",
                        "Custom19": "",
                        "Custom20": "",
                        "Custom21": "",
                        "Custom22": "",
                        "Custom23": "",
                        "Custom24": "",
                        "Custom25": "",
                        "ExternalID": "GNE-054896_1",
                        "FirstName": "Jane",
                        "LastName": "Smith",
                        "MiddleInitial": "A",
                        "Suffix": "",
                        "Title": "Director"
                    }
                ]
            }
        }
    }
}'

echo ""
echo ""
echo "============================================================"
echo "All tests complete."
echo "============================================================"
