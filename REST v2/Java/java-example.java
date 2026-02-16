import java.net.URI;
import java.net.http.HttpClient;
import java.net.http.HttpRequest;
import java.net.http.HttpResponse;

public class KeapExample {

    private static final String API_KEY = "ACESS_TOKEN";
    private static final String BASE_URL = "https://api.infusionsoft.com/crm/rest/v2";

    private final HttpClient client = HttpClient.newHttpClient();

    public static void main(String[] args) throws Exception {
        var keap = new KeapExample();
        keap.createContact();
        keap.listContacts();
    }

    void createContact() throws Exception {
        var payload = """
                {
                    "family_name": "John",
                    "given_name": "Doe",
                    "email_addresses": [
                        { "email": "johndoe@yopmail.com", "field": "EMAIL1" }
                    ]
                }
                """;

        var request = HttpRequest.newBuilder()
                .uri(URI.create(BASE_URL + "/contacts"))
                .header("Content-Type", "application/json")
                .header("Authorization", "Bearer " + API_KEY)
                .POST(HttpRequest.BodyPublishers.ofString(payload))
                .build();

        var response = client.send(request, HttpResponse.BodyHandlers.ofString());
        System.out.println("Created contact: " + response.body());
    }

    void listContacts() throws Exception {
        var request = HttpRequest.newBuilder()
                .uri(URI.create(BASE_URL + "/contacts?page_size=10"))
                .header("Authorization", "Bearer " + API_KEY)
                .GET()
                .build();

        var response = client.send(request, HttpResponse.BodyHandlers.ofString());
        System.out.println("Contacts: " + response.body());
    }
}