const yargs = require('yargs');
const axios = require('axios');
const axiosRetry = require('axios-retry');

axiosRetry(axios, { retries: 3 });

const API_BASE = `https://api.infusionsoft.com/crm/rest`;

const argv = yargs
    .option('apikey', {
        alias: 'a',
        description: 'A Personal Access Token from your Keap application instance',
        type: 'string',
    })
    .help()
    .argv;

if(!argv.apikey) {
    throw new Error('Must specify APIKey');
}

const REQUEST_HEADERS = {
    'X-Keap-API-Key': argv.apikey,
    'Content-Type': 'application/json'
}

async function getContacts() {
    const axiosResponse =
        await axios.get(
            `${API_BASE}/v2/contacts`,
            {
                headers: REQUEST_HEADERS,
                params: {}
            });

    const contactRecords = axiosResponse.data.contacts;

    for(const contact of contactRecords) {
        console.log(contact.id, contact.given_name, contact.family_name)
    }
}

getContacts()
.catch(error => {
    console.error(error);
})


