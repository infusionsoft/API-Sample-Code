const API_KEY = "KeapAK-bd1f50f94ea2d78b664cfcc1f9057eedfa5803d2ee5a12f16e";
const BASE_URL = "https://api.infusionsoft.com/crm/rest/v2";

interface EmailAddress {
  email: string;
  field: "EMAIL1" | "EMAIL2" | "EMAIL3";
}

interface Contact {
  id?: number;
  family_name: string;
  given_name: string;
  email_addresses: EmailAddress[];
}

interface ContactList {
  contacts: Contact[];
  count: number;
  next_page_token?: string;
}

const headers: Record<string, string> = {
  "Content-Type": "application/json",
  Authorization: `Bearer ${API_KEY}`,
};

async function createContact(): Promise<Contact> {
  const payload: Contact = {
    family_name: "John",
    given_name: "Doe",
    email_addresses: [{ email: "johndoe@yopmail.com", field: "EMAIL1" }],
  };

  const res = await fetch(`${BASE_URL}/contacts`, {
    method: "POST",
    headers,
    body: JSON.stringify(payload),
  });

  const data: Contact = await res.json();
  console.log("Created contact:", data);
  return data;
}

async function listContacts(): Promise<ContactList> {
  const res = await fetch(`${BASE_URL}/contacts?page_size=10`, { headers });
  const data: ContactList = await res.json();
  console.log("Contacts:", data);
  return data;
}

async function main() {
  await createContact();
  await listContacts();
}

main().catch(console.error);