import org.apache.xmlrpc.client.XmlRpcClientConfigImpl;
import org.apache.xmlrpc.client.XmlRpcClient;
import org.apache.xmlrpc.XmlRpcException;

import java.net.URL;
import java.net.MalformedURLException;
import java.util.List;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.Map;

/**
 * Examples were created using the apache xmlrpc implementation:
 * <BR>
 * http://ws.apache.org/xmlrpc/
 * <BR>
 * This example will run three api commands:
 * <p/>
 * <ol>
 * <li>Add contact to database</li>
 * <li>Add contact to group</li>
 * <li>List all contacts in a group</li>
 * </ol>
 * <p/>
 * The examples have been written using java.util.List instead of java.lang.Object[]
 * <p/>
 * Examples should be compatible with jdk1.4
 * <p/>
 * Infusion Software<BR>
 * Date: May 2, 2006<BR>
 * Time: 2:36:09 PM<BR>
 *
 * @author eric
 */
public class API {
    /**

     */
    public static void main(String[] args) throws MalformedURLException, XmlRpcException {

        //Sets up the java client, including the api url
        XmlRpcClientConfigImpl config = new XmlRpcClientConfigImpl();
        config.setServerURL(new URL("https://marty.infusionsoft.com:443/api/xmlrpc"));
        XmlRpcClient client = new XmlRpcClient();
        client.setConfig(config);

        //The secure encryption key
        String key = "6ae189d497cd486b9db53793ccf98646";

        /*************************************************
         *                                               *
         ADD CONTACT TO DATABASE
         *                                               *
         *************************************************/
        List parameters = new ArrayList();
        Map contactData = new HashMap();
        contactData.put("FirstName", "Java John");
        contactData.put("LastName", "Doe");
        contactData.put("Email", "john@doe.com");

        parameters.add(key); //The secure key
        parameters.add("Contact"); //The table we will be adding to
        parameters.add(contactData); //The data to be added

        //Make the call
        Integer contactId = (Integer) client.execute("DataService.add", parameters);
        System.out.println("Contact added was " + contactId);

        /*************************************************
         *                                               *
         ADD CONTACT TO GROUP
         *                                               *
         *************************************************/
        int groupId = 97; //The group we will be adding to
        List parameters2 = new ArrayList();
        parameters2.add(key); //Secure key
        parameters2.add(contactId); //Id of the contact we just added
        parameters2.add(groupId); //Id of the group we want to add to


        Boolean success = (Boolean) client.execute("ContactService.addToGroup", parameters2);
        System.out.println("Added to group: " + success);

        /*************************************************
         *                                               *
         LIST ALL CONTACTS IN GROUP
         *                                               *
         *************************************************/
        List fields = new ArrayList(); //What fields we will be selecting
        fields.add("ContactGroup");
        fields.add("ContactId");

        List parameters3 = new ArrayList();
        parameters3.add(key); //Secure key
        parameters3.add("ContactGroupAssign");  //What table we are looking in
        parameters3.add(new Integer(50)); //How many records to return
        parameters3.add(new Integer(0)); //Which page of results to display
        parameters3.add("GroupId"); //The field we are querying on
        parameters3.add(new Integer(groupId)); //THe data to query on
        parameters3.add(fields); //what fields to select on return

        //Make call - the result is an array of structs
        Object[] contacts = (Object[]) client.execute("DataService.findByField", parameters3);

        //Loop through results
        for (int i = 0; i < contacts.length; i++) {
            //Each item in the array is a struct
            Map contact = (Map) contacts[i];
            System.out.println("Contact " + contact.get("ContactId") + " was found in group " +
                    contact.get("ContactGroup"));

        }

    }
}
