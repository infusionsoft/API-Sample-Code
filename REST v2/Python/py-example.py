import requests

API_KEY = "ACCESS_TOKEN"
BASE_URL = "https://api.infusionsoft.com/crm/rest/v2"

headers = {
    "Content-Type": "application/json",
    "Authorization": f"Bearer {API_KEY}",
}


def create_contact():
    payload = {
        "family_name": "John",
        "given_name": "Doe",
        "email_addresses": [{"email": "johndoe@yopmail.com", "field": "EMAIL1"}],
    }
    res = requests.post(f"{BASE_URL}/contacts", json=payload, headers=headers)
    data = res.json()
    print("Created contact:", data)
    return data


def list_contacts():
    res = requests.get(f"{BASE_URL}/contacts?page_size=10", headers=headers)
    data = res.json()
    print("Contacts:", data)
    return data


if __name__ == "__main__":
    create_contact()
    list_contacts()