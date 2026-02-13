const API_KEY = "ACCESS_TOKEN";
const BASE_URL = "https://api.infusionsoft.com/crm/rest/v2";

const headers = {
  "Content-Type": "application/json",
  Authorization: `Bearer ${API_KEY}`,
};

async function createContact() {
  const res = await fetch(`${BASE_URL}/contacts`, {
    method: "POST",
    headers,
    body: JSON.stringify({
      family_name: "John",
      given_name: "Doe",
      email_addresses: [{ email: "johndoe@yopmail.com", field: "EMAIL1" }],
    }),
  });

  const data = await res.json();
  console.log("Created contact:", data);
  return data;
}

async function listContacts() {
  const res = await fetch(`${BASE_URL}/contacts?page_size=10`, { headers });
  const data = await res.json();
  console.log("Contacts:", data);
  return data;
}

async function main() {
  await createContact();
  await listContacts();
}

main().catch(console.error);