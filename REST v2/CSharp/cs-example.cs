using System.Net.Http.Headers;
using System.Text;
using System.Text.Json;

const string apiKey = "ACESS_TOKEN";
const string baseUrl = "https://api.infusionsoft.com/crm/rest/v2";

using var client = new HttpClient();
client.DefaultRequestHeaders.Authorization = new AuthenticationHeaderValue("Bearer", apiKey);

await CreateContact();
await ListContacts();

async Task CreateContact()
{
    var payload = new
    {
        family_name = "John",
        given_name = "Doe",
        email_addresses = new[]
        {
            new { email = "johndoe@yopmail.com", field = "EMAIL1" }
        }
    };

    var content = new StringContent(
        JsonSerializer.Serialize(payload),
        Encoding.UTF8,
        "application/json"
    );

    var response = await client.PostAsync($"{baseUrl}/contacts", content);
    var body = await response.Content.ReadAsStringAsync();
    Console.WriteLine($"Created contact: {body}");
}

async Task ListContacts()
{
    var response = await client.GetAsync($"{baseUrl}/contacts?page_size=10");
    var body = await response.Content.ReadAsStringAsync();
    Console.WriteLine($"Contacts: {body}");
}